<?php
$host = 'gateway01.ap-southeast-1.prod.aws.tidbcloud.com';
$port = 4000;
$user = '3HmEguLRggppLdM.root';
$db = 'test';

$passwords = [
    'blqsgmAz95Ebljkd',
    'blqsgmAz95EbIjkd',
    'blqsgmAz95Ebl1kd',
    'bIqsgmAz95Ebljkd',
    'b1qsgmAz95Ebljkd'
];

$options = [
    PDO::MYSQL_ATTR_SSL_CA => __DIR__ . '/cacert.pem',
    PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => true,
];

foreach ($passwords as $p) {
    try {
        $dsn = "mysql:host=$host;port=$port;dbname=$db";
        $pdo = new PDO($dsn, $user, $p, $options);
        echo "Connected successfully with password: $p\n";
        break;
    } catch (PDOException $e) {
        echo "Failed with $p - " . $e->getMessage() . "\n";
    }
}
