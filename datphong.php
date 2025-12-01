<?php
include 'includes/config.php';
session_start();

if (!isset($_SESSION['ma_khach_hang'])) {
    $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
    echo "<script>alert('Bạn cần đăng nhập để đặt phòng!'); window.location='dangnhap.php';</script>";
    exit();
}

if (isset($_GET['ma_phong'])) {
    $ma_phong = $_GET['ma_phong'];
    $ma_khach_hang = $_SESSION['ma_khach_hang'];

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $ngay_den = $_POST['ngay_den'];
        $ngay_di = $_POST['ngay_di'];
        $ngay_dat = date('Y-m-d'); 

        $sql_gia = "SELECT gia FROM phong WHERE ma_phong = '$ma_phong'";
        $result_gia = $conn->query($sql_gia);
        $gia = 0;
        if ($result_gia->num_rows > 0) {
            $row = $result_gia->fetch_assoc();
            $gia = $row['gia'];
        }

        $so_ngay = (strtotime($ngay_di) - strtotime($ngay_den)) / (60 * 60 * 24);
        if ($so_ngay <= 0) {
            echo "<script>alert('Ngày đi phải sau ngày đến!'); window.history.back();</script>";
            exit();
        }
        $tong_tien = $gia * $so_ngay;

        $sql_datphong = "INSERT INTO datphong (ma_khach_hang, ma_phong, ngay_dat, ngay_den, ngay_di, tong_tien, trang_thai) 
                         VALUES ('$ma_khach_hang', '$ma_phong', '$ngay_dat', '$ngay_den', '$ngay_di', '$tong_tien', 'cho_xac_nhan')";

        if ($conn->query($sql_datphong) === TRUE) {
            echo "<script>alert('Đặt phòng thành công! Chúng tôi sẽ liên hệ xác nhận.'); window.location='lichsudatphong.php';</script>";
        } else {
            echo "Lỗi: " . $conn->error;
        }
    }

    $sql_phong = "SELECT * FROM phong WHERE ma_phong = '$ma_phong'";
    $result_phong = $conn->query($sql_phong);
    if ($result_phong->num_rows > 0) {
        $phong = $result_phong->fetch_assoc();
    }
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đặt phòng</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <h2>Đặt phòng: <?php echo $phong['ten_phong']; ?></h2>
    <p>Giá: <?php echo number_format($phong['gia']); ?> VND / đêm</p>

    <form method="POST">
        <label>Ngày đến:</label><br>
        <input type="date" name="ngay_den" required><br><br>

        <label>Ngày đi:</label><br>
        <input type="date" name="ngay_di" required><br><br>

        <button type="submit">Xác nhận đặt phòng</button>
    </form>

    <p><a href="index.php">← Quay lại trang chủ</a></p>
</body>
</html>
<?php
} else {
    echo "<script>alert('Không tìm thấy phòng.'); window.location='index.php';</script>";
}
?>
