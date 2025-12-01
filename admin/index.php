<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: ../dangnhap.php");
    exit();
}

require_once '../classes/dbadmin.php';
$db = new dbadmin();

// Chỉ xử lý POST khi đang ở trang đặt phòng, không ảnh hưởng thống kê
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_GET['page'] ?? '') === 'datphong') {

    if (!empty($_POST['xacnhan'])) {
        $db->xacNhanDatPhong($_POST['xacnhan']);
    }

    if (!empty($_POST['huy'])) {
        $db->huyDatPhong($_POST['huy']);
    }

    if (!empty($_POST['trasom'])) {
        $db->traSomPhong($_POST['trasom']);
    }

    header("location: index.php?page=datphong&action=list");
    exit();
}

if (isset($_POST['them_phong'])) {
    $fileName = null;
    if (!empty($_FILES['anh_phong']['name'])) {
        $fileName = time() . "_" . $_FILES['anh_phong']['name'];
        move_uploaded_file($_FILES['anh_phong']['tmp_name'], "../uploads/" . $fileName);
    }
    $data = [
        'ten_phong' => $_POST['ten_phong'],
        'ma_loai_phong' => $_POST['ma_loai_phong'],
        'gia' => $_POST['gia'],
        'so_nguoi_toi_da' => $_POST['so_nguoi_toi_da'],
        'mo_ta' => $_POST['mo_ta'],
        'anh_phong' => $fileName,
        'trang_thai' => 'trong'
    ];
    $db->insert('phong', $data);
    header("Location: index.php?page=phong&action=list");
    exit();
}

// Xử lý sửa phòng
if (isset($_POST['sua_phong'])) {
    $ma_phong = $_POST['ma_phong'];
    $fileName = null;
    if (!empty($_FILES['anh_phong']['name'])) {
        $fileName = time() . "_" . $_FILES['anh_phong']['name'];
        move_uploaded_file($_FILES['anh_phong']['tmp_name'], "../uploads/" . $fileName);
    } else {
        // giữ nguyên ảnh cũ
        $phong = $db->getOne('phong', "ma_phong='$ma_phong'");
        $fileName = $phong['anh_phong'];
    }
    $data = [
        'ten_phong' => $_POST['ten_phong'],
        'ma_loai_phong' => $_POST['ma_loai_phong'],
        'gia' => $_POST['gia'],
        'so_nguoi_toi_da' => $_POST['so_nguoi_toi_da'],
        'mo_ta' => $_POST['mo_ta'],
        'anh_phong' => $fileName,
        'trang_thai' => $_POST['trang_thai']
    ];
    $db->update('phong', $data, "ma_phong='$ma_phong'");
    header("Location: index.php?page=phong&action=list");
    exit();
}

// Xử lý xóa phòng
if (isset($_POST['xoa_phong'])) {
    $ma_phong = $_POST['ma_phong'];
    $db->delete('phong', "ma_phong='$ma_phong'");
    header("Location: index.php?page=phong&action=list");
    exit();
}

if (isset($_POST['them_khach'])) {
    // Hash mật khẩu trước khi lưu
    $mat_khau_hash = password_hash($_POST['mat_khau'], PASSWORD_DEFAULT);

    $data = [
        'ten_dang_nhap' => $_POST['ten_dang_nhap'],
        'mat_khau' => $mat_khau_hash,
        'ho_ten' => $_POST['ho_ten'],
        'email' => $_POST['email'],
        'so_dien_thoai' => $_POST['so_dien_thoai'],
        'dia_chi' => $_POST['dia_chi'],
        'vai_tro' => $_POST['vai_tro']
    ];

    $db->insert('khachhang', $data);
    header("Location: index.php?page=khachhang&action=list");
    exit();
}

if (isset($_POST['sua_khach'])) {
    $id = $_POST['ma_khach_hang'];

    $data = [
        'ten_dang_nhap' => $_POST['ten_dang_nhap'],
        'ho_ten' => $_POST['ho_ten'],
        'email' => $_POST['email'],
        'so_dien_thoai' => $_POST['so_dien_thoai'],
        'dia_chi' => $_POST['dia_chi'],
        'vai_tro' => $_POST['vai_tro']
    ];

    if (!empty($_POST['mat_khau'])) {
        $data['mat_khau'] = password_hash($_POST['mat_khau'], PASSWORD_DEFAULT);
    }

    $db->update('khachhang', $data, "ma_khach_hang='$id'");
    header("Location: index.php?page=khachhang&action=list");
    exit();
}

if (isset($_POST['xoa_khach'])) {
    $id = $_POST['ma_khach_hang'];
    $db->delete('khachhang', "ma_khach_hang='$id'");
    header("Location: index.php?page=khachhang&action=list");
    exit();
}

$page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';
$action = isset($_GET['action']) ? $_GET['action'] : 'list';
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Trang quản trị</title>
    <link rel="stylesheet" href="css/admin.css">
</head>

<body>
    <div class="admin-container">

        <div class="sidebar">
            <div class="admin-info">
                <img src="admin-avatar.jpg" alt="Admin Avatar" class="avatar">
                <div class="admin-text">
                    <span class="welcome">Xin chào</span>
                    <span class="name"><?php
                                        echo ($_SESSION['admin'] == 'admin') ? 'Quản trị viên' : $_SESSION['admin'];
                                        ?>
                    </span>
                </div>
            </div>
            <ul>
                <li>
                    <input type="checkbox" id="menu-thongke" <?= ($_GET['page'] ?? '') == 'thongke' ? 'checked' : '' ?>>
                    <label for="menu-thongke" class="<?= ($_GET['page'] ?? '') == 'thongke' ? 'active-parent' : '' ?>">
                        📊 Thống kê <span class="arrow"></span>
                    </label>
                    <ul class="submenu">
                        <li><span class="dot"></span>
                            <a href="index.php?page=thongke&action=view"
                                class="<?= ($_GET['page'] ?? '') == 'thongke' ? 'active-child' : '' ?>">
                                Xem thống kê
                            </a>
                        </li>
                    </ul>
                </li>

                <li>
                    <input type="checkbox" id="menu-phong" <?= ($_GET['page'] ?? '') == 'phong' ? 'checked' : '' ?>>
                    <label for="menu-phong" class="<?= ($_GET['page'] ?? '') == 'phong' ? 'active-parent' : '' ?>">
                        🛏️ Quản lý phòng <span class="arrow"></span>
                    </label>
                    <ul class="submenu">
                        <li><span class="dot"></span>
                            <a href="index.php?page=phong&action=list"
                                class="<?= ($_GET['page'] ?? '') == 'phong' && ($_GET['action'] ?? '') == 'list' ? 'active-child' : '' ?>">
                                Danh sách
                            </a>
                        </li>
                        <li><span class="dot"></span>
                            <a href="index.php?page=phong&action=add"
                                class="<?= ($_GET['page'] ?? '') == 'phong' && ($_GET['action'] ?? '') == 'add' ? 'active-child' : '' ?>">
                                Thêm phòng
                            </a>
                        </li>
                    </ul>
                </li>

                <li>
                    <input type="checkbox" id="menu-datphong" <?= ($_GET['page'] ?? '') == 'datphong' ? 'checked' : '' ?>>
                    <label for="menu-datphong" class="<?= ($_GET['page'] ?? '') == 'datphong' ? 'active-parent' : '' ?>">
                        📑 Quản lý đặt phòng <span class="arrow"></span>
                    </label>
                    <ul class="submenu">
                        <li><span class="dot"></span>
                            <a href="index.php?page=datphong&action=list"
                                class="<?= ($_GET['page'] ?? '') == 'datphong' && ($_GET['action'] ?? '') == 'list' ? 'active-child' : '' ?>">
                                Danh sách
                            </a>
                        </li>
                    </ul>
                </li>

                <li>
                    <input type="checkbox" id="menu-khachhang" <?= ($_GET['page'] ?? '') == 'khachhang' ? 'checked' : '' ?>>
                    <label for="menu-khachhang" class="<?= ($_GET['page'] ?? '') == 'khachhang' ? 'active-parent' : '' ?>">
                        👥 Quản lý khách hàng <span class="arrow"></span>
                    </label>
                    <ul class="submenu">
                        <li><span class="dot"></span>
                            <a href="index.php?page=khachhang&action=list"
                                class="<?= ($_GET['page'] ?? '') == 'khachhang' && ($_GET['action'] ?? '') == 'list' ? 'active-child' : '' ?>">
                                Danh sách
                            </a>
                        </li>
                        <li><span class="dot"></span>
                            <a href="index.php?page=khachhang&action=add"
                                class="<?= ($_GET['page'] ?? '') == 'khachhang' && ($_GET['action'] ?? '') == 'add' ? 'active-child' : '' ?>">
                                Thêm khách
                            </a>
                        </li>
                    </ul>
                </li>
                <li>
                    <input type="checkbox" id="menu-lienhe" <?= ($_GET['page'] ?? '') == 'lienhe' ? 'checked' : '' ?>>
                    <label for="menu-lienhe" class="<?= ($_GET['page'] ?? '') == 'lienhe' ? 'active-parent' : '' ?>">
                        ✉️ Quản lý liên hệ <span class="arrow"></span>
                    </label>
                    <ul class="submenu">
                        <li><span class="dot"></span>
                            <a href="index.php?page=lienhe&action=list"
                                class="<?= ($_GET['page'] ?? '') == 'lienhe' && ($_GET['action'] ?? '') == 'list' ? 'active-child' : '' ?>">
                                Danh sách liên hệ
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="log_out"><a href="../dangxuat.php">🚪 Đăng xuất</a></li>
            </ul>
        </div>

        <div class="main-content">
            <?php
            $page = $_GET['page'] ?? 'dashboard';
            $ma_dat_phong = $_GET['ma_dat_phong'] ?? null;
            switch ($page) {
                case 'thongke':
                    include 'thongke.php';
                    break;

                case 'phong':
                    include 'quanlyphong.php';
                    break;
                case 'datphong':
                    include 'quanlydatphong.php';
                    break;
                case 'khachhang':
                    include 'quanlykhachhang.php';
                    break;
                case 'lienhe':
                    include 'quanlylienhe.php';
                    break;
                default:
                    echo "<h1>Dashboard</h1><p>Chào mừng bạn đến trang quản trị.</p>";
            }
            ?>
        </div>
    </div>
</body>

</html>