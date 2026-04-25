<?php
// =========================================
// PROXY SO – VALIDASI STORE + EXPIRED TIME
// =========================================

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");

date_default_timezone_set("Asia/Jakarta");

$storeId = strtoupper(trim($_GET['storeId'] ?? ''));
$dateSo  = trim($_GET['dateSo'] ?? '');

if ($storeId === '' || $dateSo === '') {
    http_response_code(400);
    echo "Parameter storeId atau dateSo kosong";
    exit;
}

/* =========================
   LOAD data.json
========================= */
$dataFile = __DIR__ . "/data.json";

if (!file_exists($dataFile)) {
    http_response_code(500);
    echo "File data.json tidak ditemukan";
    exit;
}

$json = json_decode(file_get_contents($dataFile), true);
if (!isset($json['stores']) || !is_array($json['stores'])) {
    http_response_code(500);
    echo "Struktur data.json tidak valid";
    exit;
}

/* =========================
   VALIDASI TOKO
========================= */
if (!isset($json['stores'][$storeId])) {
    http_response_code(403);
    echo "Toko $storeId tidak terdaftar";
    exit;
}

$store = $json['stores'][$storeId];

if (empty($store['expired_at'])) {
    http_response_code(403);
    echo "Toko $storeId tidak memiliki masa berlaku";
    exit;
}

if (strtotime($store['expired_at']) < time()) {
    http_response_code(403);
    echo "Akses toko $storeId sudah expired";
    exit;
}

/* =========================
   PANGGIL API ALFASTORE
========================= */
$apiUrl = "https://app.alfastore.co.id/prd/api/rpt/laporan_so/prosentase_so"
        . "?storeId=" . rawurlencode($storeId)
        . "&dateSo=" . rawurlencode($dateSo);

$ch = curl_init($apiUrl);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HEADER => false,
    CURLOPT_ENCODING => '',
    CURLOPT_HTTPHEADER => [
        "User-Agent: Mozilla/5.0",
        "Accept: text/html,*/*"
    ]
]);

$response = curl_exec($ch);

if ($response === false) {
    http_response_code(500);
    echo "Curl error: " . curl_error($ch);
    curl_close($ch);
    exit;
}

$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

http_response_code($httpCode);

/* =========================
   KIRIM HTML MENTAH
========================= */
echo $response;
exit;