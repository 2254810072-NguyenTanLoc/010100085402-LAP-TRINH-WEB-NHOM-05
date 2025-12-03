<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['admin'])) {
    header("Location: ../dangnhap.php");
    exit();
}

require_once '../classes/dbadmin.php';
$db = new dbadmin();

// Lấy tất cả đơn đặt phòng
$result = $db->getAllDatPhong();

// Kiểm tra xem có yêu cầu xem phòng
$viewId = $_GET['view'] ?? null;
$action = $_GET['action'] ?? 'list';
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Quản lý đặt phòng</title>
    <link rel="stylesheet" href="css/quanlydatphong.css">
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th,
        td {
            padding: 8px;
            border: 1px solid #ddd;
            text-align: center;
        }

        th {
            background: #1769a3ff;
        }

        .btn-view,
        .btn-xacnhan,
        .btn-huy,
        .btn-trasom {
            padding: 6px 10px;
            border-radius: 6px;
            text-decoration: none;
            color: #fff;
            cursor: pointer;
        }

        .btn-view {
            background: #1976d2;
        }

        .btn-xacnhan {
            background: #28a745;
        }

        .btn-huy {
            background: #dc3545;
        }

        .btn-trasom {
            background: #f0ad4e;
        }

        .view-box {
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            border: 2px solid #1976d2;
            margin: 20px 0;
        }

        .view-box img {
            width: 110px;
            height: 75px;
            object-fit: cover;
            border-radius: 6px;
        }
    </style>
</head>

<body>

    <h2>Quản lý đặt phòng</h2>

    <?php if ($action === 'view' && $ma_dat_phong) {
        $rooms = $db->getPhongTheoDatPhong($ma_dat_phong);
    ?>
        <div class="view-box">
            <h3>Chi tiết phòng - Đơn <?= htmlspecialchars($ma_dat_phong) ?></h3>
            <table>
                <tr>
                    <th>Ảnh</th>
                    <th>Tên phòng</th>
                    <th>Ngày nhận</th>
                    <th>Ngày trả</th>
                    <th>Giá</th>
                    <th>Thành tiền</th>
                </tr>
                <?php while ($r = $rooms->fetch_assoc()) { ?>
                    <tr>
                        <td><img src="../uploads/<?= $r['anh_phong'] ?>"></td>
                        <td><?= $r['ten_phong'] ?></td>
                        <td><?= $r['ngay_den'] ?></td>
                        <td><?= $r['ngay_di'] ?></td>
                        <td><?= number_format($r['gia']) ?> VND</td>
                        <td><?= number_format($r['thanh_tien']) ?> VND</td>
                    </tr>
                <?php } ?>
            </table>
            <br>
            <a href="index.php?page=datphong&action=list" class="btn-view">Đóng</a>
        </div>
    <?php
    } ?>

    <table>
        <tr>
            <th>Đơn</th>
            <th>Khách hàng</th>
            <th>Email</th>
            <th>SĐT</th>
            <th>Ngày đặt</th>
            <th>Ngày nhận</th>
            <th>Ngày trả</th>
            <th>Tổng tiền</th>
            <th>Trạng thái</th>
            <th>Phòng</th>
            <th>Hành động</th>
            <th>Khác</th>
        </tr>

        <?php if ($result && $result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['ma_dat_phong'] ?></td>
                    <td><?= ($row['ho_ten']) ?></td>
                    <td><?= ($row['email']) ?></td>
                    <td><?= ($row['so_dien_thoai']) ?></td>
                    <td><?= $row['ngay_dat'] ?></td>
                    <td><?= $row['ngay_den'] ?></td>
                    <td><?= $row['ngay_di'] ?></td>
                    <td><?= number_format($row['tong_tien']) ?> VND</td>
                    <td>
                        <?php
                        switch ($row['trang_thai']) {
                            case 'cho_xac_nhan':
                                echo '⏳ Chờ xác nhận';
                                break;
                            case 'da_xac_nhan':
                                echo '✅ Đã xác nhận';
                                break;
                            case 'da_huy':
                                echo '❌ Đã hủy';
                                break;
                            case 'tra_som':
                                echo '↩️ Trả sớm';
                                break;
                            default:
                                echo '---';
                        }
                        ?>
                    </td>
                    <td>
                        <a class="btn-view" href="index.php?page=datphong&action=view&ma_dat_phong=<?= $row['ma_dat_phong'] ?>">Xem phòng</a>
                    </td>
                    <td>
                        <?php if ($row['trang_thai'] == 'cho_xac_nhan'): ?>
                            <form method="POST" style="display:inline-block;">
                                <input type="hidden" name="xacnhan" value="<?= $row['ma_dat_phong'] ?>">
                                <button class="btn-xacnhan">Xác nhận</button>
                            </form>
                            <form method="POST" style="display:inline-block;">
                                <input type="hidden" name="huy" value="<?= $row['ma_dat_phong'] ?>">
                                <button class="btn-huy" onclick="return confirm('Hủy đơn?');">Hủy</button>
                            </form>
                        <?php else: ?>
                            <i>Không khả dụng</i>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($row['trang_thai'] == 'da_xac_nhan'): ?>
                            <form method="POST">
                                <input type="hidden" name="trasom" value="<?= $row['ma_dat_phong'] ?>">
                                <button class="btn-trasom">Trả sớm</button>
                            </form>
                        <?php else: ?>
                            <i>---</i>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php endif; ?>
    </table>

</body>

</html>