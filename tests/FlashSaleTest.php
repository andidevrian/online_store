<?php

$url = 'http://localhost:8080/inventory-api/api/orders';

$mh = curl_multi_init();
$handles = [];

for ($i = 1; $i <= 100; $i++) {
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_POST => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json'
        ],
        CURLOPT_POSTFIELDS => json_encode([
            'product_id' => 1,
            'qty' => 1
        ]),
        CURLOPT_TIMEOUT => 30
    ]);

    curl_multi_add_handle($mh, $ch);
    $handles[] = $ch;
}

$running = null;

do {
    curl_multi_exec($mh, $running);
    if ($running) {
        curl_multi_select($mh);
    }
} while ($running > 0);
$success = 0;
$failed = 0;

foreach ($handles as $handle) {
    $httpCode = curl_getinfo($handle, CURLINFO_HTTP_CODE);
    $response = curl_multi_getcontent($handle);
    // $json = json_decode($response, true);
    $response = trim($response);
    // hapus BOM UTF-8 jika ada
    $response = preg_replace('/^\xEF\xBB\xBF/', '', $response);
    $json = json_decode($response, true);

    if (
        $httpCode >= 200 &&
        $httpCode < 300 &&
        !empty($json['success'])
    ) {
        $success++;
    } else {
        $failed++;
        echo PHP_EOL;
        echo "HTTP : {$httpCode}" . PHP_EOL;
        echo "RESP : {$response}" . PHP_EOL;
    }
    curl_multi_remove_handle($mh, $handle);
    curl_close($handle);
}

curl_multi_close($mh);

echo PHP_EOL;
echo "======================" . PHP_EOL;
echo "SUCCESS : {$success}" . PHP_EOL;
echo "FAILED  : {$failed}" . PHP_EOL;
echo "======================" . PHP_EOL;