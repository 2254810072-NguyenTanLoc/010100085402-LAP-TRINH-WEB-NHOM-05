<?php
session_start();
include 'includes/config.php';
include 'classes/dbadmin.php';
$db = new dbadmin();

if (!isset($_GET['ma_phong'])) {
    echo "<script>alert('Không tìm thấy phòng.'); window.location='index.php';</script>";
    exit();
}

$ma_phong = $_GET['ma_phong'];
$phong = $db->getChiTietPhong($ma_phong);
if (!$phong) {
    echo "<script>alert('Phòng không tồn tại.'); window.location='index.php';</script>";
    exit();
}

if (isset($_POST['gui_danh_gia'])) {
    if (!isset($_SESSION['ma_khach_hang'])) {
        echo "<script>alert('Bạn cần đăng nhập để gửi đánh giá!'); window.location='dangnhap.php';</script>";
        exit();
    }

    $ma_khach_hang = $_SESSION['ma_khach_hang'];
    $diem = (int)$_POST['diem'];
    $noi_dung = trim($_POST['noi_dung']);

    if ($diem < 1 || $diem > 5 || $noi_dung == '') {
        echo "<script>alert('Vui lòng nhập đủ thông tin hợp lệ!');</script>";
    } else {
        $db->insert('danhgia', [
            'ma_phong' => $ma_phong,
            'ma_khach_hang' => $ma_khach_hang,
            'diem_danh_gia' => $diem,
            'noi_dung' => $noi_dung,
            'ngay_danh_gia' => date('Y-m-d H:i:s')
        ]);
        echo "<script>alert('Cảm ơn bạn đã đánh giá!'); window.location.reload();</script>";
    }
}

if (isset($_POST['them_gio'])) {
    if (!isset($_SESSION['ma_khach_hang'])) {
        echo "<script>alert('Bạn cần đăng nhập để thêm vào giỏ hàng!'); window.location='dangnhap.php';</script>";
        exit();
    }

    $ma_khach_hang = $_SESSION['ma_khach_hang'];
    $ma_phong_post = (int)$_POST['ma_phong'];
    $ngay_den_post = $_POST['ngay_den'] ?? '';
    $ngay_di_post = $_POST['ngay_di'] ?? '';

    if (!$ngay_den_post || !$ngay_di_post) {
        echo "<script>alert('Vui lòng chọn ngày đến và ngày đi');</script>";
    } else {

        $tz = new DateTimeZone('Asia/Ho_Chi_Minh');
        $today = new DateTime('today', $tz);
        $den = DateTime::createFromFormat('Y-m-d', $ngay_den_post, $tz);
        $di  = DateTime::createFromFormat('Y-m-d', $ngay_di_post, $tz);

        if (!$den || !$di) {
            echo "<script>alert('Định dạng ngày không hợp lệ');</script>";
        } elseif ($den < $today) {
            echo "<script>alert('Ngày nhận không được là ngày trước hôm nay');</script>";
        } elseif ($di <= $den) {
            echo "<script>alert('Ngày trả phải lớn hơn ngày đến');</script>";
        } else {
            $ok = $db->themChiTietGioHang($ma_khach_hang, $ma_phong_post, $ngay_den_post, $ngay_di_post);
            if ($ok) {
                echo "<script>alert('Đã thêm vào giỏ hàng'); window.location='giohang.php';</script>";
                exit();
            } else {
                echo "<script>alert('Thêm giỏ hàng thất bại - Kiểm tra ngày hoặc phòng đã trùng trong giỏ!');</script>";
            }
        }
    }
}
$danhgia = $db->getDanhGiaPhong($ma_phong);
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Chi tiết phòng - <?= ($phong['ten_phong']) ?></title>
    <link rel="stylesheet" href="css/chitietphong.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
    <?php include 'includes/navbar.php'; ?>
    <div class="container">
        <?php $backUrl = $_SERVER['HTTP_REFERER'] ?? 'index.php' ?>
        <a href="<?= $backUrl ?>" class="btn">← Quay lại</a>

        <div class="room-detail">
            <div class="room-media">
                <img src="uploads/<?= ($phong['anh_phong'] ?? 'default.jpg') ?>" alt="<?= ($phong['ten_phong']) ?>">
            </div>

            <div class="info">
                <div class="info-box">
                    <h2><?= ($phong['ten_phong']) ?></h2>

                    <div class="info-item">
                        <b>Loại phòng:</b>
                        <span><?= ($phong['ten_loai']) ?></span>
                    </div>

                    <div class="info-item">
                        <b>Giá:</b>
                        <span><?= number_format($phong['gia']) ?> VND / đêm</span>
                    </div>

                    <div class="info-item">
                        <b>Số người tối đa:</b>
                        <span><?= $phong['so_nguoi_toi_da'] ?></span>
                    </div>

                    <div class="info-item">
                        <b>Mô tả:</b>
                        <span><?= nl2br(($phong['mo_ta'])) ?></span>
                    </div>

                    <div class="info-actions">
                        <?php if ($phong['trang_thai'] == 'trong'): ?>
                            <?php $today = date('Y-m-d'); ?>
                            <form method="POST" action="" class="add-to-cart-form">
                                <input type="hidden" name="ma_phong" value="<?= ($phong['ma_phong']) ?>">
                                <label>Ngày nhận:</label>
                                <input type="date" name="ngay_den" min="<?= $today ?>" required>
                                <label>Ngày trả:</label>
                                <input type="date" name="ngay_di" min="<?= $today ?>" required>
                                <button type="submit" name="them_gio" class="btn">Đặt phòng ngay</button>
                            </form>
                            <!-- <br> -->
                            <!-- <a href="datphong.php?ma_phong=<?= $phong['ma_phong'] ?>" class="btn">Đặt phòng ngay</a> -->
                        <?php else: ?>
                            <span class="badge-unavailable">Phòng hiện đã được đặt</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="amenities-section">
            <h3>Tiện nghi phòng</h3>
            <div class="amenities-grid">
                <div class="amenity"><i class="fa fa-wifi"></i><span>Wi-Fi miễn phí</span></div>
                <div class="amenity"><i class="fa fa-snowflake"></i><span>Điều hòa</span></div>
                <div class="amenity"><i class="fa fa-tv"></i><span>TV màn hình phẳng</span></div>
                <div class="amenity"><i class="fa fa-coffee"></i><span>Minibar</span></div>
                <div class="amenity"><i class="fa fa-bath"></i><span>Phòng tắm riêng</span></div>
            </div>
        </div>

        <div class="reviews">
            <h3>Đánh giá từ khách hàng</h3>

            <?php if ($danhgia && $danhgia->num_rows > 0): ?>
                <?php while ($d = $danhgia->fetch_assoc()): ?>
                    <div class="review-item">
                        <p><b><?= ($d['ho_ten']) ?></b></p>
                        <p class="stars"><?= str_repeat('★', (int)$d['diem_danh_gia']) . str_repeat('☆', 5 - (int)$d['diem_danh_gia']) ?></p>
                        <p><?= ($d['noi_dung']) ?></p>
                        <small><i>Ngày đánh giá: <?= date('d/m/Y H:i', strtotime($d['ngay_danh_gia'])) ?></i></small>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>Chưa có đánh giá nào cho phòng này.</p>
            <?php endif; ?>
        </div>

        <!-- Form đánh giá -->
        <!-- <hr>
        <?php if (isset($_SESSION['ma_khach_hang'])): ?>
            <form method="POST" action="">
                <h4>Gửi đánh giá của bạn</h4>
                <label>Điểm (1-5):</label>
                <input type="number" name="diem" min="1" max="5" required>
                <br><br>
                <label>Nội dung:</label><br>
                <textarea name="noi_dung" required></textarea>
                <br>
                <button type="submit" name="gui_danh_gia">Gửi đánh giá</button>
            </form>
        <?php else: ?>
            <p><a href="dangnhap.php">Đăng nhập</a> để gửi đánh giá của bạn.</p>
        <?php endif; ?> -->
    </div>
</body>

</html>