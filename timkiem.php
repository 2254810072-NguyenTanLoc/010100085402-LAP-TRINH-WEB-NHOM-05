<?php
session_start();
include('includes/config.php');
include 'classes/dbadmin.php';
$db = new dbadmin();

$ngay_den = $_GET['ngay_den'] ?? '';
$ngay_di = $_GET['ngay_di'] ?? '';
$so_nguoi = $_GET['so_nguoi'] ?? '';
$loai = $_GET['loai'] ?? '';

if ($loai) {
    $result = $db->getPhongTheoLoai($loai, $ngay_den, $ngay_di);
} else {
    $result = $db->timPhong('trong', $so_nguoi, $ngay_den, $ngay_di);
}
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Kết quả tìm kiếm phòng</title>
    <link rel="stylesheet" href="css/timkiem.css">
</head>

<body>
    <?php include 'includes/navbar.php'; ?>
    <div class="search-page">
        <div class="search-form">
            <h3>Tìm phòng</h3>
            <form method="GET" action="timkiem.php">
                <label>Ngày đến:</label>
                <input type="date" name="ngay_den" value="<?= ($ngay_den) ?>">
                <label>Ngày đi:</label>
                <input type="date" name="ngay_di" value="<?= ($ngay_di) ?>">
                <label>Số người:</label>
                <input type="number" name="so_nguoi" value="<?= ($so_nguoi) ?>" min="1">
                <button type="submit">Tìm phòng</button>
            </form>
        </div>

        <div class="results">
            <h2>Kết quả tìm kiếm</h2>
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="room-card">
                        <img src="uploads/<?= ($row['anh_phong'] ?? 'default.jpg') ?>" alt="<?= ($row['ten_phong']) ?>">
                        <div class="room-info">
                            <h3><?= ($row['ten_phong']) ?></h3>
                            <p class="type">Loại: <?= ($row['ten_loai']) ?></p>
                            <p class="price">Giá: <?= number_format($row['gia']) ?> VND/đêm</p>
                            <p>Số người tối đa: <?= $row['so_nguoi_toi_da'] ?></p>
                            <p><?= nl2br(($row['mo_ta'])) ?></p>
                            <div class="actions">
                                <a href="chitietphong.php?ma_phong=<?= $row['ma_phong'] ?>" class="detail">Xem chi tiết</a>
                                <!-- <a href="chitietphong.php?ma_phong=<?= $row['ma_phong'] ?>" class="book">Đặt ngay</a> -->
                            </div>
                        </div>
                    </div> <?php endwhile; ?>
            <?php else: ?>
                <p>Không tìm thấy phòng phù hợp.</p>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>