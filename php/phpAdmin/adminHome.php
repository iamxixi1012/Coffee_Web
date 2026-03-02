<?php
session_start();
include "../connect.php";

if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "admin") {
    header("Location: ../phpAccount/login.php");
    exit;
}
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
                <li><a href="products.php">Quản lý sản phẩm</a></li>
                <li><a href="orders.php">Quản lý hóa đơn</a></li>
                <li><a href="statistics.php">Thống kê</a></li>
            </ul>
        </aside>

        <!-- NỘI DUNG -->
        <main class="content">

            <!-- TOP BAR -->
            <header class="topbar">
                <h1>TRANG CHỦ</h1>

                <div class="account">
                    👤
                    <?= htmlspecialchars($_SESSION["username"]) ?>
                    |
                    <a href="../phpAccount/logout.php">Đăng xuất</a>
                </div>
            </header>

            <!-- THỐNG KÊ -->
            <section class="welcome">
                <h2>Xin chào, <?= htmlspecialchars($_SESSION["name"]) ?> 👋</h2>
            </section>

        </main>

    </div>

</body>

</html>