class CartCheckout {
  constructor(products, total) {
    this.products = products;
    this.total = total;
  }

  confirm() {
    let message = "🧾 XÁC NHẬN THANH TOÁN\n\n";

    this.products.forEach((p) => {
      message += `• ${p.name} | SL: ${p.qty} | ${p.sum} VNĐ\n`;
    });

    message += "\n-----------------------\n";
    message += "TỔNG TIỀN: " + this.total + " VNĐ\n\n";
    message += "Bạn có chắc chắn muốn thanh toán không?";

    return confirm(message);
  }
}

function checkoutCart() {
  if (!checkout.confirm()) return;

  fetch("cart.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/x-www-form-urlencoded",
    },
    body: "ajax_checkout=1",
  })
    .then((res) => res.json())
    .then((data) => {
      if (data.status === "success") {
        alert("✅ Thanh toán thành công!");
        location.reload(); // load lại để giỏ trống
      } else {
        alert("❌ " + data.msg);
      }
    })
    .catch(() => {
      alert("❌ Lỗi kết nối máy chủ");
    });
}
