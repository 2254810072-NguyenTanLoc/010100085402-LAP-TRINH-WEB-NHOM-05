

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Giới thiệu - Baby Cute Hotel</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "Segoe UI", sans-serif;
        }

        body {
            background: #f9f9f9;
            color: #333;
        }

        header {
            background: url('images/bgimg7.jpg') center/cover no-repeat;
            height: 60vh;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: #fff;
        }

        header::after {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.45);
        }

        header .content {
            position: relative;
            z-index: 2;
        }

        header h1 {
            font-size: 48px;
            font-weight: 700;
            margin-bottom: 10px;
        }

        header p {
            font-size: 20px;
            opacity: 0.9;
        }

        .section {
            padding: 80px 10%;
        }

        .section h2 {
            font-size: 34px;
            margin-bottom: 20px;
            text-align: center;
            font-weight: 700;
        }

        .section p {
            font-size: 18px;
            line-height: 1.6;
            text-align: center;
            max-width: 800px;
            margin: auto;
            margin-bottom: 40px;
            opacity: .9;
        }

        .services {
            display: flex;
            gap: 25px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .service-box {
            background: #fff;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            width: 300px;
            text-align: center;
            transition: .3s;
        }

        .service-box:hover {
            transform: translateY(-5px);
        }

        .service-box img {
            width: 65px;
            margin-bottom: 15px;
        }

        .service-box h3 {
            margin-bottom: 10px;
            font-size: 22px;
        }

        .gallery {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
            margin-top: 40px;
        }

        .gallery img {
            width: 100%;
            height: 220px;
            object-fit: cover;
            border-radius: 12px;
            box-shadow: 0 3px 12px rgba(0, 0, 0, .15);
            transition: .3s;
        }

        .gallery img:hover {
            transform: scale(1.03);
        }

        footer {
            padding: 30px;
            text-align: center;
            background: #222;
            color: #fff;
            margin-top: 60px;
            font-size: 15px;
        }

        .features {
            display: flex;
            gap: 20px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .feature-box {
            background: #fff;
            padding: 25px;
            border-radius: 10px;
            width: 300px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            text-align: center;
            transition: 0.3s;
        }

        .feature-box:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }

        .feature-box img {
            width: 60px;
            margin-bottom: 15px;
        }
    </style>
</head>

<body>
    <?php include 'includes/navbar.php'; ?>
    <header>
        <div class="content">
            <h1>Chào mừng đến với Baby Cute Hotel</h1>
            <p>Thiên đường nghỉ dưỡng giữa lòng biển xanh</p>
        </div>
    </header>

    <section class="section">
        <h2>Về chúng tôi</h2>
        <p>Baby Cute Hotel tự hào mang đến cho quý khách trải nghiệm nghỉ dưỡng cao cấp với dịch vụ chuẩn 5 sao, không gian sang trọng và đội ngũ nhân viên chu đáo. Với vị trí đắc địa sát biển, khách sạn chúng tôi là nơi lý tưởng để tận hưởng kỳ nghỉ tuyệt vời.</p>

        <div class="services">
            <div class="features">
                <div class="feature-box">
                    <img src="images/room.png" alt="">
                    <h3>Quản lý phòng</h3>
                    <p>Kiểm soát tình trạng phòng, giá phòng, loại phòng dễ dàng.</p>
                </div>

                <div class="feature-box">
                    <img src="images/booking.png" alt="">
                    <h3>Đặt phòng trực tuyến</h3>
                    <p>Khách hàng có thể đặt phòng nhanh chóng mọi lúc mọi nơi.</p>
                </div>

                <div class="feature-box">
                    <img src="images/payment-method.png" alt="">
                    <h3>Thanh toán tiện lợi</h3>
                    <p>Hỗ trợ thanh toán nhanh, minh bạch, nhiều phương thức.</p>
                </div>
            </div>
    </section>

    <section class="section">
        <h2>Không gian khách sạn</h2>
        <p>Mang đến sự thoải mái, sang trọng và không gian thư giãn tuyệt đối.</p>

        <div class="gallery">
            <img src="images/bgimg4.jpeg" alt="Hotel view" />
            <img src="images/bgimg6.jpeg" alt="Room" />
            <img src="uploads/roomvip1.jpg" alt="Luxury Room" />
            <img src="uploads/roomvip2.jpg" alt="Pool area" />
        </div>
    </section>

    <footer>
        © 2025 Baby Cute Hotel | Đẳng cấp - Sang trọng - Tinh tế
    </footer>
</body>

</html>