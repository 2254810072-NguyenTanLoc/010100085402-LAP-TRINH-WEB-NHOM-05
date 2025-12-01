<?php
include 'includes/config.php';
include 'classes/dbadmin.php';

$db = new dbadmin();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $ten_dang_nhap = $_POST["ten_dang_nhap"];
    $mat_khau = $_POST["mat_khau"];
    $email = $_POST["email"];
    $so_dien_thoai = $_POST["so_dien_thoai"];

    $data = [
        "ten_dang_nhap" => $ten_dang_nhap,
        "mat_khau" => $mat_khau,
        "email" => $email,
        "so_dien_thoai" => $so_dien_thoai,
        "vai_tro" => "user"
    ];

    if ($db->insert("khachhang", $data)) {
        echo "<script>alert('Đăng ký thành công!'); window.location='dangnhap.php';</script>";
    } else {
        echo "<script>alert('Đăng ký thất bại!');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Đăng ký tài khoản</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <?php include 'includes/navbar.php'; ?>
    <div class="auth-container">
        <div class="auth-box">
            <h2>Đăng ký</h2>
            <form method="POST" action="">
                <input type="text" name="username" placeholder="Tên đăng nhập" required>
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="password" placeholder="Mật khẩu" required>
                <input type="password" name="confirm_password" placeholder="Nhập lại mật khẩu" required>
                <button type="submit" name="dangky">Đăng ký</button>
            </form>
            <p>Đã có tài khoản? <a href="dangnhap.php">Đăng nhập ngay</a></p>
        </div>
    </div>
</body>

</html>