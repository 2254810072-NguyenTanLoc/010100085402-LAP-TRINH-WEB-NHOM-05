<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Liên hệ - Khách sạn Baby Cute</title>
    <link rel="stylesheet" href="css/lienhe.css">
    <style>
        .contact-container {
            max-width: 900px;
            margin: 40px auto;
            padding: 30px;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 6px 20px rgba(0,0,0,0.1);
        }
        .contact-container h2 {
            color: #1976d2;
            margin-bottom: 20px;
            border-left: 5px solid #1976d2;
            padding-left: 10px;
        }
        .contact-info {
            margin-bottom: 30px;
        }
        .contact-info p {
            margin: 8px 0;
            font-size: 15px;
            color: #333;
        }
        .contact-form label {
            display: block;
            margin-bottom: 6px;
            font-weight: 600;
            color: #444;
        }
        .contact-form input,
        .contact-form textarea {
            width: 100%;
            padding: 10px 14px;
            margin-bottom: 16px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 14px;
            box-sizing: border-box;
        }
        .contact-form button {
            background: linear-gradient(135deg, #1976d2, #0d47a1);
            color: #fff;
            border: none;
            padding: 12px 20px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .contact-form button:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(25,118,210,0.3);
        }
    </style>
</head>
<body>
    <?php include 'includes/navbar.php' ?>
    <div class="contact-container">
        <h2>Liên hệ với chúng tôi</h2>
        <div class="contact-info">
            <p><b>Địa chỉ:</b> 123 Đường Biển, Quận 1, TP.HCM</p>
            <p><b>Điện thoại:</b> 0123 456 789</p>
            <p><b>Email:</b> info@babycutehotel.com</p>
        </div>

        <form class="contact-form" method="post" action="guilienhe.php">
            <label for="name">Họ và tên:</label>
            <input type="text" id="name" name="name" required>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>

            <label for="message">Nội dung:</label>
            <textarea id="message" name="message" rows="5" required></textarea>

            <button type="submit">Gửi liên hệ</button>
        </form>
    </div>
</body>
</html>
