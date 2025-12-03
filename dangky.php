<?php
include 'includes/config.php';
include 'classes/dbadmin.php';

$db = new dbadmin();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $ten_dang_nhap = trim($_POST["ten_dang_nhap"] ?? "");
    $mat_khau = $_POST["mat_khau"] ?? "";
    $confirm = $_POST["confirm_mat_khau"] ?? "";
    $email = trim($_POST["email"] ?? "");
    $so_dien_thoai = trim($_POST["so_dien_thoai"] ?? "");

    if ($mat_khau !== $confirm) {
        echo "<script>alert('Mật khẩu nhập lại không khớp!');</script>";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>alert('Email không hợp lệ!');</script>";
    } elseif (empty($ten_dang_nhap) || empty($mat_khau)) {
        echo "<script>alert('Vui lòng điền đầy đủ thông tin.');</script>";
    } else {
        $mat_khau_hash = password_hash($mat_khau, PASSWORD_DEFAULT);

        $data = [
            "ten_dang_nhap" => $ten_dang_nhap,
            "mat_khau" => $mat_khau_hash,  
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
                <input type="text" name="ten_dang_nhap" placeholder="Tên đăng nhập" required>
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="mat_khau" placeholder="Mật khẩu" required>
                <input type="password" name="confirm_mat_khau" placeholder="Nhập lại mật khẩu" required>
                <button type="submit">Đăng ký</button>
            </form>
            <p>Đã có tài khoản? <a href="dangnhap.php">Đăng nhập ngay</a></p>
        </div>
    </div>
</body>

</html>
