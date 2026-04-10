<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\AttendanceLog;
use App\Models\QrToken;
use App\Services\QrCodeService;
use App\Services\GeolocationService;
use Illuminate\Http\Request;

class AbsensiController extends Controller
{
    protected QrCodeService $qrService;
    protected GeolocationService $geoService;

    public function __construct(QrCodeService $qrService, GeolocationService $geoService)
    {
        $this->qrService = $qrService;
        $this->geoService = $geoService;
    }

    /**
     * GET /absen/{token} — Show attendance page after QR scan.
     */
    public function showForm(string $token)
    {
        $qrToken = $this->qrService->validateToken($token);

        if (!$qrToken) {
            return view('absensi.expired');
        }

        $event = $qrToken->event;

        if (!$event->is_active) {
            return view('absensi.expired', ['message' => 'Event tidak aktif.']);
        }

        if (!$event->isWithinTimeWindow()) {
            return view('absensi.expired', ['message' => 'Waktu absensi telah berakhir.']);
        }

        return view('absensi.scan', [
            'event' => $event,
            'token' => $token,
        ]);
    }

    /**
     * POST /validate-location — Validate user's GPS coordinates.
     */
    public function validateLocation(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'accuracy' => 'nullable|numeric',
            'altitude' => 'nullable|numeric',
            'speed' => 'nullable|numeric',
        ]);

        $qrToken = $this->qrService->validateToken($request->token);

        if (!$qrToken) {
            return response()->json([
                'valid' => false,
                'message' => 'QR Code sudah kadaluarsa. Silakan scan ulang.',
                'type' => 'expired',
            ], 410);
        }

        $event = $qrToken->event;

        $distance = $this->geoService->calculateHaversine(
            $request->latitude,
            $request->longitude,
            $event->latitude,
            $event->longitude
        );

        $fraudScore = $this->geoService->detectFakeGps(
            $request->latitude,
            $request->longitude,
            $request->accuracy,
            $request->altitude,
            $request->speed
        );

        $withinRadius = $distance <= $event->radius;

        return response()->json([
            'valid' => $withinRadius,
            'distance' => round($distance, 2),
            'radius' => $event->radius,
            'fraud_score' => $fraudScore,
            'event' => [
                'id' => $event->id,
                'nama' => $event->nama_event,
                'google_form_url' => $event->google_form_url,
            ],
            'message' => $withinRadius
                ? 'Lokasi valid! Silakan isi data absensi.'
                : 'Anda berada di luar radius absensi (' . round($distance) . 'm dari lokasi event).',
            'type' => $withinRadius ? 'success' : 'rejected',
        ]);
    }

    /**
     * POST /submit-attendance — Record attendance.
     */
    public function submitAttendance(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
            'nama' => 'required|string|max:255',
            'npm' => 'required|string|max:20',
            'universitas' => 'required|string|max:150',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $qrToken = $this->qrService->validateToken($request->token);

        if (!$qrToken) {
            return response()->json([
                'success' => false,
                'message' => 'QR Code sudah kadaluarsa.',
            ], 410);
        }

        $event = $qrToken->event;

        // Check duplicate
        $existing = AttendanceLog::where('event_id', $event->id)
            ->where('npm', $request->npm)
            ->where('status', 'hadir')
            ->first();

        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah melakukan absensi untuk event ini.',
                'type' => 'duplicate',
            ], 409);
        }

        $distance = $this->geoService->calculateHaversine(
            $request->latitude,
            $request->longitude,
            $event->latitude,
            $event->longitude
        );

        $fraudScore = $this->geoService->detectFakeGps(
            $request->latitude,
            $request->longitude,
            $request->accuracy,
            $request->altitude,
            $request->speed
        );

        $status = $distance <= $event->radius ? 'hadir' : 'ditolak';

        $log = AttendanceLog::create([
            'event_id' => $event->id,
            'nama' => $request->nama,
            'npm' => $request->npm,
            'universitas' => $request->universitas,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'distance' => $distance,
            'status' => $status,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'device_fingerprint' => $request->header('X-Device-Fingerprint'),
            'fraud_score' => $fraudScore,
        ]);

        if ($status === 'ditolak') {
            return response()->json([
                'success' => false,
                'message' => 'Lokasi Anda di luar radius yang ditentukan.',
                'type' => 'rejected',
            ], 403);
        }

        return response()->json([
            'success' => true,
            'message' => 'Absensi berhasil dicatat!',
            'data' => [
                'nama' => $log->nama,
                'npm' => $log->npm,
                'event' => $event->nama_event,
                'time' => $log->created_at->format('d M Y H:i:s'),
                'distance' => round($distance, 2) . 'm',
            ],
        ]);
    }
}
