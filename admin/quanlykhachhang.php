<?php
if (!isset($_SESSION['admin'])) {
    header("Location: ../dangnhap.php");
    exit();
}
require_once '../classes/dbadmin.php';
$db = new dbadmin();

$action = $_GET['action'] ?? 'list';

$ds_khach = $db->getAll('khachhang');
?>

<link rel="stylesheet" href="css/quanlykhachhang.css">

<h2>Quản lý khách hàng</h2>

<?php if ($action == 'list') { ?>

    <h3>Danh sách khách hàng</h3>
    <table>
        <tr>
            <th>Mã</th>
            <th>Tên đăng nhập</th>
            <th>Họ tên</th>
            <th>Email</th>
            <th>SĐT</th>
            <th>Địa chỉ</th>
            <th>Vai trò</th>
            <th>Hành động</th>
        </tr>
        <?php while ($kh = $ds_khach->fetch_assoc()) { ?>
            <tr>
                <td><?= $kh['ma_khach_hang'] ?></td>
                <td><?= $kh['ten_dang_nhap'] ?></td>
                <td><?= $kh['ho_ten'] ?></td>
                <td><?= $kh['email'] ?></td>
                <td><?= $kh['so_dien_thoai'] ?></td>
                <td><?= $kh['dia_chi'] ?></td>
                <td><?= $kh['vai_tro'] == 'admin' ? 'Admin' : 'Khách' ?></td>
                <td>
                    <a href="index.php?page=khachhang&action=edit&id=<?= $kh['ma_khach_hang'] ?>" class="btn-edit">Sửa</a>
                    <a href="index.php?page=khachhang&action=delete&id=<?= $kh['ma_khach_hang'] ?>" class="btn-del">Xóa</a>
                </td>
            </tr>
        <?php } ?>
    </table>

<?php } else { ?>

    <div class="khach-container">
        <div class="form-section">
            <?php if ($action == 'add') { ?>
                <h3>Thêm khách hàng</h3>
                <form method="POST">
                    <input type="text" name="ten_dang_nhap" placeholder="Tên đăng nhập" required>
                    <input type="text" name="mat_khau" placeholder="Mật khẩu" required>
                    <input type="text" name="ho_ten" placeholder="Họ tên">
                    <input type="email" name="email" placeholder="Email">
                    <input type="text" name="so_dien_thoai" placeholder="Số điện thoại">
                    <input type="text" name="dia_chi" placeholder="Địa chỉ">
                    <select name="vai_tro">
                        <option value="khach">Khách</option>
                        <option value="admin">Admin</option>
                    </select>
                    <button type="submit" name="them_khach" class="btn-add">Thêm</button>
                </form>
            <?php } elseif ($action == 'edit' && isset($_GET['id'])) {
                $kh = $db->getOne('khachhang', "ma_khach_hang='" . $_GET['id'] . "'");
            ?>
                <h3>Sửa khách hàng</h3>
                <form method="POST">
                    <input type="hidden" name="ma_khach_hang" value="<?= $kh['ma_khach_hang'] ?>">
                    <input type="text" name="ten_dang_nhap" value="<?= $kh['ten_dang_nhap'] ?>">
                    <input type="text" name="mat_khau" value="<?= $kh['mat_khau'] ?>">
                    <input type="text" name="ho_ten" value="<?= $kh['ho_ten'] ?>">
                    <input type="email" name="email" value="<?= $kh['email'] ?>">
                    <input type="text" name="so_dien_thoai" value="<?= $kh['so_dien_thoai'] ?>">
                    <input type="text" name="dia_chi" value="<?= $kh['dia_chi'] ?>">
                    <select name="vai_tro">
                        <option value="khach" <?= $kh['vai_tro'] == 'khach' ? 'selected' : '' ?>>Khách</option>
                        <option value="admin" <?= $kh['vai_tro'] == 'admin' ? 'selected' : '' ?>>Admin</option>
                    </select>
                    <button type="submit" name="sua_khach" class="btn-edit">Cập nhật</button>
                </form>
            <?php } elseif ($action == 'delete' && isset($_GET['id'])) { ?>
                <h3>Xóa khách hàng</h3>
                <form method="POST" onsubmit="return confirm('Xác nhận xóa khách hàng này?');">
                    <input type="hidden" name="ma_khach_hang" value="<?= $_GET['id'] ?>">
                    <p>Bạn có chắc muốn xóa khách hàng #<?= $_GET['id'] ?>?</p>
                    <button type="submit" name="xoa_khach" class="btn-del">Xóa</button>
                    <a href="index.php?page=khachhang&action=list" class="btn-cancel">Không</a>
                </form>
            <?php } ?>
        </div>

        <div class="list-section">
            <h3>Danh sách khách hàng</h3>
            <table>
                <tr>
                    <th>Mã</th>
                    <th>Tên đăng nhập</th>
                    <th>Họ tên</th>
                    <th>Email</th>
                    <th>SĐT</th>
                    <th>Địa chỉ</th>
                    <th>Vai trò</th>
                    <th>Hành động</th>
                </tr>
                <?php
                $ds_khach = $db->getAll('khachhang');
                while ($kh = $ds_khach->fetch_assoc()) { ?>
                    <tr>
                        <td><?= $kh['ma_khach_hang'] ?></td>
                        <td><?= $kh['ten_dang_nhap'] ?></td>
                        <td><?= $kh['ho_ten'] ?></td>
                        <td><?= $kh['email'] ?></td>
                        <td><?= $kh['so_dien_thoai'] ?></td>
                        <td><?= $kh['dia_chi'] ?></td>
                        <td><?= $kh['vai_tro'] == 'admin' ? 'Admin' : 'Khách' ?></td>
                        <td>
                            <a href="index.php?page=khachhang&action=edit&id=<?= $kh['ma_khach_hang'] ?>" class="btn-edit">Sửa</a>
                            <a href="index.php?page=khachhang&action=delete&id=<?= $kh['ma_khach_hang'] ?>" class="btn-del">Xóa</a>
                        </td>
                    </tr>
                <?php } ?>
            </table>
        </div>
    </div>
<?php } ?>