    <!-- Footer Start -->
    <footer>
        <div style="background: #e5e8e9;" class="container-fluid pt-3 pb-3">
            <h3 style="font-size:35px" class="text-center"><a style="text-decoration: none;color: #000;" href="https://www.instagram.com/athletesqa/" target="_blank">Follow US @Athletesqa</a></h3>
        </div>
        <div class="container">
            <div class="footer-cta-block">
                <div class="footer-title wow fadeInUp">
                    <h2>Enroll Now and Start Your Training</h2>
                    <a href="contact.php" class="common-btn primary">Start your Journey now!</a>
                </div>
                <nav class="navbar navbar-expand-lg">
                    <a class="navbar-brand p-0" href="index.php"><img src="assets/images/logo/logo-white.png" width="240px"></a>
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link" href="#">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="about-us.php">About</a>
                        </li>
                        <!-- <li class="nav-item">
                            <a class="nav-link" href="shop.php">Shop</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="membership.php">Memberships</a>
                        </li> -->
                        <li class="nav-item">
                            <a class="nav-link" href="contact.php">Contact</a>
                        </li>
                    </ul>
                </nav>
            </div>

            <div class="copyright-info-block">
                <p>© <span id="year"></span> Athletes Gym | All Rights Reserved</p>
                <!-- <p href="#">Made by Zajel Creative Agency</p> -->
            </div>
        </div>
    </footer>
    <!-- Footer End -->
    <script src="assets/vendors/js/jquery.min.js"></script>
    <script src="assets/vendors/bootstrap/bootstrap.bundle.min.js"></script>
    <script src="assets/vendors/js/swiper.js"></script>
    <script src="assets/vendors/js/wow.js"></script>
    <script src="assets/vendors/js/custom.js"></script>

    <script>
        document.getElementById("year").innerHTML = new Date().getFullYear();
    </script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.4.120/pdf.min.js"></script>

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
//     // $('#cart-count').text(count);
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

//   document.getElementById('readBtn').addEventListener('click', () => {
//     const content = document.querySelector('.checkout-container').innerText;
//     const utterance = new SpeechSynthesisUtterance(content);
//     utterance.rate = 1;     // speed: 0.1 (slow) to 10 (fast)
//     utterance.pitch = 1;    // voice pitch
//     utterance.volume = 1;   // 0 to 1

//     // Optional: choose a voice
//     const voices = window.speechSynthesis.getVoices();
//     if (voices.length > 0) {
//       utterance.voice = voices[0]; // pick a specific voice if needed
//     }

//     window.speechSynthesis.cancel(); // stop any ongoing speech
//     window.speechSynthesis.speak(utterance);
//   });

document.addEventListener("DOMContentLoaded", () => {
  const buttons = document.querySelectorAll(".add-to-cart-btn");
  buttons.forEach(btn => {
    btn.addEventListener("click", function () {
      alert("asdasdasd");
      const productDiv = this.closest(".product");
      const productId = productDiv.dataset.id;
      const quantity = productDiv.querySelector(".quantity").value;
        console.log(productId);
        console.log(quantity);
      // Send via AJAX
      fetch("cards/cart_add.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/json"
        },
        body: JSON.stringify({ product_id: productId, quantity: quantity })
      })
      .then(res => res.json())
      .then(data => {
        console.log(data);
        if (data.success) {
          alert("✅ Added to cart: " + data.item.name + " (" + data.item.quantity + ")");
          // Optional: update mini cart counter
          document.getElementById("cart-count").textContent = data.cart_count;
        } else {
          alert("❌ Error: " + data.message);
        }
      })
      .catch(err => console.error(err));
    });
  });
});
//   const url = "assets/athletes/RUN OF THE MILL X ZAJEL PROPOSAL FINAL (2).pdf"; // replace with your PDF file path
//   let pdfDoc = null,
//       pageNum = 1,
//       pageRendering = false,
//       canvas = document.getElementById("pdf-canvas"),
//       ctx = canvas.getContext("2d");

//   // Load PDF
//   pdfjsLib.getDocument(url).promise.then(function (pdfDoc_) {
//     pdfDoc = pdfDoc_;
//     document.getElementById("page-count").textContent = pdfDoc.numPages;
//     renderPage(pageNum);
//   });

//   function renderPage(num) {
//     pageRendering = true;
//     pdfDoc.getPage(num).then(function (page) {
//       const viewport = page.getViewport({ scale: 1.5 });
//       canvas.height = viewport.height;
//       canvas.width = viewport.width;

//       const renderContext = {
//         canvasContext: ctx,
//         viewport: viewport,
//       };
//       page.render(renderContext).promise.then(function () {
//         pageRendering = false;
//         document.getElementById("page-num").textContent = pageNum;
//       });
//     });
//   }

//   document.getElementById("prev").addEventListener("click", function () {
//     if (pageNum <= 1) return;
//     pageNum--;
//     renderPage(pageNum);
//   });

//   document.getElementById("next").addEventListener("click", function () {
//     if (pageNum >= pdfDoc.numPages) return;
//     pageNum++;
//     renderPage(pageNum);
//   });

//   // 🔊 Read Visible Page Text
//   document.getElementById("readBtn").addEventListener("click", function () {
//   pdfDoc.getPage(pageNum).then(function (page) {
//     return page.getTextContent();
//   }).then(function (textContent) {
//     const textItems = textContent.items;
//     if (!textItems || textItems.length === 0) {
//       alert("No readable text on this page.");
//       return;
//     }

//     const finalText = textItems.map(item => item.str).join(" ");
//     console.log("Reading text:", finalText);

//     const utterance = new SpeechSynthesisUtterance(finalText);
//     utterance.rate = 1;
//     utterance.pitch = 1;
//     utterance.volume = 1;

//     window.speechSynthesis.cancel(); // stop current voice
//     window.speechSynthesis.speak(utterance);
//   }).catch(err => {
//     console.error("Error getting text content:", err);
//     alert("Failed to read this page.");
//   });
// });

</script>

    </body>

    </html>