<?php
session_start();
include 'includes/config.php';
include 'classes/dbadmin.php';
$db = new dbadmin();

if (!isset($_SESSION['ma_khach_hang'])) {
    echo "<script>alert('Vui lòng đăng nhập để xem giỏ hàng'); window.location='dangnhap.php';</script>";
    exit();
}

$ma_khach_hang = $_SESSION['ma_khach_hang'];
$rows = $db->layChiTietGioHangTheoKhach($ma_khach_hang);
$tong = 0;
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="utf-8">
    <title>Giỏ hàng của bạn</title>
    <link rel="stylesheet" href="css/giohang.css">
</head>

<body>
<?php include 'includes/navbar.php'; ?>
<div class="container">
    <h2>🛒 Giỏ hàng của bạn</h2>

    <?php if ($rows && $rows->num_rows > 0): ?>
        <form method="POST" action="datphongchon.php">
            <table>
                <tr>
                    <th>Chọn</th>
                    <th>Ảnh</th>
                    <th>Tên phòng</th>
                    <th>Ngày nhận</th>
                    <th>Ngày trả</th>
                    <th>Đơn giá / đêm</th>
                    <th>Thành tiền</th>
                    <th>Hành động</th>
                </tr>

                <?php while ($r = $rows->fetch_assoc()): ?>
                    <?php $tong += $r['thanh_tien']; ?>
                    <tr>
                        <td>
                            <input type="checkbox" name="chon_phong[]" value="<?= $r['ma_chi_tiet'] ?>">
                        </td>
                        <td><img class="img-room" src="uploads/<?= ($r['anh_phong'] ?? 'default.jpg') ?>" alt=""></td>
                        <td><?= ($r['ten_phong']) ?></td>
                        <td><?= ($r['ngay_den']) ?></td>
                        <td><?= ($r['ngay_di']) ?></td>
                        <td><?= number_format($r['don_gia']) ?> VND</td>
                        <td><?= number_format($r['thanh_tien']) ?> VND</td>
                        <td class="actions">
                            <a href="xoachitietgiohang.php?ma_chi_tiet=<?= $r['ma_chi_tiet'] ?>" class="btn btn-danger" onclick="return confirm('Xóa phòng này khỏi giỏ hàng?')">Xóa</a>
                            <a href="chitietphong.php?ma_phong=<?= $r['ma_phong'] ?>" class="btn btn-primary">Xem</a>
                        </td>
                    </tr>
                <?php endwhile; ?>

                <tr>
                    <td colspan="8" style="text-align:right;">
                        <button type="submit" name="action" value="chon" class="btn btn-primary">Đặt phòng đã chọn</button>
                        <button type="submit" name="action" value="tatca" class="btn btn-primary" style="margin-left:10px;">Đặt tất cả</button>
                    </td>
                </tr>
            </table>
        </form>

        <div class="total-box">
            Tổng tiền: <?= number_format($tong) ?> VND
        </div>

    <?php else: ?>
        <div class="empty" style="padding:30px; text-align:center;">
            Giỏ hàng trống - Vui lòng chọn và đặt phòng
        </div>
    <?php endif; ?>

    <div style="margin-top:20px;">
        <a href="index.php" class="btn btn-primary">← Tiếp tục chọn phòng</a>
        <a href="lichsudatphong.php" class="btn btn-primary" style="margin-left:800px;">Lịch sử đặt phòng</a>
    </div>
</div>
</body>
</html>
