function addToCart(id, btn) {
  const card = btn.closest(".product-card");
  const name = card.querySelector("h3").innerText;
  const price = parseInt(
    card.querySelector(".price").innerText.replace(/\D/g, "")
  );
  const qty = parseInt(card.querySelector(".qty").value);
  const total = price * qty;

  if (
    !confirm(
      `🛒 THÊM GIỎ HÀNG\n\n` +
        `Sản phẩm: ${name}\n` +
        `Số lượng: ${qty}\n` +
        `Tổng tiền: ${total.toLocaleString()} VNĐ`
    )
  )
    return;

  fetch("products.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: `product_id=${id}&quantity=${qty}&type=cart`,
  })
    .then((res) => res.json())
    .then((data) => alert(data.msg));
}

function buyNow(id, btn) {
  const card = btn.closest(".product-card");
  const name = card.querySelector("h3").innerText;
  const price = parseInt(
    card.querySelector(".price").innerText.replace(/\D/g, "")
  );
  const qty = parseInt(card.querySelector(".qty").value);
  const total = price * qty;

  if (
    !confirm(
      `💳 XÁC NHẬN MUA HÀNG\n\n` +
        `Sản phẩm: ${name}\n` +
        `Số lượng: ${qty}\n` +
        `Tổng tiền: ${total.toLocaleString()} VNĐ`
    )
  )
    return;

  fetch("products.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: `product_id=${id}&quantity=${qty}&type=buy`,
  })
    .then((res) => res.json())
    .then((data) => alert(data.msg));
}
