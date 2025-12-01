<?php
session_start();
require 'classes/dbadmin.php';
$db = new dbadmin();

// Kiểm tra khách đã đăng nhập
if (!isset($_SESSION['ma_khach_hang'])) {
    echo "<script>alert('Vui lòng đăng nhập để quản lý giỏ hàng'); window.location='dangnhap.php';</script>";
    exit();
}

$ma_khach_hang = $_SESSION['ma_khach_hang'];
$ma_chi_tiet = isset($_GET['ma_chi_tiet']) ? (int)$_GET['ma_chi_tiet'] : 0;

if ($ma_chi_tiet <= 0) {
    header("Location: giohang.php");
    exit();
}

// Thực hiện xóa
$ok = $db->xoaChiTietGioHang($ma_chi_tiet, $ma_khach_hang);

// Kiểm tra số hàng bị ảnh hưởng
if ($ok && $db->getAffectedRows() > 0) {
    // Xóa thành công
    header("Location: giohang.php");
    exit();
} else if ($ok) {
    // Query chạy được nhưng không có hàng nào bị xóa (ID không tồn tại hoặc không phải của khách)
    echo "<script>alert('Mục này không tồn tại hoặc không thuộc bạn'); window.location='giohang.php';</script>";
    exit();
} else {
    // Lỗi query
    echo "<script>alert('Lỗi hệ thống, không thể xóa'); window.location='giohang.php';</script>";
    exit();
}
