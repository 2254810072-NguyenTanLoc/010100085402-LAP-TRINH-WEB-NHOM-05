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

$action = $_GET['action'] ?? 'list';


// Lấy danh sách loại phòng
$ds_loai = $db->query("SELECT * FROM loaiphong");

// Lấy danh sách phòng và tính trạng thái thực tế dựa trên đơn đã xác nhận
$ds_phong = $db->query("
    SELECT p.*, l.ten_loai,
        CASE
            WHEN EXISTS (
                SELECT 1
                FROM chitietdatphong ct
                JOIN datphong dp ON ct.ma_dat_phong = dp.ma_dat_phong
                WHERE ct.ma_phong = p.ma_phong
                  AND dp.trang_thai = 'da_xac_nhan'
                  AND ct.ngay_di >= CURDATE()
            ) THEN 'da_dat'
            ELSE 'trong'
        END AS trang_thai_hien_tai
    FROM phong p
    JOIN loaiphong l ON p.ma_loai_phong = l.ma_loai_phong
");
?>

<link rel="stylesheet" href="css/quanlyphong.css">

<h2>Quản lý phòng</h2> <?php if ($action == 'list') { ?>
    <h3>Danh sách phòng</h3>
    <table>
        <tr>
            <th>Mã</th>
            <th>Tên</th>
            <th>Loại</th>
            <th>Giá</th>
            <th>Số người</th>
            <th>Ảnh</th>
            <th>Mô tả</th>
            <th>Trạng thái</th>
            <th>Hành động</th>
        </tr> <?php while ($phong = $ds_phong->fetch_assoc()) { ?>
            <tr>
                <td><?= $phong['ma_phong'] ?></td>
                <td><?= $phong['ten_phong'] ?></td>
                <td><?= $phong['ten_loai'] ?></td>
                <td><?= $phong['gia'] ?></td>
                <td><?= $phong['so_nguoi_toi_da'] ?></td>
                <td> <?php if ($phong['anh_phong']) { ?> <img src="../uploads/<?= $phong['anh_phong'] ?>" width="100">
                    <?php } ?> </td>
                <td><?= $phong['mo_ta'] ?></td>
                <td><?= $phong['trang_thai_hien_tai'] == 'trong' ? 'Trống' : 'Đã đặt' ?></td>
                <td> <a href="index.php?page=phong&action=edit&id=<?= $phong['ma_phong'] ?>" class="btn-edit">Sửa</a> <a
                        href="index.php?page=phong&action=delete&id=<?= $phong['ma_phong'] ?>" class="btn-del">Xóa</a> </td>
            </tr> <?php } ?>
    </table> <?php } else { ?>
    <div class="phong-container">
        <div class="form-section"> <?php if ($action == 'add') { ?>
                <h3>Thêm phòng mới</h3>
                <form method="POST" enctype="multipart/form-data"> <input type="text" name="ten_phong" placeholder="Tên phòng"
                        required> <select name="ma_loai_phong" required>
                        <option value="">-- Chọn loại phòng --</option> <?php while ($loai = $ds_loai->fetch_assoc()) { ?>
                            <option value="<?= $loai['ma_loai_phong'] ?>"><?= $loai['ten_loai'] ?></option> <?php } ?>
                    </select> <input type="number" name="gia" placeholder="Giá phòng" required> <input type="number"
                        name="so_nguoi_toi_da" placeholder="Số người tối đa" required> <textarea name="mo_ta"
                        placeholder="Mô tả" rows="5" cols="50"></textarea> <input type="file" name="anh_phong" accept="image/*">
                    <button type="submit" name="them_phong" class="btn-add">Thêm phòng</button>
                </form>
            <?php } elseif ($action == 'edit' && isset($_GET['id'])) {
            $phong = $db->getOne('phong', "ma_phong='" . $_GET['id'] . "'"); ?>
                <h3>Sửa phòng</h3>
                <form method="POST" enctype="multipart/form-data"> 
                    <input type="hidden" name="ma_phong" value="<?= $phong['ma_phong'] ?>"> 
                    <input type="text" name="ten_phong" value="<?= $phong['ten_phong'] ?>"> 
                    <select name="ma_loai_phong" required>
                        <?php while ($loai = $ds_loai->fetch_assoc()): ?>
                            <option value="<?= $loai['ma_loai_phong'] ?>" <?= $phong['ma_loai_phong'] == $loai['ma_loai_phong'] ? 'selected' : '' ?>>
                                <?= $loai['ten_loai'] ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                    <input type="number" name="gia" value="<?= $phong['gia'] ?>"> 
                    <input type="number" name="so_nguoi_toi_da" value="<?= $phong['so_nguoi_toi_da'] ?>"> 
                    <textarea name="mo_ta" rows="5" cols="50"><?= htmlspecialchars($phong['mo_ta']) ?></textarea>
                    <?php if ($phong['anh_phong']): ?>
                        <img src="../uploads/<?= $phong['anh_phong'] ?>" width="150" alt="Ảnh phòng hiện tại">
                    <?php endif; ?>
                    <input type="file" name="anh_phong" accept="image/*">
                    <select name="trang_thai">
                        <option value="trong" <?= $phong['trang_thai'] == 'trong' ? 'selected' : '' ?>>Trống</option>
                        <option value="da_dat" <?= $phong['trang_thai'] == 'da_dat' ? 'selected' : '' ?>>Đã đặt</option>
                    </select> 
                    <button type="submit" name="sua_phong" class="btn-edit">Cập nhật</button> </form>
            <?php } elseif ($action == 'delete' && isset($_GET['id'])) { ?>
                <h3>Xóa phòng</h3>
                <form method="POST" onsubmit="return confirm('Xác nhận xóa phòng này?');"> <input type="hidden" name="ma_phong"
                        value="<?= $_GET['id'] ?>">
                    <p>Bạn có chắc muốn xóa phòng #<?= $_GET['id'] ?>?</p> <button type="submit" name="xoa_phong"
                        class="btn-del">Xóa</button> <a href="index.php?page=phong&action=list" class="btn-cancel">Không</a>
                </form> <?php } ?>
        </div>
        <div class="list-section">
            <h3>Danh sách phòng</h3>
            <table>
                <tr>
                    <th>Mã</th>
                    <th>Tên</th>
                    <th>Loại</th>
                    <th>Giá</th>
                    <th>Số người</th>
                    <th>Ảnh</th>
                    <th>Mô tả</th>
                    <th>Trạng thái</th>
                    <th>Hành động</th>
                </tr> <?php while ($phong = $ds_phong->fetch_assoc()) { ?>
                    <tr>
                        <td><?= $phong['ma_phong'] ?></td>
                        <td><?= $phong['ten_phong'] ?></td>
                        <td><?= $phong['ten_loai'] ?></td>
                        <td><?= $phong['gia'] ?></td>
                        <td><?= $phong['so_nguoi_toi_da'] ?></td>
                        <td> <?php if ($phong['anh_phong']) { ?> <img src="../uploads/<?= $phong['anh_phong'] ?>" width="100">
                            <?php } ?> </td>
                        <td><?= $phong['mo_ta'] ?></td>
                        <td><?= $phong['trang_thai_hien_tai'] == 'trong' ? 'Trống' : 'Đã đặt' ?></td>
                        <td> <a href="index.php?page=phong&action=edit&id=<?= $phong['ma_phong'] ?>" class="btn-edit">Sửa</a> <a
                                href="index.php?page=phong&action=delete&id=<?= $phong['ma_phong'] ?>" class="btn-del">Xóa</a>
                        </td>
                    </tr> <?php } ?>
            </table>
        </div>
    </div> <?php } ?>