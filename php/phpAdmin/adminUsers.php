<?php
session_start();
include "../connect.php";

/* CHỈ ADMIN ĐƯỢC VÀO */
if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "admin") {
    header("Location: ../phpAccount/login.php");
    exit;
}

/* XỬ LÝ XÓA USER */
if (isset($_GET["delete"])) {
    $id = (int) $_GET["delete"];

    // Không cho admin tự xóa mình
    if ($id !== $_SESSION["id"]) {
        $sql = "DELETE FROM users WHERE id = ? AND role = 'user'";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
    }

    header("Location: adminUsers.php");
    exit;
}
$result = $conn->query("SELECT id, username, name, email, role FROM users WHERE role = 'user'");
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Quản lý người dùng</title>
    <link rel="stylesheet" href="../../css/adminUsers.css">
</head>

<body>

    <div class="layout">

        <!-- SIDEBAR -->
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

        <!-- CONTENT -->
        <main class="content">

            <!-- TOPBAR -->
            <header class="topbar">
                <h1>QUẢN LÝ NGƯỜI DÙNG</h1>
                <div class="account">
                    👤
                    <?= htmlspecialchars($_SESSION["username"]) ?> |
                    <a href="../phpAccount/logout.php">Đăng xuất</a>
                </div>
            </header>

            <!-- TABLE USERS -->
            <table>
                <tr>
                    <th>Tên đăng nhập</th>
                    <th>Họ tên</th>
                    <th>Email</th>
                    <th>Quyền</th>
                    <th>Mật khẩu</th>
                    <th>Hành động</th>
                </tr>

                <?php while ($row = $result->fetch_assoc()) { ?>
                    <tr>
                        <td><?= htmlspecialchars($row["username"]) ?></td>
                        <td><?= htmlspecialchars($row["name"]) ?></td>
                        <td><?= htmlspecialchars($row["email"]) ?></td>
                        <td><?= $row["role"] ?></td>
                        <td>******</td>
                        <td>
                            <?php if ($row["role"] !== "admin") { ?>
                                <a
                                    class="btn-delete"
                                    href="?delete=<?= $row["id"] ?>"
                                    onclick="return confirm('Bạn có chắc muốn xóa tài khoản này?')">
                                    Xóa
                                </a>
                            <?php } ?>
                        </td>
                    </tr>
                <?php } ?>
            </table>

        </main>

    </div>

</body>

</html>