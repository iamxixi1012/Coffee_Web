<?php
session_start();
include "../connect.php";

/* CHỈ USER */
if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "user") {
    header("Location: ../phpAccount/login.php");
    exit;
}

$userId = $_SESSION["user_id"];
$cart = $_SESSION["cart"] ?? [];

/* ================= AJAX CHECKOUT ================= */
if (isset($_POST["ajax_checkout"])) {

    header("Content-Type: application/json");

    if (empty($cart)) {
        echo json_encode([
            "status" => "error",
            "msg" => "Giỏ hàng trống"
        ]);
        exit;
    }

    mysqli_begin_transaction($conn);

    try {
        foreach ($cart as $productId => $qty) {

            $rs = mysqli_query($conn, "
                SELECT name, price, quantity
                FROM products
                WHERE id = $productId
                FOR UPDATE
            ");
            $p = mysqli_fetch_assoc($rs);

            if (!$p || $qty > $p["quantity"]) {
                throw new Exception("{$p['name']} không đủ tồn kho");
            }

            $totalPrice = $p["price"] * $qty;

            // LƯU HÓA ĐƠN
            mysqli_query($conn, "
                INSERT INTO user_invoices
                (user_id, product_name, quantity, total_price, created_at)
                VALUES (
                    $userId,
                    '" . mysqli_real_escape_string($conn, $p["name"]) . "',
                    $qty,
                    $totalPrice,
                    NOW()
                )
            ");

            // TRỪ KHO
            mysqli_query($conn, "
                UPDATE products
                SET quantity = quantity - $qty
                WHERE id = $productId
            ");
        }

        unset($_SESSION["cart"]);
        mysqli_commit($conn);

        echo json_encode([
            "status" => "success"
        ]);
        exit;
    } catch (Exception $e) {
        mysqli_rollback($conn);

        echo json_encode([
            "status" => "error",
            "msg" => $e->getMessage()
        ]);
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Giỏ hàng</title>
    <link rel="stylesheet" href="../../css/home.css">
    <link rel="stylesheet" href="../../css/cart.css">
</head>

<body>

    <div class="layout">

        <!-- SIDEBAR -->
        <div class="sidebar">
            <h2>☕ COFFEE SHOP</h2>
            <ul>
                <li><a href="userHome.php">Trang chủ</a></li>
                <li><a href="products.php">Sản phẩm</a></li>
                <li class="active"><a href="cart.php">Giỏ hàng</a></li>
                <li><a href="invoice.php">Hóa đơn</a></li>
                <li><a href="personalProfile.php">Hồ sơ cá nhân</a></li>
            </ul>
        </div>

        <!-- CONTENT -->
        <div class="content">

            <div class="topbar">
                <h1>GIỎ HÀNG</h1>
                <div class="account">
                    👤 <b><?= htmlspecialchars($_SESSION["name"]) ?></b>
                </div>
            </div>

            <div class="welcome">

                <?php if (empty($cart)) { ?>

                    <p>🛒 Giỏ hàng trống</p>

                <?php } else { ?>

                    <table border="1" width="100%" cellpadding="10">
                        <tr>
                            <th>Sản phẩm</th>
                            <th>Số lượng</th>
                            <th>Giá</th>
                            <th>Tổng</th>
                        </tr>

                        <?php
                        $grandTotal = 0;
                        $jsProducts = [];

                        foreach ($cart as $id => $qty) {
                            $rs = mysqli_query($conn, "SELECT name, price FROM products WHERE id = $id");
                            $p = mysqli_fetch_assoc($rs);

                            $sum = $p["price"] * $qty;
                            $grandTotal += $sum;

                            $jsProducts[] = [
                                "name" => $p["name"],
                                "qty"  => $qty,
                                "sum"  => number_format($sum)
                            ];
                        ?>
                            <tr>
                                <td><?= htmlspecialchars($p["name"]) ?></td>
                                <td><?= $qty ?></td>
                                <td><?= number_format($p["price"]) ?> VNĐ</td>
                                <td><?= number_format($sum) ?> VNĐ</td>
                            </tr>
                        <?php } ?>

                        <tr>
                            <td colspan="3"><b>TỔNG TIỀN</b></td>
                            <td><b><?= number_format($grandTotal) ?> VNĐ</b></td>
                        </tr>
                    </table>

                    <br>

                    <!-- NÚT THANH TOÁN -->
                    <button type="button" onclick="checkoutCart()">
                        ✔ Thanh toán
                    </button>

                <?php } ?>

            </div>
        </div>
    </div>

    <!-- JS -->
    <script src="../../js/cart.js"></script>
    <script>
        const products = <?= json_encode($jsProducts ?? []) ?>;
        const total = "<?= number_format($grandTotal ?? 0) ?>";

        const checkout = new CartCheckout(products, total);
    </script>

</body>

</html>