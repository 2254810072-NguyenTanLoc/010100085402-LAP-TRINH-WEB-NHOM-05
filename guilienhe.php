<?php
session_start();
include 'includes/config.php';
include 'classes/dbadmin.php';
$db = new dbadmin();

// Lấy dữ liệu từ form
$ho_ten = $_POST['name'] ?? '';
$email = $_POST['email'] ?? '';
$noi_dung = $_POST['message'] ?? '';

// Kiểm tra dữ liệu
if (empty($ho_ten) || empty($email) || empty($noi_dung)) {
    echo "<script>alert('Vui lòng điền đầy đủ thông tin.'); window.history.back();</script>";
    exit;
}

// Chuẩn bị dữ liệu để insert
$data = [
    'ho_ten' => $ho_ten,
    'email' => $email,
    'noi_dung' => $noi_dung
];

// Thực hiện insert
if ($db->insert('lienhe', $data)) {
    echo "<script>alert('Gửi liên hệ thành công!'); window.location.href='lienhe.php';</script>";
} else {
    echo "<script>alert('Có lỗi xảy ra khi gửi liên hệ.'); window.history.back();</script>";
}
?>
