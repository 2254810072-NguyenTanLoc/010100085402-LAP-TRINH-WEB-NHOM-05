<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);
include('includes/config.php');
include 'classes/dbadmin.php';
$db = new dbadmin();

$dia_diem = $_GET['dia_diem'] ?? '';
$ngay_den = $_GET['ngay_den'] ?? '';
$ngay_di = $_GET['ngay_di'] ?? '';
$so_nguoi = $_GET['so_nguoi'] ?? '';
$loai = $_GET['loai'] ?? '';

if ($loai) {
    $result = $db->getPhongTheoLoai($loai);
} else {
    $result = $db->timPhong('trong', $so_nguoi);
}
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Khách sạn Baby Cute</title>
    <link rel="stylesheet" href="css/index.css">
    <style>
        .rooms {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }

        .room-link {
            display: block;
            flex: 1 1 calc(25% - 20px);
            text-decoration: none;
            color: inherit;
        }

        .room {
            display: flex;
            flex-direction: column;
            height: 100%;
            border: 1px solid #ddd;
            border-radius: 8px;
            overflow: hidden;
            background: #fff;
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .room img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            transition: transform 0.3s;
        }

        .room:hover img {
            transform: scale(1.1);
        }

        .room:hover {
            transform: scale(1.05);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }

        .room .info {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 15px;
        }
    </style>
</head>

<body>
    <nav class="navbar">
        <div class="logo">
            <a href="index.php"><img src="images/hotel.png" alt="Hotel Logo"></a>
        </div>
        <ul class="nav-links">
            <li><a href="giohang.php">Thông tin đặt phòng</a></li>
            <li class="dropdown">
                <a href="index.php">Loại phòng</a>
                <ul class="dropdown-menu">
                    <li><a href="index.php?loai=1">Phòng thường</a></li>
                    <li><a href="index.php?loai=2">Phòng VIP</a></li>
                </ul>
            </li>
            <li><a href="lienhe.php">Liên hệ</a></li>
            <li><a href="gioithieu.php">Giới thiệu</a></li>
        </ul>
        <div class="auth-links">
            <?php if (isset($_SESSION['user'])): ?>
                <span class="username">👤 <?= ($_SESSION['user']) ?></span>
                <a href="capnhatthongtin.php" class="btn-logout">Cập nhật thông tin</a>
                <a href="dangxuat.php" class="btn-logout">Đăng xuất</a>
            <?php else: ?>
                <a href="dangnhap.php" class="btn-login">Đăng nhập</a>
                <a href="dangky.php" class="btn-register">Đăng ký</a>
            <?php endif; ?>
        </div>
    </nav>

    <header class="hero">
        <div class="slide">
            <img src="images/bgimg4.jpeg" alt="">
            <div class="text">
                <h1>Resort biển tuyệt đẹp</h1>
                <p>Nơi những bờ biển bắt đầu</p>
            </div>
        </div>

        <div class="slide">
            <img src="images/bgimg7.jpg" alt="">
            <div class="text">
                <h1>Thiên đường nghỉ dưỡng</h1>
                <p>Tận hưởng khoảnh khắc thư giãn tuyệt vời</p>
            </div>
        </div>

        <div class="slide">
            <img src="images/bgimg6.jpeg" alt="">
            <div class="text">
                <h1>Dịch vụ chuẩn 5 sao</h1>
                <p>Trải nghiệm đẳng cấp - Giá cực tốt</p>
            </div>
        </div>
    </header>

    <section class="search-box">
        <?php $today = date('Y-m-d') ?>
        <form method="GET" action="timkiem.php">
        <form method="GET" action="index.php">
            <input type="date" name="ngay_den" value="<?= $ngay_den ?>" min="<?= $today ?>">
            <input type="date" name="ngay_di" value="<?= $ngay_di ?>" min="<?= $today ?>">
            <input type="number" name="so_nguoi" placeholder="Số người" value="<?= $so_nguoi ?>" min="1">
            <button type="submit">Tìm phòng</button>
        </form>
    </section>

    <section class="room-list">
        <h2>Danh sách phòng khách sạn</h2>
        <div class="rooms">
            <?php while ($row = $result->fetch_assoc()) { ?>
                <a href="chitietphong.php?ma_phong=<?= $row['ma_phong'] ?>" class="room-link">
                    <div class="room">
                        <img src="uploads/<?= $row['anh_phong'] ?? 'default.jpg' ?>" alt="<?= $row['ten_phong'] ?>">
                        <div class="info">
                            <h3><?= $row['ten_phong'] ?></h3>
                            <p>Loại: <?= $row['ten_loai'] ?></p>
                            <p>Giá: <?= number_format($row['gia']) ?> VND/đêm</p>
                            <p>Số người tối đa: <?= $row['so_nguoi_toi_da'] ?></p>
                            <p><?= $row['mo_ta'] ?></p>
                        </div>
                    </div>
                </a>
            <?php } ?>
        </div>
    </section>
</body>

</html>