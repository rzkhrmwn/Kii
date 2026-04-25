<?php  
session_start();  
date_default_timezone_set("Asia/Jakarta");  
  
$PASSWORD = "Watniuca123"; // GANTI PASSWORD ADMIN  
  
$dataFile = __DIR__ . "/data.json";  
if (!file_exists($dataFile)) {  
    file_put_contents($dataFile, json_encode(["stores"=>[]], JSON_PRETTY_PRINT));  
}  
  
$data = json_decode(file_get_contents($dataFile), true);  
  
/* ===== LOGIN ===== */  
if (isset($_POST['password'])) {  
    if ($_POST['password'] === $PASSWORD) {  
        $_SESSION['admin'] = true;  
        header("Location: admin.php");  
        exit;  
    } else {  
        $error = "Password salah";  
    }  
}  
  
/* ===== LOGOUT ===== */  
if (isset($_GET['logout'])) {  
    session_destroy();  
    header("Location: admin.php");  
    exit;  
}  
  
if (!isset($_SESSION['admin'])):  
?>  <!DOCTYPE html>  <html>  
<head>  
<meta charset="UTF-8">  
<title>Admin Login</title>  
<style>  
body{font-family:Arial;background:#111;color:#fff;display:flex;align-items:center;justify-content:center;height:100vh}  
form{background:#222;padding:20px;border-radius:10px;width:300px}  
input,button{width:100%;padding:10px;margin-top:10px}  
button{background:#0f9;border:none;font-weight:bold}  
</style>  
</head>  
<body>  
<form method="post">  
<h3>ADMIN LOGIN</h3>  
<?php if(isset($error)) echo "<p style='color:red'>$error</p>"; ?>  
<input type="password" name="password" placeholder="Password admin" required>  
<button>LOGIN</button>  
</form>  
</body>  
</html>  
<?php exit; endif; ?>  <!DOCTYPE html>  <html>  
<head>  
<meta charset="UTF-8">  
<title>Admin Panel Toko</title>  
<style>  
body{font-family:Arial;background:#0f2027;color:#fff;padding:20px}  
table{width:100%;border-collapse:collapse;margin-top:20px}  
th,td{border:1px solid #555;padding:8px;text-align:center}  
th{background:#203a43}  
input{padding:6px}  
button{padding:6px 10px}  
a{color:#0f9}  
</style>  
</head>  
<body>  <h2>ADMIN PANEL TOKO SO GRAND</h2>  
<a href="?logout=1">Logout</a>  <form method="post" action="save.php">  
<h3>Tambah / Edit Toko</h3>  
<input name="storeId" placeholder="Kode Toko" required>  
<input name="nama" placeholder="Nama Toko" required>  
<input type="datetime-local" name="expired_at" required>  
<button>SIMPAN</button>  
</form>  <table>  
<tr>  
<th>Kode</th>  
<th>Nama</th>  
<th>Expired</th>  
<th>Sisa Waktu</th>  
<th>Aksi</th>  
</tr>  <?php foreach ($data['stores'] as $k=>$v):  
$sisa = strtotime($v['expired_at']) - time();  
?>  <tr>  
<td><?= $k ?></td>  
<td><?= $v['nama'] ?></td>  
<td><?= $v['expired_at'] ?></td>  
<td><?= $sisa>0 ? gmdate("H:i:s",$sisa) : "HABIS" ?></td>  
<td>  
<a href="save.php?hapus=<?= $k ?>" onclick="return confirm('Hapus toko?')">Hapus</a>  
</td>  
</tr>  
<?php endforeach; ?>  </table>  
</body>  
</html>