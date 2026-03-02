<?php
session_start();
include "../connect.php";

if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "admin") {
    header("Location: ../phpAccount/login.php");
    exit;
}

// THỐNG KÊ TỔNG USER
$sqlUser = "SELECT COUNT(*) AS total FROM users WHERE role = 'user'";
$resultUser = mysqli_query($conn, $sqlUser);
$rowUser = mysqli_fetch_assoc($resultUser);
$totalUser = $rowUser["total"];
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Trang quản trị</title>
    <link rel="stylesheet" href="../../css/home.css">
</head>

<body>

    <div class="layout">

        <!-- NAV BÊN TRÁI -->
        <aside class="sidebar">
            <h2>☕ADMIN</h2>
            <ul>
                <li><a href="adminHome.php">Trang chủ</a></li>
                <li><a href="adminUsers.php">Quản lý người dùng</a></li>
                <li><a href="categories.php">Quản lý danh mục sản phẩm</a></li>
                <li><a href="products.php" class="active">Quản lý sản phẩm</a></li>
                <li><a href="orders.php">Quản lý hóa đơn</a></li>
                <li><a href="statistics.php">Thống kê</a></li>
            </ul>
        </aside>

        <!-- NỘI DUNG -->
        <main class="content">

            <!-- TOP BAR -->
            <header class="topbar">
                <h1>THỐNG KÊ</h1>

                <div class="account">
                    👤
                    <?= htmlspecialchars($_SESSION["username"]) ?>
                    |
                    <a href="../phpAccount/logout.php">Đăng xuất</a>
                </div>
            </header>

            <!-- THỐNG KÊ -->
            <section class="welcome">
                <h3>Thống kê hệ thống</h3>
                <br>
                <p>👤 Tổng người dùng: <b><?= $totalUser ?></b></p>
                <br>
                <p>☕ Tổng sản phẩm: <b>Đang cập nhật</b></p>
                <br>
                <p>🧾 Tổng hóa đơn: <b>Đang cập nhật</b></p>
            </section>

        </main>

    </div>

</body>

</html>