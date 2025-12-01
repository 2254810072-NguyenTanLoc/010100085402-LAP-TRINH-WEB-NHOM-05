<?php
session_start();
require 'classes/dbadmin.php';
$db = new dbadmin();

if (!isset($_SESSION['ma_khach_hang'])) {
    echo "<script>alert('Vui lòng đăng nhập'); window.location='dangnhap.php';</script>";
    exit();
}

$ma_khach_hang = $_SESSION['ma_khach_hang'];
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mat_khau_cu = $_POST['mat_khau_cu'] ?? '';
    $mat_khau_moi = $_POST['mat_khau_moi'] ?? '';
    $mat_khau_moi2 = $_POST['mat_khau_moi2'] ?? '';

    if (!$mat_khau_cu || !$mat_khau_moi || !$mat_khau_moi2) {
        $error = "Vui lòng điền đầy đủ thông tin";
    } elseif ($mat_khau_moi !== $mat_khau_moi2) {
        $error = "Mật khẩu mới và xác nhận mật khẩu không trùng khớp";
    } elseif (!$db->checkMatKhauCu($ma_khach_hang, $mat_khau_cu)) {
        $error = "Mật khẩu cũ không đúng";
    } else {
        $ok = $db->doiMatKhau($ma_khach_hang, $mat_khau_moi);
        if ($ok) {
            echo "<script>alert('Đổi mật khẩu thành công'); window.location='index.php';</script>";
            exit();
        } else {
            $error = "Đổi mật khẩu thất bại, thử lại";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Đổi mật khẩu</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <?php include 'includes/navbar.php' ?>
    <div class="auth-container">
        <div class="auth-box">
            <h2>Đổi mật khẩu</h2>
            <?php if ($error) echo "<div class='error'>$error</div>"; ?>
            <form method="POST" action="">
                <label>Mật khẩu cũ:</label>
                <input type="password" name="mat_khau_cu" required>

                <label>Mật khẩu mới:</label>
                <input type="password" name="mat_khau_moi" required>

                <label>Xác nhận mật khẩu mới:</label>
                <input type="password" name="mat_khau_moi2" required>

                <button type="submit">Đổi mật khẩu</button>
            </form>
        </div>
    </div>
</body>

</html>