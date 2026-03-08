# Checkout Page Debug Steps

## Issue: Checkout page shows only "Enroll Now and Start Your Training"

This means:
- ✅ Page is loading
- ✅ Footer is showing
- ❌ Main content is not visible/rendered

---

## 🔍 Debugging Steps

### Step 1: Check if you're logged in

1. **Open:** http://localhost/athletesgym/auth/login.php
2. **Login** with your test account
3. **Or register** a new account at: http://localhost/athletesgym/auth/register.php

### Step 2: Add items to cart

1. **Go to shop:** http://localhost/athletesgym/shop.php
2. **Click on any product**
3. **Select size and color**
4. **Click "Add to Cart"**
5. **Verify** cart icon shows item count

### Step 3: View cart

1. **Go to cart:** http://localhost/athletesgym/cart.php
2. **You should see:**
   - Your cart items listed
   - Quantities and prices
   - "Proceed to Checkout" button

### Step 4: Proceed to checkout

1. **Click** "Proceed to Checkout"
2. **You should see:**
   - Billing form (Name, Email, Phone, Address)
   - Order summary on the right
   - "Place Order" button

---

## 🐛 Common Issues & Fixes

### Issue 1: Not Logged In
**Symptom:** Redirects to login page

**Fix:**
- Register at: http://localhost/athletesgym/auth/register.php
- Or login at: http://localhost/athletesgym/auth/login.php

---

### Issue 2: Empty Cart
**Symptom:** Message "Your cart is empty"

**Fix:**
1. Go to shop: http://localhost/athletesgym/shop.php
2. Add items to cart first
3. Then proceed to checkout

---

### Issue 3: CSS Not Loading
**Symptom:** Page loads but looks broken / unstyled

**Check:**
1. View page source (Ctrl+U)
2. Look for checkout form HTML
3. If HTML exists but not visible → CSS issue
4. Check browser console (F12) for errors

**Fix:**
- Clear browser cache (Ctrl+Shift+Delete)
- Hard refresh (Ctrl+F5)

---

### Issue 4: PHP Errors Hidden
**Symptom:** Blank page or partial content

**Check errors:**

**Method 1: Check browser**
- Right-click page → Inspect (F12)
- Go to Console tab
- Look for JavaScript errors

**Method 2: Check PHP errors**
- Open: `c:\xampp\htdocs\athletesGym\checkout.php`
- Add at the very top (line 2):
```php
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

**Method 3: Check Apache error log**
- Location: `C:\xampp\apache\logs\error.log`
- Open in Notepad
- Check for recent errors

---

## ✅ Quick Test Script

Create this test file to check your cart:

**File:** `c:\xampp\htdocs\athletesGym\test_cart.php`

```php
<?php
session_start();
require_once "admin/includes/db.php";
require_once "includes/cart_functions.php";

echo "<h1>Cart Debug</h1>";

echo "<h2>Session Info:</h2>";
echo "User ID: " . ($_SESSION['user_id'] ?? 'Not logged in') . "<br>";
echo "Guest: " . ($_SESSION['guest']['id'] ?? 'No guest session') . "<br>";
echo "Session ID: " . session_id() . "<br>";

echo "<h2>Cart Items:</h2>";
$items = getCartItems($pdo);

if ($items) {
    echo "<pre>";
    print_r($items);
    echo "</pre>";
    echo "<p><strong>Total items: " . count($items) . "</strong></p>";
} else {
    echo "<p style='color:red'>Cart is empty!</p>";

    // Check database directly
    $stmt = $pdo->prepare("SELECT * FROM cart_items WHERE session_id = ?");
    $stmt->execute([session_id()]);
    $dbItems = $stmt->fetchAll();

    echo "<h3>Direct DB Query:</h3>";
    if ($dbItems) {
        echo "<pre>";
        print_r($dbItems);
        echo "</pre>";
    } else {
        echo "<p>No items in database for this session</p>";
    }
}

echo "<hr>";
echo "<h2>Actions:</h2>";
echo "<a href='shop.php'>Go to Shop</a> | ";
echo "<a href='cart.php'>Go to Cart</a> | ";
echo "<a href='checkout.php'>Go to Checkout</a>";
?>
```

**Run:** http://localhost/athletesgym/test_cart.php

This will show:
- Your session info
- Cart items
- Any database issues

---

## 🎯 Expected Checkout Page Look

When working correctly, you should see:

```
[Header with Logo and Navigation]

Checkout
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

[Left Side]                    [Right Side]
Billing Details                Order Summary

Full Name: [________]          Product Image | Name
Email: [________]              Qty x Price QR
Phone: [________]
Address: [________]            Subtotal: XX.XX QR
Notes: [________]              Shipping: Free
                              ━━━━━━━━━━━━━━━
[Place Order Button]           Total: XX.XX QR

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

[Footer - "Enroll Now and Start Your Training"]
```

---

## 🔧 Manual Check

1. **Open:** http://localhost/athletesgym/checkout.php
2. **Right-click** → View Page Source (Ctrl+U)
3. **Search for:** "Billing Details"
4. **If found:** HTML is there but CSS hiding it
5. **If not found:** PHP is stopping execution early

---

## 📝 Quick Fixes

### Fix 1: Enable Error Display (Temporary)

Edit `c:\xampp\htdocs\athletesGym\.env`:
```env
APP_DEBUG=true
```

Reload checkout page - any errors will show

---

### Fix 2: Check Database Connection

Edit `checkout.php` - Add after line 13:
```php
$items = getCartItems($pdo);

// DEBUG - remove after testing
echo "<pre>Items: ";
var_dump($items);
echo "</pre>";
die("Debug stop");

if (!$items) {
```

This will show what `getCartItems()` returns.

---

### Fix 3: Bypass Authentication (Test Only)

Edit `checkout.php` - Comment out line 11:
```php
// require_auth();  // TEMPORARILY DISABLED FOR TESTING
```

Try loading checkout - if it works, auth is the issue.

---

## 🚀 Most Likely Solution

**The issue is probably:**
1. You're not logged in
2. Cart is empty
3. Both!

**Quick fix:**
1. Register: http://localhost/athletesgym/auth/register.php
2. Add items to cart from shop
3. Go to cart to verify items exist
4. Click "Proceed to Checkout"

---

**Try these steps and let me know what you see!**
