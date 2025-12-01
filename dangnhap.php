<?php
session_start();
include 'includes/config.php';
include 'classes/dbadmin.php';

$db = new dbadmin();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $ten_dang_nhap = $_POST["ten_dang_nhap"];
    $mat_khau = $_POST["mat_khau"];

    if ($db->checkAdmin($ten_dang_nhap, $mat_khau)) {
        $_SESSION['admin'] = $ten_dang_nhap;
        header("Location: admin/index.php");
        exit();
    } elseif ($db->checkUser($ten_dang_nhap, $mat_khau)) {
        $sql = "SELECT ma_khach_hang FROM khachhang WHERE ten_dang_nhap = '$ten_dang_nhap'";
        $result = $conn->query($sql);
        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $_SESSION['ma_khach_hang'] = $row['ma_khach_hang'];
        }

        $_SESSION['user'] = $ten_dang_nhap;

        if (isset($_SESSION['redirect_url'])) {
            $url = $_SESSION['redirect_url'];
            unset($_SESSION['redirect_url']);
            header("Location: $url");
        } else {
            header("Location: index.php");
        }
        exit();
    } else {
        echo "<script>alert('Sai tên đăng nhập hoặc mật khẩu!');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Đăng nhập</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <?php include 'includes/navbar.php'; ?>
    <div class="auth-container">
        <div class="auth-box">
            <h2>Đăng nhập</h2>
            <form method="POST" action="">
                <input type="text" name="ten_dang_nhap" placeholder="Tên đăng nhập" required>
                <input type="password" name="mat_khau" placeholder="Mật khẩu" required>
                <button type="submit" name="dangnhap">Đăng nhập</button>
            </form>
            <p>Chưa có tài khoản? <a href="dangky.php">Đăng ký ngay</a></p>
        </div>
    </div>
</body>

</html>