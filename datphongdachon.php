<?php
session_start();
require 'includes/config.php';
require 'classes/dbadmin.php';
$db = new dbadmin();

if (!isset($_SESSION['ma_khach_hang'])) {
    echo "<script>alert('Bạn cần đăng nhập để đặt phòng!'); window.location='dangnhap.php';</script>";
    exit();
}

$ma_khach_hang = intval($_SESSION['ma_khach_hang']);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['chon_phong'])) {
    $chon_phong = $_POST['chon_phong']; // array các ma_chi_tiet
    $ngay_dat = date('Y-m-d');

    /* 1) Lấy toàn bộ chi tiết giỏ hàng đã chọn */
    $ds_phong = $db->layChiTietNhieuCTGH($chon_phong, $ma_khach_hang);
    if (count($ds_phong) === 0) {
        echo "<script>alert('Dữ liệu không hợp lệ'); window.location='giohang.php';</script>";
        exit();
    }

    /* 2) Tính tổng tiền và xác định ngày đến/đi của đơn cha */
    $tong = 0;
    $ngay_den_don = $ds_phong[0]['ngay_den'];
    $ngay_di_don  = $ds_phong[0]['ngay_di'];
    $i = 0;
    $count = count($ds_phong);
    while ($i < $count) {
        $ct = $ds_phong[$i];

        // tính tổng tiền
        $so_ngay = (strtotime($ct['ngay_di']) - strtotime($ct['ngay_den'])) / 86400;
        if ($so_ngay <= 0) $so_ngay = 1;
        $tong += $ct['don_gia'] * $so_ngay;

        // tìm ngày đến sớm nhất / đi muộn nhất
        if ($ct['ngay_den'] < $ngay_den_don) $ngay_den_don = $ct['ngay_den'];
        if ($ct['ngay_di']  > $ngay_di_don)  $ngay_di_don  = $ct['ngay_di'];

        $i++;
    }

    /* 3) Tạo đơn cha */
    $sql = "INSERT INTO datphong(ma_khach_hang, ngay_dat, ngay_den, ngay_di, tong_tien, trang_thai)
            VALUES ('$ma_khach_hang', '$ngay_dat', '$ngay_den_don', '$ngay_di_don', '$tong', 'cho_xac_nhan')";
    $db->query($sql);
    $ma_dat_phong = $db->getLastInsertId();

    /* 4) Tạo chi tiết đơn con */
    $j = 0;
    while ($j < $count) {
        $ct = $ds_phong[$j];
        $ma_phong = $ct['ma_phong'];
        $ngay_den = $ct['ngay_den'];
        $ngay_di  = $ct['ngay_di'];
        $gia      = $ct['don_gia'];
        $so_ngay = (strtotime($ngay_di) - strtotime($ngay_den)) / 86400;
        if ($so_ngay <= 0) $so_ngay = 1;
        $thanh_tien = $gia * $so_ngay;

        $sql_ct = "INSERT INTO chitietdatphong(ma_dat_phong, ma_phong, ngay_den, ngay_di, don_gia, thanh_tien)
                   VALUES ('$ma_dat_phong', '$ma_phong', '$ngay_den', '$ngay_di', '$gia', '$thanh_tien')";
        $db->query($sql_ct);

        // xóa khỏi giỏ hàng
        $db->xoaChiTietGioHang($ct['ma_chi_tiet'], $ma_khach_hang);
        $j++;
    }

    echo "<script>alert('Đặt phòng thành công!'); window.location='lichsudatphong.php';</script>";
    exit();
} else {
    echo "<script>alert('Bạn chưa chọn phòng nào!'); window.location='giohang.php';</script>";
    exit();
}
