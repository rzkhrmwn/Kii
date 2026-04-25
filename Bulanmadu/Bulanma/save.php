<?php
session_start();
date_default_timezone_set("Asia/Jakarta");

if (!isset($_SESSION['admin'])) {
    exit("Akses ditolak");
}

$dataFile = __DIR__ . "/data.json";

if (!file_exists($dataFile)) {
    file_put_contents($dataFile, json_encode(["stores"=>[]], JSON_PRETTY_PRINT));
}

$data = json_decode(file_get_contents($dataFile), true);

/* ===== HAPUS ===== */
if (isset($_GET['hapus'])) {
    $hapusId = strtoupper(trim($_GET['hapus']));

    if (isset($data['stores'][$hapusId])) {
        unset($data['stores'][$hapusId]);
        file_put_contents($dataFile, json_encode($data, JSON_PRETTY_PRINT), LOCK_EX);
    }

    header("Location: admin.php");
    exit;
}

/* ===== VALIDASI POST ===== */
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    exit("Invalid request");
}

$storeId = strtoupper(trim($_POST['storeId'] ?? ''));
$nama = trim($_POST['nama'] ?? '');
$expiredInput = $_POST['expired_at'] ?? '';

/* ===== VALIDASI DATA ===== */
if ($storeId === '' || $nama === '' || $expiredInput === '') {
    exit("Data tidak lengkap");
}

/* ===== FORMAT WAKTU ===== */
$expired = str_replace("T", " ", $expiredInput) . ":00";

/* ===== SIMPAN ===== */
$data['stores'][$storeId] = [
    "nama" => $nama,
    "expired_at" => $expired
];

/* ===== SIMPAN FILE (AMAN) ===== */
file_put_contents($dataFile, json_encode($data, JSON_PRETTY_PRINT), LOCK_EX);

header("Location: admin.php");
exit;
