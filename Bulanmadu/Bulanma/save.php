<?php
session_start();
date_default_timezone_set("Asia/Jakarta");

if (!isset($_SESSION['admin'])) {
    exit("Akses ditolak");
}

$dataFile = __DIR__ . "/data.json";
$data = json_decode(file_get_contents($dataFile), true);

/* ===== HAPUS ===== */
if (isset($_GET['hapus'])) {
    unset($data['stores'][$_GET['hapus']]);
    file_put_contents($dataFile, json_encode($data, JSON_PRETTY_PRINT));
    header("Location: admin.php");
    exit;
}

/* ===== SIMPAN ===== */
$storeId = strtoupper(trim($_POST['storeId']));
$nama = trim($_POST['nama']);
$expired = str_replace("T"," ",$_POST['expired_at']) . ":00";

$data['stores'][$storeId] = [
    "nama" => $nama,
    "expired_at" => $expired
];

file_put_contents($dataFile, json_encode($data, JSON_PRETTY_PRINT));
header("Location: admin.php");
exit;