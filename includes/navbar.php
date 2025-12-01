<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        body {
            margin: 0;
        }
        
        .navbar {
            display: flex;
            align-items: center;
            justify-content: space-evenly;
            height: 80px;
            padding: 10px 25px;
            background: #ffffff;
            position: relative;
            width: 100%;
            top: 0;
            z-index: 999;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            font-family: Arial, sans-serif;
        }

        .navbar .logo img {
            height: 42px;
            width: auto;
            object-fit: contain;
        }

        .nav-links {
            display: flex;
            gap: 35px;
            list-style: none;
        }

        .nav-links li a {
            color: #222;
            text-decoration: none;
            font-weight: 600;
            padding: 6px 14px;
            transition: color 0.3s;
            font-size: 14px;
            border: 2px solid transparent;
            border-radius: 20px;
        }

        .nav-links li a:hover {
            color: #3498db;
            background: #c9d3b5;
            border: 1px solid #fff;
            border-color: #c9d3b5;
        }

        .auth-links {
            display: flex;
            gap: 8px;
            margin-left: 20px;
        }

        .auth-links a {
            padding: 6px 10px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 600;
            font-size: 13px;
            white-space: nowrap;
            border: 1px solid #3498db;
            transition: 0.2s;
        }

        .btn-login {
            background: #ffffff;
            color: #3498db;
        }

        .btn-login:hover {
            background: #3498db;
            color: #fff;
        }

        .btn-register {
            background: #3498db;
            color: #fff;
        }

        .btn-register:hover {
            background: #1f78be;
            color: #fff;
        }

        .btn-logout:hover {
            background: #1f78be;
            color: #fff;
        }

        @media (max-width: 992px) {
            .auth-links a {
                padding: 5px 8px;
                font-size: 12px;
            }

            .navbar {
                padding: 8px 15px;
            }

            .nav-links {
                gap: 10px;
            }
        }

        .nav-links .dropdown {
            position: relative;
        }

        .dropdown-menu {
            display: none;
            position: absolute;
            background: white;
            padding: 10px;
            list-style: none;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
        }

        .dropdown-menu li {
            margin: 5px 0;
        }

        .dropdown-menu a {
            text-decoration: none;
            color: #333;
            font-size: 14px;
            padding: 6px 12px;
            display: block;
            border-radius: 6px;
        }

        .dropdown-menu a:hover {
            background: #007bff;
            color: white;
        }

        .dropdown:hover .dropdown-menu {
            display: block;
        }

        .dropdown>a {
            position: relative;
            padding-right: 18px;
        }

        .dropdown>a::after {
            content: "";
            display: none;
            border: 5px solid transparent;
            border-top-color: #333;
            position: absolute;
            right: 5px;
            top: 50%;
            transform: translateY(-50%);
        }

        .username {
            font-weight: bold;
            background: #ebe9e9;
            border: none;
            padding: 5px;
            margin: -2px;
            border-radius: 5px;
            cursor: pointer;
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

</body>

</html>