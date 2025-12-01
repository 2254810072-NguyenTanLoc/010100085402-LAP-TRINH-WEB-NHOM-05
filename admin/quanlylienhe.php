<?php
if (!isset($_SESSION['admin'])) {
    header("Location: ../dangnhap.php");
    exit();
}

require_once '../classes/dbadmin.php';
$db = new dbadmin();

$action = $_GET['action'] ?? 'list';

if (isset($_POST['xoa_lien_he'])) {
    $ma_lien_he = $_POST['ma_lien_he'];
    $db->delete('lienhe', "ma_lien_he = '$ma_lien_he'");
    header("Location: index.php?page=lienhe&action=list");
    exit();
}

$ds_lien_he = $db->getAll('lienhe');
?>

<link rel="stylesheet" href="css/quanlyphong.css">

<h2>Quản lý liên hệ</h2>

<?php if ($action == 'list') { ?>

    <h3>Danh sách liên hệ</h3>
    <table>
        <tr>
            <th>Mã</th>
            <th>Họ tên</th>
            <th>Email</th>
            <th>Nội dung</th>
            <th>Ngày gửi</th>
            <th>Hành động</th>
        </tr>
        <?php while ($lh = $ds_lien_he->fetch_assoc()) { ?>
            <tr>
                <td><?= $lh['ma_lien_he'] ?></td>
                <td><?= htmlspecialchars($lh['ho_ten']) ?></td>
                <td><?= htmlspecialchars($lh['email']) ?></td>
                <td><?= nl2br(htmlspecialchars($lh['noi_dung'])) ?></td>
                <td><?= $lh['ngay_gui'] ?></td>
                <td>
                    <a href="index.php?page=lienhe&action=delete&id=<?= $lh['ma_lien_he'] ?>" class="btn-del">Xóa</a>
                </td>
            </tr>
        <?php } ?>
    </table>

<?php } elseif ($action == 'delete' && isset($_GET['id'])) { ?>
    <h3>Xóa liên hệ</h3>
    <form method="POST" onsubmit="return confirm('Xác nhận xóa liên hệ này?');">
        <input type="hidden" name="ma_lien_he" value="<?= $_GET['id'] ?>">
        <p>Bạn có chắc muốn xóa liên hệ #<?= $_GET['id'] ?>?</p>
        <button type="submit" name="xoa_lien_he" class="btn-del">Xóa</button>
        <a href="index.php?page=lienhe&action=list" class="btn-cancel">Không</a>
    </form>
<?php } ?>
