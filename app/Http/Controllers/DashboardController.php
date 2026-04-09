<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\AttendanceLog;
use App\Services\QrCodeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    protected QrCodeService $qrService;

    public function __construct(QrCodeService $qrService)
    {
        $this->qrService = $qrService;
    }

    /**
     * Admin dashboard overview.
     */
    public function index()
    {
        $stats = [
            'total_events' => Event::count(),
            'active_events' => Event::where('is_active', true)->count(),
            'today_attendance' => AttendanceLog::whereDate('created_at', today())
                ->where('status', 'hadir')->count(),
            'total_attendance' => AttendanceLog::where('status', 'hadir')->count(),
            'fraud_alerts' => AttendanceLog::where('fraud_score', '>', 0.5)->count(),
        ];

        $recentLogs = AttendanceLog::with('event')
            ->latest()
            ->take(10)
            ->get();

        $events = Event::withCount(['attendanceLogs as hadir_count' => function ($q) {
            $q->where('status', 'hadir');
        }])->latest()->take(5)->get();

        return view('admin.dashboard', compact('stats', 'recentLogs', 'events'));
    }

    /**
     * List all events.
     */
    public function events()
    {
        $events = Event::withCount(['attendanceLogs as hadir_count' => function ($q) {
            $q->where('status', 'hadir');
        }])->latest()->paginate(10);

        return view('admin.events.index', compact('events'));
    }

    /**
     * Show create event form.
     */
    public function createEvent()
    {
        return view('admin.events.create');
    }

    /**
     * Store new event.
     */
    public function storeEvent(Request $request)
    {
        $request->validate([
            'nama_event' => 'required|string|max:255',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'radius' => 'required|integer|min:10|max:500',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'google_form_url' => 'nullable|url',
        ]);

        $event = Event::create([
            'nama_event' => $request->nama_event,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'radius' => $request->radius,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'is_active' => true,
            'google_form_url' => $request->google_form_url,
            'created_by' => Auth::id(),
        ]);

        return redirect()->route('admin.events.show', $event)
            ->with('success', 'Event berhasil dibuat!');
    }

    /**
     * Show edit event form.
     */
    public function editEvent(Event $event)
    {
        return view('admin.events.edit', compact('event'));
    }

    /**
     * Update event.
     */
    public function updateEvent(Request $request, Event $event)
    {
        $request->validate([
            'nama_event' => 'required|string|max:255',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'radius' => 'required|integer|min:10|max:500',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
        ]);

        $event->update([
            'nama_event' => $request->nama_event,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'radius' => $request->radius,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
        ]);

        return redirect()->route('admin.events.index')
            ->with('success', 'Event berhasil diperbarui!');
    }

    /**
     * Destroy event.
     */
    public function destroyEvent(Event $event)
    {
        $event->delete();
        return redirect()->route('admin.events.index')
            ->with('success', 'Event berhasil dihapus!');
    }

    /**
     * Show event detail with QR and attendance.
     */
    public function showEvent(Event $event)
    {
        $event->loadCount(['attendanceLogs as hadir_count' => function ($q) {
            $q->where('status', 'hadir');
        }]);

        $logs = $event->attendanceLogs()
            ->latest()
            ->paginate(20);

        // Get or create QR token
        $qrToken = $this->qrService->getOrCreateToken($event->id);
        $qrUrl = url('/absen/' . $qrToken->token);
        $qrImage = $this->qrService->generateQrImage($qrUrl, 350);

        return view('admin.events.show', compact('event', 'logs', 'qrToken', 'qrUrl', 'qrImage'));
    }

    /**
     * AJAX: Refresh QR for event.
     */
    public function refreshQr(Event $event)
    {
        $qrToken = $this->qrService->refreshToken($event->id);
        $qrUrl = url('/absen/' . $qrToken->token);
        $qrImage = $this->qrService->generateQrImage($qrUrl, 350);

        return response()->json([
            'token' => $qrToken->token,
            'url' => $qrUrl,
            'qr_svg' => $qrImage,
            'expired_at' => $qrToken->expired_at->toIso8601String(),
            'refresh_seconds' => config('absensi.qr_refresh_seconds'),
        ]);
    }

    /**
     * View attendance logs for an event.
     */
    public function attendanceLogs(Event $event)
    {
        $logs = $event->attendanceLogs()
            ->latest()
            ->paginate(30);

        return view('admin.attendance.index', compact('event', 'logs'));
    }

    /**
     * Export attendance to CSV.
     */
    public function exportCsv(Event $event)
    {
        $logs = $event->attendanceLogs()->where('status', 'hadir')->get();

        $filename = 'absensi_' . str_replace(' ', '_', $event->nama_event) . '_' . now()->format('Ymd') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($logs, $event) {
            $file = fopen('php://output', 'w');

            // BOM for Excel UTF-8
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($file, ['No', 'Nama', 'NPM', 'Waktu', 'Jarak (m)', 'Status', 'IP Address', 'Fraud Score']);

            foreach ($logs as $i => $log) {
                fputcsv($file, [
                    $i + 1,
                    $log->nama,
                    $log->npm,
                    $log->created_at->format('d/m/Y H:i:s'),
                    $log->distance,
                    $log->status,
                    $log->ip_address,
                    $log->fraud_score,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Toggle event active status.
     */
    public function toggleEvent(Event $event)
    {
        $event->update(['is_active' => !$event->is_active]);

        return response()->json([
            'is_active' => $event->is_active,
            'message' => $event->is_active ? 'Event diaktifkan.' : 'Event dinonaktifkan.',
        ]);
    }
}
