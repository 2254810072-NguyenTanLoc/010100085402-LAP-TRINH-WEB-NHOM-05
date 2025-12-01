<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

class dbadmin
{
    private $conn;

    public function __construct()
    {
        $this->conn = new mysqli("localhost", "root", "", "dat_phong_khach_san");
        if ($this->conn->connect_error) {
            die("Kết nối thất bại: " . $this->conn->connect_error);
        }
        $this->conn->set_charset("utf8");
    }

    public function insert($table, $data)
    {
        $cols = implode(",", array_keys($data));
        $vals = "'" . implode("','", array_values($data)) . "'";
        $sql = "INSERT INTO $table ($cols) VALUES ($vals)";
        return $this->conn->query($sql);
    }

    public function delete($table, $where)
    {
        $sql = "DELETE FROM $table WHERE $where";
        return $this->conn->query($sql);
    }

    public function update($table, $data, $where)
    {
        $updates = [];
        foreach ($data as $col => $val) {
            $updates[] = "$col = '$val'";
        }
        $updates_str = implode(", ", $updates);
        $sql = "UPDATE $table SET $updates_str WHERE $where";
        return $this->conn->query($sql);
    }

    public function getAll($table)
    {
        $sql = "SELECT * FROM $table";
        return $this->conn->query($sql);
    }

    public function getOne($table, $where)
    {
        $sql = "SELECT * FROM $table WHERE $where LIMIT 1";
        return $this->conn->query($sql)->fetch_assoc();
    }

    public function checkAdmin($ten_dang_nhap, $mat_khau)
    {
        $sql = "SELECT * FROM khachhang WHERE ten_dang_nhap='$ten_dang_nhap' AND mat_khau='$mat_khau' AND vai_tro='admin'";
        $result = $this->conn->query($sql);
        return $result->num_rows > 0;
    }

    public function checkUser($ten_dang_nhap, $mat_khau)
    {
        $sql = "SELECT mat_khau, ma_khach_hang FROM khachhang WHERE ten_dang_nhap='$ten_dang_nhap'";
        $result = $this->conn->query($sql);

        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            // So sánh password đã hash
            if (password_verify($mat_khau, $row['mat_khau'])) {
                return $row['ma_khach_hang']; // trả về id nếu muốn lưu session
            }
        }
        return false;
    }

    public function getMaKhachHang($ten_dang_nhap)
    {
        $sql = "SELECT ma_khach_hang FROM khachhang WHERE ten_dang_nhap='$ten_dang_nhap' LIMIT 1";
        $result = $this->conn->query($sql);
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return $row['ma_khach_hang'];
        }
        return null;
    }

    // Lấy thông tin khách hàng theo ID
    public function getKhachHangTheoId($ma_khach_hang)
    {
        $ma_khach_hang = $this->conn->real_escape_string($ma_khach_hang);
        $sql = "SELECT * FROM khachhang WHERE ma_khach_hang = '$ma_khach_hang' LIMIT 1";
        $result = $this->conn->query($sql);
        return $result ? $result->fetch_assoc() : false;
    }

    // Cập nhật thông tin khách hàng
    public function capNhatThongTinKhachHang($ma_khach_hang, $ho_ten, $email, $so_dien_thoai, $dia_chi)
    {
        $stmt = $this->conn->prepare("UPDATE khachhang SET ho_ten=?, email=?, so_dien_thoai=?, dia_chi=? WHERE ma_khach_hang=?");
        $stmt->bind_param("ssssi", $ho_ten, $email, $so_dien_thoai, $dia_chi, $ma_khach_hang);
        return $stmt->execute();
    }

    public function checkMatKhauCu($ma_khach_hang, $mat_khau_cu)
    {
        $ma_khach_hang = $this->conn->real_escape_string($ma_khach_hang);
        $sql = "SELECT mat_khau FROM khachhang WHERE ma_khach_hang = '$ma_khach_hang' LIMIT 1";
        $result = $this->conn->query($sql);

        if ($result && $row = $result->fetch_assoc()) {
            return password_verify($mat_khau_cu, $row['mat_khau']);
        }

        return false;
    }


    // Đổi mật khẩu
    public function doiMatKhau($ma_khach_hang, $mat_khau_moi)
    {
        $hash_moi = password_hash($mat_khau_moi, PASSWORD_DEFAULT);
        $sql = "UPDATE khachhang SET mat_khau='$hash_moi' WHERE ma_khach_hang=$ma_khach_hang";
        return $this->conn->query($sql);
    }

    public function getLastInsertId()
    {
        return $this->conn->insert_id;
    }

    public function getLichSuDatPhong($ma_khach_hang)
    {
        $sql = "SELECT dp.*, p.ten_phong 
                FROM datphong dp 
                JOIN phong p ON dp.ma_phong = p.ma_phong 
                WHERE dp.ma_khach_hang = '$ma_khach_hang' 
                ORDER BY dp.ngay_dat DESC";
        $result = $this->conn->query($sql);
        return $result;
    }

    public function getAllphong()
    {
        $sql = "SELECT * FROM phong";
        $result = $this->conn->query($sql);
        return $result;
    }

    public function timPhong($trang_thai = 'trong', $so_nguoi = 0, $ngay_den = '', $ngay_di = '')
    {
        $sql = "SELECT p.*, lp.ten_loai 
            FROM phong p 
            JOIN loaiphong lp ON p.ma_loai_phong = lp.ma_loai_phong 
            WHERE p.trang_thai = '$trang_thai'";

        if ($so_nguoi > 0) {
            $sql .= " AND p.so_nguoi_toi_da >= $so_nguoi";
        }

        if ($ngay_den && $ngay_di) {
    $sql .= " AND p.ma_phong NOT IN (
        SELECT ct.ma_phong 
        FROM chitietdatphong ct
        JOIN datphong dp ON ct.ma_dat_phong = dp.ma_dat_phong
        WHERE dp.trang_thai <> 'da_huy'
          AND NOT (dp.ngay_di <= '$ngay_den' OR dp.ngay_den >= '$ngay_di')
    )";
}


        return $this->conn->query($sql);
    }

    public function getPhongTheoLoai($loai, $ngay_den = '', $ngay_di = '')
    {
        $sql = "SELECT p.*, lp.ten_loai 
            FROM phong p 
            JOIN loaiphong lp ON p.ma_loai_phong = lp.ma_loai_phong 
            WHERE lp.ma_loai_phong = '$loai' AND p.trang_thai = 'trong'";

        if ($ngay_den && $ngay_di) {
    $sql .= " AND p.ma_phong NOT IN (
        SELECT ct.ma_phong 
        FROM chitietdatphong ct
        JOIN datphong dp ON ct.ma_dat_phong = dp.ma_dat_phong
        WHERE dp.trang_thai <> 'da_huy'
          AND NOT (dp.ngay_di <= '$ngay_den' OR dp.ngay_den >= '$ngay_di')
    )";
}


        return $this->conn->query($sql);
    }

    public function getChiTietPhong($ma_phong)
    {
        $sql = "SELECT p.*, lp.ten_loai FROM phong p JOIN loaiphong lp ON p.ma_loai_phong = lp.ma_loai_phong WHERE p.ma_phong = '$ma_phong' LIMIT 1";
        $result = $this->conn->query($sql);
        return $result ? $result->fetch_assoc() : null;
    }

    public function getDanhGiaPhong($ma_phong)
    {
        $sql = "SELECT dg.*, kh.ho_ten FROM danhgia dg JOIN khachhang kh ON dg.ma_khach_hang = kh.ma_khach_hang WHERE dg.ma_phong = '$ma_phong' ORDER BY dg.ngay_danh_gia DESC";
        $result = $this->conn->query($sql);
        return $result;
    }

    public function capNhatTrangThaiPhong($ma_phong)
    {
        $today = date('Y-m-d');
        $sql = "
    SELECT * 
    FROM chitietdatphong c
    JOIN datphong d ON c.ma_dat_phong = d.ma_dat_phong
    WHERE c.ma_phong = '$ma_phong' 
      AND d.trang_thai = 'da_xac_nhan'
      AND '$today' BETWEEN c.ngay_den AND c.ngay_di
";
        $result = $this->conn->query($sql);

        if ($result->num_rows > 0) {
            $this->update('phong', ['trang_thai' => 'da_dat'], "ma_phong = '$ma_phong'");
        } else {
            $this->update('phong', ['trang_thai' => 'trong'], "ma_phong = '$ma_phong'");
        }
    }

   public function xacNhanDatPhong($ma_dat_phong)
{
    // Cập nhật trạng thái đơn
    $this->update('datphong', ['trang_thai' => 'da_xac_nhan'], "ma_dat_phong = '$ma_dat_phong'");
    
    // Lấy tất cả phòng trong đơn
    $result = $this->query("SELECT ma_phong FROM chitietdatphong WHERE ma_dat_phong = '$ma_dat_phong'");
    
    while ($row = $result->fetch_assoc()) {
        $this->capNhatTrangThaiPhong($row['ma_phong']); // cập nhật từng phòng
    }
}

public function huyDatPhong($ma_dat_phong)
{
    $this->update('datphong', ['trang_thai' => 'da_huy'], "ma_dat_phong = '$ma_dat_phong'");
    
    $result = $this->query("SELECT ma_phong FROM chitietdatphong WHERE ma_dat_phong = '$ma_dat_phong'");
    
    while ($row = $result->fetch_assoc()) {
        $this->capNhatTrangThaiPhong($row['ma_phong']);
    }
}

public function traSomPhong($ma_dat_phong)
{
    $this->update('datphong', ['trang_thai' => 'tra_som'], "ma_dat_phong = '$ma_dat_phong'");
    
    $result = $this->query("SELECT ma_phong FROM chitietdatphong WHERE ma_dat_phong = '$ma_dat_phong'");
    
    while ($row = $result->fetch_assoc()) {
        $this->update('phong', ['trang_thai' => 'trong'], "ma_phong = '{$row['ma_phong']}'");
    }
}


    public function getAllDatPhong()
    {
        $sql = "SELECT dp.*, kh.ho_ten, kh.email, kh.so_dien_thoai
            FROM datphong dp
            JOIN khachhang kh ON dp.ma_khach_hang = kh.ma_khach_hang
            ORDER BY dp.ma_dat_phong DESC";
        return $this->conn->query($sql);
    }

    public function getPhongTheoDatPhong($ma_dat_phong)
    {
        $sql = "SELECT ct.*, p.ten_phong, p.anh_phong, p.gia
            FROM chitietdatphong ct
            JOIN phong p ON ct.ma_phong = p.ma_phong
            WHERE ct.ma_dat_phong = $ma_dat_phong";
        return $this->conn->query($sql);
    }


    public function getOrCreateGioHang($ma_khach_hang)
    {
        $sql = "SELECT ma_gio_hang FROM giohang WHERE ma_khach_hang = '$ma_khach_hang'";
        $result = $this->conn->query($sql);
        if ($result && $row = $result->fetch_assoc()) {
            return (int)$row['ma_gio_hang'];
        }

        $sql = "INSERT INTO giohang (ma_khach_hang, tong_tien) VALUES ('$ma_khach_hang', 0)";
        $this->conn->query($sql);
        return $this->conn->insert_id;
    }

    public function themChiTietGioHang($ma_khach_hang, $ma_phong, string $ngay_den, string $ngay_di): bool
    {
        if (!$this->isValidDate($ngay_den) || !$this->isValidDate($ngay_di)) {
            return false;
        }

        $tz = new DateTimeZone('Asia/Ho_Chi_Minh');
        $today = new DateTime('today', $tz);
        $den = DateTime::createFromFormat('Y-m-d', $ngay_den, $tz);
        $di  = DateTime::createFromFormat('Y-m-d', $ngay_di, $tz);

        if (!$den || !$di) {
            return false;
        }

        if ($den < $today || $di <= $den) {
            return false;
        }

        $sql = "SELECT gia FROM phong WHERE ma_phong = '$ma_phong' LIMIT 1";
        $phong = $this->conn->query($sql)->fetch_assoc();
        if (!$phong) return false;

        $ma_gio_hang = $this->getOrCreateGioHang($ma_khach_hang);

        $sql_check = "
        SELECT 1 
        FROM chitietgiohang 
        WHERE ma_gio_hang = '$ma_gio_hang' 
          AND ma_phong = '$ma_phong'
          AND NOT (ngay_di <= '$ngay_den' OR ngay_den >= '$ngay_di')
        LIMIT 1
    ";
        $result_check = $this->conn->query($sql_check);
        if ($result_check && $result_check->num_rows > 0) {
            return false;
        }

        $days = $this->tinhSoNgay($ngay_den, $ngay_di);
        if ($days <= 0) {
            return false;
        }

        $don_gia = (float)$phong['gia'];
        $thanh_tien = $don_gia * $days;

        $sql = "
        INSERT INTO chitietgiohang (ma_gio_hang, ma_phong, ngay_den, ngay_di, don_gia, thanh_tien)
        VALUES ('$ma_gio_hang', '$ma_phong', '$ngay_den', '$ngay_di', '$don_gia', '$thanh_tien')
    ";
        $this->conn->query($sql);

        $this->capNhatTongTienGioHang($ma_gio_hang);

        return true;
    }

    public function capNhatTongTienGioHang($ma_gio_hang)
    {
        $sql = "SELECT SUM(thanh_tien) AS tong
            FROM chitietgiohang
            WHERE ma_gio_hang = '$ma_gio_hang'";
        $result = $this->query($sql);

        $tong = 0;
        if ($result && $row = $result->fetch_assoc()) {
            $tong = (float)($row['tong'] ?? 0);
        }

        $this->update("giohang", ["tong_tien" => $tong], "ma_gio_hang = '$ma_gio_hang'");

        return $tong;
    }

    public function layTongTienTheoKhach($ma_khach_hang)
    {
        $sql = "SELECT tong_tien 
            FROM giohang 
            WHERE ma_khach_hang = '$ma_khach_hang' 
            LIMIT 1";
        $result = $this->conn->query($sql);

        if ($result && $row = $result->fetch_assoc()) {
            return (float)$row['tong_tien'];
        }

        return 0;
    }


    public function layChiTietGioHangTheoKhach(int $ma_khach_hang)
    {
        $sql = "
            SELECT ct.*, p.ten_phong, p.anh_phong
            FROM chitietgiohang ct
            JOIN giohang g ON ct.ma_gio_hang = g.ma_gio_hang
            JOIN phong p ON ct.ma_phong = p.ma_phong
            WHERE g.ma_khach_hang = '$ma_khach_hang'
            ORDER BY ct.ma_chi_tiet DESC
        ";
        return $this->conn->query($sql);
    }

    public function tinhTien($ngay_den, $ngay_di, $gia)
    {
        $start = new DateTime($ngay_den);
        $end = new DateTime($ngay_di);
        $days = $start->diff($end)->days;
        if ($days == 0) $days = 1;
        return $days * $gia;
    }
    public function query($sql)
    {
        return $this->conn->query($sql);
    }

    public function layChiTietGioHangTheoId($ma_chi_tiet)
    {
        $ma_chi_tiet = $this->conn->real_escape_string($ma_chi_tiet);
        $sql = "SELECT ct.*, p.ten_phong, p.anh_phong
            FROM chitietgiohang ct
            JOIN phong p ON ct.ma_phong = p.ma_phong
            WHERE ct.ma_chi_tiet='$ma_chi_tiet' LIMIT 1";
        $result = $this->conn->query($sql);
        return $result ? $result->fetch_assoc() : null;
    }

    public function xoaChiTietGioHang($ma_chi_tiet, $ma_khach_hang)
    {
        $ma_chi_tiet = $this->conn->real_escape_string($ma_chi_tiet);
        $ma_khach_hang = $this->conn->real_escape_string($ma_khach_hang);

        $sql = "
        DELETE ct
        FROM chitietgiohang ct
        JOIN giohang g ON ct.ma_gio_hang = g.ma_gio_hang
        WHERE ct.ma_chi_tiet = '$ma_chi_tiet' AND g.ma_khach_hang = '$ma_khach_hang'
    ";
        return $this->conn->query($sql);
    }

    public function getAffectedRows()
    {
        return $this->conn->affected_rows;
    }

    public function xoaGioHangTheoKhach($ma_khach_hang)
    {
        $ma_khach_hang = $this->conn->real_escape_string($ma_khach_hang);
        $this->conn->query("DELETE FROM chitietgiohang WHERE ma_gio_hang = (SELECT ma_gio_hang FROM giohang WHERE ma_khach_hang='$ma_khach_hang' LIMIT 1)");
    }

    public function getLichSuDatPhongChiTiet($ma_khach_hang)
    {
        $sql = "SELECT 
                dp.ma_dat_phong, dp.ngay_dat, dp.tong_tien, dp.trang_thai,
                ct.ma_phong, ct.ngay_den AS ct_ngay_den, ct.ngay_di AS ct_ngay_di,
                ct.thanh_tien AS ct_thanh_tien,
                p.ten_phong, p.gia, p.anh_phong
            FROM datphong dp
            JOIN chitietdatphong ct ON dp.ma_dat_phong = ct.ma_dat_phong
            JOIN phong p ON ct.ma_phong = p.ma_phong
            WHERE dp.ma_khach_hang = '" . intval($ma_khach_hang) . "'
            ORDER BY dp.ma_dat_phong DESC, ct.ma_ct_dat_phong ASC";
        return $this->conn->query($sql);
    }

    public function layChiTietNhieuCTGH(array $listCT, int $ma_khach_hang)
    {
        // Chuyển mảng id thành chuỗi 1,2,3,...
        $ids = implode(',', array_map('intval', $listCT));

        $sql = "
        SELECT 
            ct.ma_chi_tiet,
            ct.ma_phong,
            ct.ngay_den,
            ct.ngay_di,
            ct.don_gia,
            p.ten_phong,
            p.anh_phong,
            lp.ten_loai
        FROM chitietgiohang ct
        JOIN giohang g ON ct.ma_gio_hang = g.ma_gio_hang
        JOIN phong p ON ct.ma_phong = p.ma_phong
        JOIN loaiphong lp ON p.ma_loai_phong = lp.ma_loai_phong
        WHERE ct.ma_chi_tiet IN ($ids)
        AND g.ma_khach_hang = $ma_khach_hang
    ";

        $result = $this->conn->query($sql);

        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }

        return $data; // trả về danh sách phòng
    }


    public function tinhSoNgay($ngay_den, $ngay_di)
    {
        try {
            $start = new DateTime($ngay_den);
            $end = new DateTime($ngay_di);
            return max(1, $start->diff($end)->days);
        } catch (Exception $e) {
            return 1;
        }
    }

    // Tổng số phòng
public function thongKeTongPhong() {
    $sql = "SELECT COUNT(*) AS tong FROM phong";
    return $this->query($sql)->fetch_assoc()['tong'];
}

// Phòng trống
public function thongKePhongTrong() {
    $sql = "SELECT COUNT(*) as phong_trong
            FROM phong
            WHERE ma_phong NOT IN (
                SELECT DISTINCT p.ma_phong
                FROM phong p
                JOIN chitietdatphong ct ON p.ma_phong = ct.ma_phong
                JOIN datphong dp ON ct.ma_dat_phong = dp.ma_dat_phong
                WHERE dp.trang_thai <> 'da_huy'
            )";
    $result = $this->conn->query($sql)->fetch_assoc();
    return $result['phong_trong'] ?? 0;
}


// Phòng đã đặt (chỉ tính phòng đã xác nhận)
public function thongKePhongDaDat() {
    $sql = "SELECT COUNT(DISTINCT p.ma_phong) as da_dat
            FROM phong p
            JOIN chitietdatphong ct ON p.ma_phong = ct.ma_phong
            JOIN datphong dp ON ct.ma_dat_phong = dp.ma_dat_phong
            WHERE dp.trang_thai <> 'da_huy'"; // kể cả chưa xác nhận
    $result = $this->conn->query($sql)->fetch_assoc();
    return $result['da_dat'] ?? 0;
}


// Tổng doanh thu (chỉ tính đơn đã xác nhận)
public function tongDoanhThu() {
    $sql = "SELECT SUM(tong_tien) AS tong
            FROM datphong
            WHERE trang_thai='da_xac_nhan'";
    $row = $this->query($sql)->fetch_assoc();
    return $row['tong'] ?? 0;
}

// Doanh thu theo tháng (chi tiết: tổng tiền từng tháng, từng năm, chỉ đơn đã xác nhận)
public function doanhThuTheoThang() {
    $sql = "SELECT YEAR(ngay_dat) AS nam, MONTH(ngay_dat) AS thang, SUM(tong_tien) AS doanh_thu
            FROM datphong
            WHERE trang_thai='da_xac_nhan'
            GROUP BY YEAR(ngay_dat), MONTH(ngay_dat)
            ORDER BY YEAR(ngay_dat), MONTH(ngay_dat)";
    return $this->query($sql);
}

// Doanh thu theo khoảng ngày (lọc đơn đã xác nhận)
public function doanhThuTheoKhoangNgay($tu_ngay, $den_ngay) {
    $tu_ngay = $this->conn->real_escape_string($tu_ngay);
    $den_ngay = $this->conn->real_escape_string($den_ngay);
    $sql = "SELECT dp.*, kh.ho_ten
            FROM datphong dp
            JOIN khachhang kh ON dp.ma_khach_hang=kh.ma_khach_hang
            WHERE dp.trang_thai='da_xac_nhan'
              AND dp.ngay_dat BETWEEN '$tu_ngay' AND '$den_ngay'
            ORDER BY dp.ngay_dat DESC";
    return $this->query($sql);
}

// Tỷ lệ phòng trống vs đã đặt
public function tiLePhong() {
    $trong = $this->thongKePhongTrong();
    $dat = $this->thongKePhongDaDat();
    return ['phong_trong'=>$trong,'phong_da_dat'=>$dat];
}


    public function isValidDate($date)
    {
        $d = DateTime::createFromFormat('Y-m-d', $date);
        return $d && $d->format('Y-m-d') === $date;
    }
}
