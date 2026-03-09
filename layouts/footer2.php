<footer class="text-white text-center text-lg-start" style="background-color: #000000; width: 100%; margin: 0; padding: 0;">
    <!-- Grid container -->
    <div class="container p-4" style="max-width: 1200px; margin: 0 auto;">
        <!--Grid row-->
        <div class="row mt-4">
            <!--Grid column-->
            <div class="col-lg-4 col-md-12 mb-4 mb-md-0">
                <h3 class="text-uppercase mb-4">Let's Get Moving</h3>
                <p>
                We’re more than just a workout place.
                </p>
                <div class="mt-4">
                    <!-- Facebook -->
                    <a type="button" href="https://www.instagram.com/athletesqa/" class="btn btn-floating btn-light btn-lg"><i class="fa-brands fa-instagram"></i></a>
                    <!-- Whatsapp -->
                    <a type="button" href="https://www.tiktok.com/@athletesqa" class="btn btn-floating btn-light btn-lg"><i class="fa-brands fa-tiktok"></i></a>
                    <!-- Twitter -->
                    <a type="button" href="https://www.youtube.com/@athletesqa" class="btn btn-floating btn-light btn-lg"><i class="fa-brands fa-youtube"></i></a>
                </div>
            </div>
            <!--Grid column-->

            <!--Grid column-->
            <div class="col-lg-4 col-md-6 mb-4 mb-md-0">
                <h5 class="text-uppercase mb-4 pb-1">Contact Us</h5>
                <ul class="contact-list" style="list-style: none; padding: 0; margin: 0;">
                    <li class="mb-3" style="display: flex; align-items: flex-start; gap: 12px;">
                        <i class="fas fa-home" style="font-size: 16px; margin-top: 3px; min-width: 20px;"></i>
                        <span>G-27, Mamsha Bay, Lusail Marina 12-D, Doha, Qatar.</span>
                    </li>
                    <li class="mb-3" style="display: flex; align-items: flex-start; gap: 12px;">
                        <i class="fas fa-envelope" style="font-size: 16px; margin-top: 3px; min-width: 20px;"></i>
                        <span>info@athletesgym.qa</span>
                    </li>
                    <li class="mb-3" style="display: flex; align-items: flex-start; gap: 12px;">
                        <i class="fas fa-phone" style="font-size: 16px; margin-top: 3px; min-width: 20px;"></i>
                        <span>+974 3999 2247</span>
                    </li>
                </ul>
            </div>
            <!--Grid column-->

            <!--Grid column-->
            <style>
                .table>*:not(caption)>*>* {
                    color: #fff;
                    background-color: #000;
                    /* Removes all inherited styles */
                }
            </style>
            <div class="col-lg-4 col-md-6 mb-4 mb-md-0">
                <h5 class="text-uppercase mb-4">Viewing hours</h5>

                <table class="table text-left text-white">
                    <tbody class="fw-normal">
                        <tr>
                            <td>Sat - Thu</td>
                            <td>8:00am - 5:00pm</td>
                        </tr>
                        <tr>
                            <td>Friday</td>
                            <td>Closed</td>
                        </tr>
                        <tr>
                            <td>Holidays:</td>
                            <td>Closed</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <!--Grid column-->
        </div>
        <!--Grid row-->
    </div>
    <!-- Grid container -->

    <!-- Copyright -->
    <div class="text-center p-3" style="background-color: rgba(0, 0, 0, 0.2); width: 100%;">
        <p style="margin: 0;">© <span id="year"></span> Athletes Gym | All Rights Reserved</p>
    </div>
    <!-- Copyright -->
</footer>
<!-- Footer End -->
<script src="<?= BASE_URL ?>assets/vendors/js/jquery.min.js"></script>
<script src="<?= BASE_URL ?>assets/vendors/bootstrap/bootstrap.bundle.min.js"></script>
<script src="<?= BASE_URL ?>assets/vendors/js/swiper.js"></script>
<script src="<?= BASE_URL ?>assets/vendors/js/wow.js"></script>
<script src="<?= BASE_URL ?>assets/vendors/js/custom.js"></script>
<script src="https://kit.fontawesome.com/a545e4c658.js" crossorigin="anonymous"></script>
<script>
    document.getElementById("year").innerHTML = new Date().getFullYear();
</script>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
//   const CART_KEY = "my_custom_cart";

//   function getCart() {
//     return JSON.parse(localStorage.getItem(CART_KEY)) || [];
//   }

//   function saveCart(cart) {
//     localStorage.setItem(CART_KEY, JSON.stringify(cart));
//   }

//   function updateCartUI() {
//     const cart = getCart();
//     let total = 0;
//     let count = 0;
//     $('#cart-items').empty();
//     cart.forEach((item, index) => {
//       let itemTotal = item.price * item.quantity;
//       total += itemTotal;
//       count += item.quantity;
//       $('#cart-items').append(`
//         <li data-index="${index}">
//         <div><strong>${item.name}</strong></div>
//         <div style="display: flex; align-items: center; gap: 5px; margin: 5px 0;">
//           <button class="cart-minus">−</button>
//           <span>${item.quantity}</span>
//           <button class="cart-plus">+</button>
//           <span style="margin-left: auto;">${itemTotal.toFixed(2)} QAR</span>
//         </div>
//         <button class="remove-item btn btn-sm btn-danger" data-index="${index}">Remove</button>
//       </li>
//       `);
//     });
//     $('#cart-count').text(count);
//     $('#cart-total').text(total.toFixed(2));
//   }
//   $(document).on('click', '.cart-plus', function () {
//   const index = $(this).closest('li').data('index');
//   const cart = getCart();
//   cart[index].quantity += 1;
//   saveCart(cart);
//   updateCartUI();
// });

// // Handle cart − button
// $(document).on('click', '.cart-minus', function () {
//   const index = $(this).closest('li').data('index');
//   const cart = getCart();
//   if (cart[index].quantity > 1) {
//     cart[index].quantity -= 1;
//   } else {
//     cart.splice(index, 1); // Optionally remove if quantity is 0
//   }
//   saveCart(cart);
//   updateCartUI();
// });
//   $(document).ready(function () {
//     updateCartUI();

//     $('.add-to-cart-btn').on('click', function () {
//       const parent = $(this).closest('.product');
//       const id = parent.data('id');
//       const name = parent.data('name');
//       const price = parseFloat(parent.data('price'));
//       const quantity = parseInt(parent.find('.quantity').val());
//       const size = parent.data('size');

//       const cart = getCart();
//       const existingIndex = cart.findIndex(i => i.id === id && i.size === size);

//       if (existingIndex !== -1) {
//         cart[existingIndex].quantity += quantity;
//       } else {
//         cart.push({ id, name, price, quantity, size });
//       }

//       saveCart(cart);
//       updateCartUI();
//     });

//     $('#toggle-cart').on('click', function () {
//       $('#cart-dropdown').toggle();
//     });

//     $(document).on('click', '.remove-item', function () {
//       const index = $(this).data('index');
//       const cart = getCart();
//       cart.splice(index, 1);
//       saveCart(cart);
//       updateCartUI();
//     });

//     $('#clear-cart').on('click', function () {
//       localStorage.removeItem(CART_KEY);
//       updateCartUI();
//     });
//   });

document.addEventListener("DOMContentLoaded", () => {
  const buttons = document.querySelectorAll(".add-to-cart-btn");

  buttons.forEach(btn => {
    btn.addEventListener("click", function () {
      const productDiv = this.closest(".product");
      const productId = productDiv.dataset.id;
      const quantity = productDiv.querySelector(".quantity").value;
      const selectedVariantId = productDiv.querySelector(".variant")?.value || null;

      console.log("Add to Cart clicked:", { productId, quantity, selectedVariantId });

      if (!selectedVariantId) {
        alert("⚠️ Please select a color and size before adding to cart");
        return;
      }

      // Send via AJAX
      fetch("<?= BASE_URL ?>cards/cart_add.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/json"
        },
        body: JSON.stringify({
          product_id: parseInt(productId),
          quantity: parseInt(quantity),
          variant_id: parseInt(selectedVariantId)
        })
      })
      // 👇 parse JSON first
      .then(res => {
        if (!res.ok) throw new Error("HTTP error " + res.status);
        return res.json();
      })
      .then(data => {
        console.log("Parsed Response:", data);

        if (data.success === true) {
          alert("✅ Added to cart: " + data.item.name + " (" + data.item.quantity + ")");
          const cartCount = document.getElementById("cart-count");
          if (cartCount) cartCount.textContent = data.cart_count;
        } else {
          alert("❌ Error: " + (data.message || data.error || "Something went wrong."));
        }
      })
      .catch(err => {
        console.error("Fetch Error:", err);
        alert("Something went wrong while adding to cart.");
      });
    });
  });
});

/////////////////////////////////////

document.addEventListener("DOMContentLoaded", () => {
const variantMap = <?= json_encode($variantMap) ?>;
const variants = <?= json_encode($variants) ?>;

let selectedColor = document.querySelector('.swatch.selected')?.dataset.color;
let selectedSize = document.querySelector('.size-btn.selected')?.dataset.size;
const stockStatus = document.getElementById('stock-status');
const addBtn = document.querySelector('.add-to-cart-btn');
const qtyInput = document.querySelector('.quantity');
const variantInput = document.querySelector('.variant');

// Update stock info dynamically
function updateStockStatus() {
    if (!selectedColor || !selectedSize) return;

    const stock = variantMap[selectedColor]?.[selectedSize] ?? 0;
    
    // ✅ Find and set variant ID
    const matchingVariant = variants.find(v => 
        v.color.toLowerCase() === selectedColor.toLowerCase() && 
        v.size.toLowerCase() === selectedSize.toLowerCase()
    );
    if (matchingVariant) {
        variantInput.value = matchingVariant.id;
    }

    if (stock <= 0) {
        addBtn.disabled = true;
        addBtn.classList.add('btn-disabled');
        addBtn.textContent = "Out of Stock";
        stockStatus.textContent = "This variant is currently unavailable.";
        stockStatus.style.color = "#666";
        stockStatus.style.backgroundColor = "#f5f5f5";
        qtyInput.value = 0;
        qtyInput.disabled = true;
    } else if (stock < 5) {
        addBtn.disabled = false;
        addBtn.classList.remove('btn-disabled');
        addBtn.textContent = "Add to Cart";
        qtyInput.disabled = false;
        qtyInput.max = stock; // ✅ limit quantity field dynamically
        if (parseInt(qtyInput.value) > stock) qtyInput.value = stock;
        stockStatus.textContent = "⏱ Only " + stock + " item" + (stock === 1 ? "" : "s") + " left";
        stockStatus.style.color = "#1a5e3d";
        stockStatus.style.backgroundColor = "#f0f8f5";
        stockStatus.style.display = "block";
    } else {
        addBtn.disabled = false;
        addBtn.classList.remove('btn-disabled');
        addBtn.textContent = "Add to Cart";
        qtyInput.disabled = false;
        qtyInput.max = stock; // ✅ limit quantity field dynamically
        if (parseInt(qtyInput.value) > stock) qtyInput.value = stock;
        stockStatus.textContent = "In Stock: " + stock + " available";
        stockStatus.style.color = "#1a5e3d";
        stockStatus.style.backgroundColor = "#f0f8f5";
        stockStatus.style.display = "block";
    }
}

// Handle color selection
document.querySelectorAll('.swatch').forEach(el => {
    el.addEventListener('click', () => {
        document.querySelectorAll('.swatch').forEach(s => s.classList.remove('selected'));
        el.classList.add('selected');
        selectedColor = el.dataset.color;
        updateStockStatus();
    });
});

// Handle size selection
document.querySelectorAll('.size-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        document.querySelectorAll('.size-btn').forEach(b => b.classList.remove('selected'));
        btn.classList.add('selected');
        selectedSize = btn.dataset.size;
        updateStockStatus();
    });
});

// ✅ Prevent entering quantity greater than stock
qtyInput.addEventListener('input', () => {
    const stock = variantMap[selectedColor]?.[selectedSize] ?? 0;
    let qty = parseInt(qtyInput.value);
    if (qty > stock) {
        qtyInput.value = stock;
        alert("Only " + stock + " items available in stock!");
    } else if (qty < 1) {
        qtyInput.value = 1;
    }
});

// ✅ Handle Quantity +/- Buttons
const qtyMinusBtn = document.querySelector('.qty-minus');
const qtyPlusBtn = document.querySelector('.qty-plus');

if (qtyMinusBtn && qtyPlusBtn) {
    qtyMinusBtn.addEventListener('click', (e) => {
        e.preventDefault();
        let qty = parseInt(qtyInput.value) || 1;
        if (qty > 1) {
            qtyInput.value = qty - 1;
        }
    });

    qtyPlusBtn.addEventListener('click', (e) => {
        e.preventDefault();
        const stock = variantMap[selectedColor]?.[selectedSize] ?? 10;
        let qty = parseInt(qtyInput.value) || 1;
        if (qty < stock) {
            qtyInput.value = qty + 1;
        }
    });
}

// Initial stock check
updateStockStatus();

}); // END DOMContentLoaded

</script>
</body>

</html>