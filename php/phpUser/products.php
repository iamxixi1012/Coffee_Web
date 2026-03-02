<?php
session_start();
include "../connect.php";

/* ===== CHỈ USER ===== */
if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "user") {
    header("Location: ../phpAccount/login.php");
    exit;
}

/* =====================================================
   XỬ LÝ AJAX: THÊM GIỎ / MUA NGAY (KHÔNG NHẢY TRANG)
===================================================== */
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    header("Content-Type: application/json");

    $id   = (int)$_POST["product_id"];
    $qty  = (int)$_POST["quantity"];
    $type = $_POST["type"] ?? "";

    $rs = mysqli_query($conn, "
        SELECT name, price, quantity 
        FROM products 
        WHERE id = $id
    ");
    $p = mysqli_fetch_assoc($rs);

    if (!$p) {
        echo json_encode([
            "status" => "error",
            "msg" => "Sản phẩm không tồn tại"
        ]);
        exit;
    }

    if ($qty <= 0 || $qty > $p["quantity"]) {
        echo json_encode([
            "status" => "error",
            "msg" => "Số lượng không hợp lệ hoặc vượt tồn kho"
        ]);
        exit;
    }

    /* ===== THÊM GIỎ ===== */
    if ($type === "cart") {

        $_SESSION["cart"][$id] = ($_SESSION["cart"][$id] ?? 0) + $qty;

        echo json_encode([
            "status" => "success",
            "msg" => "🛒 Đã thêm vào giỏ hàng"
        ]);
        exit;
    }

    /* ===== MUA NGAY ===== */
    if ($type === "buy") {

        $userId = $_SESSION["user_id"];
        $total  = $p["price"] * $qty;

        mysqli_query($conn, "
            INSERT INTO user_invoices
            (user_id, product_name, quantity, total_price, created_at)
            VALUES (
                $userId,
                '".mysqli_real_escape_string($conn, $p["name"])."',
                $qty,
                $total,
                NOW()
            )
        ");

        mysqli_query($conn, "
            UPDATE products
            SET quantity = quantity - $qty
            WHERE id = $id
        ");

        echo json_encode([
            "status" => "success",
            "msg" => "✅ Mua hàng thành công"
        ]);
        exit;
    }

    echo json_encode([
        "status" => "error",
        "msg" => "Thao tác không hợp lệ"
    ]);
    exit;
}

/* ================= TÌM KIẾM ================= */
$search = $_GET["search"] ?? "";
$sql = "SELECT * FROM products WHERE status='active'";
if ($search !== "") {
    $sql .= " AND name LIKE '%" . mysqli_real_escape_string($conn, $search) . "%'";
}
$products = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Sản phẩm</title>

    <link rel="stylesheet" href="../../css/home.css">
    <link rel="stylesheet" href="../../css/products.css">
</head>
<body>

<div class="layout">

    <!-- SIDEBAR -->
    <div class="sidebar">
        <h2>☕ COFFEE SHOP</h2>
        <ul>
            <li><a href="userHome.php">Trang chủ</a></li>
            <li class="active"><a href="products.php">Sản phẩm</a></li>
            <li><a href="cart.php">Giỏ hàng</a></li>
            <li><a href="invoice.php">Hóa đơn</a></li>
            <li><a href="personalProfile.php">Hồ sơ cá nhân</a></li>
        </ul>
    </div>

    <!-- CONTENT -->
    <div class="content">

        <div class="topbar">
            <h1>SẢN PHẨM</h1>
            <div class="account">
                👤 <b><?= htmlspecialchars($_SESSION["name"]) ?></b> |
                <a href="../phpAccount/logout.php">Đăng xuất</a>
            </div>
        </div>

        <div class="welcome">

            <!-- SEARCH -->
            <form method="get" style="margin-bottom:20px">
                <input type="text"
                       name="search"
                       placeholder="Tìm sản phẩm..."
                       value="<?= htmlspecialchars($search) ?>">
                <button type="submit">Tìm</button>
            </form>

            <!-- PRODUCT GRID -->
            <div class="product-grid">

                <?php while ($p = mysqli_fetch_assoc($products)):

                    $img = (!empty($p["image"]) && file_exists("../../uploads/products/".$p["image"]))
                        ? "../../uploads/products/".$p["image"]
                        : "../../images/no-image.png";

                    $stock = (int)$p["quantity"];
                ?>

                <div class="product-card">

                    <img src="<?= $img ?>" class="product-image">

                    <h3><?= htmlspecialchars($p["name"]) ?></h3>

                    <p class="desc">
                        <?= htmlspecialchars($p["description"]) ?>
                    </p>

                    <div class="price">
                        <?= number_format($p["price"]) ?> VNĐ
                    </div>

                    <p class="stock">
                        Còn lại: <b><?= $stock ?></b> sản phẩm
                    </p>

                    <?php if ($stock > 0): ?>
                        <div class="product-actions">

                            <input type="number"
                                   class="qty"
                                   value="1"
                                   min="1"
                                   max="<?= $stock ?>">

                            <button class="btn-cart"
                                    onclick="addToCart(<?= $p['id'] ?>, this)">
                                🛒 Thêm giỏ
                            </button>

                            <button class="btn-buy"
                                    onclick="buyNow(<?= $p['id'] ?>, this)">
                                💳 Mua ngay
                            </button>

                        </div>
                    <?php else: ?>
                        <p class="out-stock">❌ Tạm hết hàng</p>
                    <?php endif; ?>

                </div>

                <?php endwhile; ?>

            </div>
        </div>
    </div>
</div>

<!-- JS -->
<script src="../../js/products.js"></script>

</body>
</html>