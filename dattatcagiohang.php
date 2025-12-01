<?php
session_start();
include 'includes/config.php';
include 'classes/dbadmin.php';

if (!isset($_SESSION['ma_khach_hang'])) {
    echo "<script>alert('Bạn cần đăng nhập để đặt phòng!'); window.location='dangnhap.php';</script>";
    exit();
}

$ma_khach_hang = $_SESSION['ma_khach_hang'];
$db = new dbadmin();
$rows = $db->layChiTietGioHangTheoKhach($ma_khach_hang);

$ds_phong = [];
$tong = 0;

if ($rows && $rows->num_rows > 0) {
    // Lấy tất cả chi tiết phòng và tính tổng tiền
    while ($r = $rows->fetch_assoc()) {
        $ds_phong[] = $r;
        $so_ngay = (strtotime($r['ngay_di']) - strtotime($r['ngay_den'])) / (60*60*24);
        if ($so_ngay <= 0) $so_ngay = 1;
        $tong += $r['don_gia'] * $so_ngay;
    }

    // Lấy ngày đến sớm nhất và ngày đi muộn nhất
    $ngay_den = $ds_phong[0]['ngay_den'];
    $ngay_di  = $ds_phong[0]['ngay_di'];
    $i = 0;
    while ($i < count($ds_phong)) {
        if ($ds_phong[$i]['ngay_den'] < $ngay_den) $ngay_den = $ds_phong[$i]['ngay_den'];
        if ($ds_phong[$i]['ngay_di']  > $ngay_di)  $ngay_di  = $ds_phong[$i]['ngay_di'];
        $i++;
    }

    $ngay_dat = date('Y-m-d');

    // Tạo đơn đặt phòng cha với tổng tiền
    $sql = "INSERT INTO datphong(ma_khach_hang, ngay_dat, ngay_den, ngay_di, tong_tien, trang_thai)
            VALUES ('$ma_khach_hang', '$ngay_dat', '$ngay_den', '$ngay_di', '$tong', 'cho_xac_nhan')";
    $db->query($sql);
    $ma_dat_phong = $db->getLastInsertId();

    // Thêm chi tiết từng phòng
    $j = 0;
    while ($j < count($ds_phong)) {
        $ct = $ds_phong[$j];
        $ma_phong = $ct['ma_phong'];
        $gia = $ct['don_gia'];
        $ngay_den_ct = $ct['ngay_den'];
        $ngay_di_ct  = $ct['ngay_di'];
        $so_ngay = (strtotime($ngay_di_ct) - strtotime($ngay_den_ct)) / (60*60*24);
        if ($so_ngay <= 0) $so_ngay = 1;
        $thanh_tien = $gia * $so_ngay;

        $sql_ct = "INSERT INTO chitietdatphong(ma_dat_phong, ma_phong, ngay_den, ngay_di, don_gia, thanh_tien)
                   VALUES ('$ma_dat_phong', '$ma_phong', '$ngay_den_ct', '$ngay_di_ct', '$gia', '$thanh_tien')";
        $db->query($sql_ct);
        $j++;
    }

    // Xóa toàn bộ giỏ hàng
    $db->xoaGioHangTheoKhach($ma_khach_hang);

    echo "<script>alert('Đặt tất cả phòng thành công!'); window.location='lichsudatphong.php';</script>";
    exit();
} else {
    echo "<script>alert('Giỏ hàng trống!'); window.location='giohang.php';</script>";
    exit();
}
?>
