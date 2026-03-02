<?php
session_start();
include "../connect.php";

if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "user") {
    header("Location: ../phpAccount/login.php");
    exit;
}

$userId = $_SESSION["user_id"];

$invoices = mysqli_query($conn, "
    SELECT product_name, quantity, total_price, created_at
    FROM user_invoices
    WHERE user_id = $userId
    ORDER BY created_at DESC
");
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Hóa đơn</title>
    <link rel="stylesheet" href="../../css/home.css">
</head>
<body>

<div class="layout">
    <div class="sidebar">
        <h2>☕COFFEE SHOP</h2>
        <ul>
            <li><a href="userHome.php">Trang chủ</a></li>
            <li><a href="products.php">Sản phẩm</a></li>
            <li><a href="cart.php">Giỏ hàng</a></li>
            <li class="active"><a href="invoice.php">Hóa đơn</a></li>
            <li><a href="personalProfile.php">Hồ sơ cá nhân</a></li>
        </ul>
    </div>

    <div class="content">
        <div class="topbar">
            <h1>HÓA ĐƠN</h1>
        </div>

        <div class="welcome">

            <?php if (isset($_GET["success"])) { ?>
                <p style="color:green;">✔ Thanh toán thành công</p>
            <?php } ?>

            <?php if (mysqli_num_rows($invoices) == 0) { ?>
                <p>Chưa có hóa đơn.</p>
            <?php } else { ?>
                <table border="1" width="100%" cellpadding="10">
                    <tr>
                        <th>Sản phẩm</th>
                        <th>Số lượng</th>
                        <th>Tổng tiền</th>
                        <th>Ngày mua</th>
                    </tr>

                    <?php while ($row = mysqli_fetch_assoc($invoices)) { ?>
                    <tr>
                        <td><?= htmlspecialchars($row["product_name"]) ?></td>
                        <td><?= $row["quantity"] ?></td>
                        <td><?= number_format($row["total_price"]) ?> VNĐ</td>
                        <td><?= $row["created_at"] ?></td>
                    </tr>
                    <?php } ?>
                </table>
            <?php } ?>

        </div>
    </div>
</div>

</body>
</html>
