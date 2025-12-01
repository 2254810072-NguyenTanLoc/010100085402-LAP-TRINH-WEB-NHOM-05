<?php
session_start();
include 'includes/config.php';
include 'classes/dbadmin.php';

if (!isset($_SESSION['ma_khach_hang'])) {
    echo "<script>alert('Bạn cần đăng nhập!'); window.location='dangnhap.php';</script>";
    exit();
}

if (isset($_GET['ma_dat_phong'])) {
    $ma_dat_phong = (int)$_GET['ma_dat_phong'];
    $db = new dbadmin();

    $db->huyDatPhong($ma_dat_phong);

    echo "<script>alert('Đã hủy đặt phòng thành công!'); window.location='lichsudatphong.php';</script>";
} else {
    echo "<script>alert('Không tìm thấy mã đặt phòng!'); window.location='lichsudatphong.php';</script>";
}
