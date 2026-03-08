# Cart Not Persisting - Complete Fix Guide

## 🔴 Problem
Cart items disappear after adding them. Cart empties automatically.

## 🔍 Root Cause
Session ID is changing between page loads, causing:
- New cart created each time
- Old cart items can't be found
- Appears as "empty cart"

---

## ✅ STEP 1: Test Session Persistence

**Run this test:** http://localhost/athletesgym/test_session.php

### What to Look For:
1. **Refresh the page 3-4 times (F5)**
2. **Counter should increase:** 1 → 2 → 3 → 4
3. **Session ID should stay the same**

### If Session ID Changes:
❌ **Sessions are broken** - follow fixes below

### If Session ID Stays Same:
✅ **Sessions work** - cart issue is different (skip to Alternative Fixes)

---

## 🔧 FIX 1: Check XAMPP tmp Folder

### Problem: Session files can't be saved

1. **Navigate to:** `C:\xampp\tmp`

2. **Check if folder exists:**
   - ✅ If exists: Continue to step 3
   - ❌ If missing: Create folder `C:\xampp\tmp`

3. **Check folder permissions:**
   - Right-click `tmp` folder → Properties
   - Uncheck "Read-only" if checked
   - Click Apply

4. **Clear old session files:**
   - Delete all files in `C:\xampp\tmp` (session files starting with `sess_`)

5. **Restart Apache:**
   - XAMPP Control Panel → Stop Apache
   - Start Apache again

6. **Test:** http://localhost/athletesgym/test_session.php

---

## 🔧 FIX 2: Configure php.ini

### Problem: Session save path not configured

1. **Open XAMPP Control Panel**

2. **Click "Config" next to Apache**

3. **Select "php.ini"**

4. **Find this line** (Ctrl+F search for "session.save_path"):
   ```ini
   ;session.save_path = "/tmp"
   ```

5. **Change to:**
   ```ini
   session.save_path = "C:/xampp/tmp"
   ```
   (Remove the semicolon `;` at the start)

6. **Save file**

7. **Restart Apache** in XAMPP Control Panel

8. **Test:** http://localhost/athletesgym/test_session.php

---

## 🔧 FIX 3: Clear Browser Cookies

### Problem: Corrupted session cookie

### Chrome/Edge:
1. Press `Ctrl + Shift + Delete`
2. Select "Cookies and other site data"
3. Time range: "Last hour"
4. Click "Clear data"
5. **Or manually:**
   - F12 → Application tab
   - Storage → Cookies → http://localhost
   - Delete all cookies

### Firefox:
1. Press `Ctrl + Shift + Delete`
2. Select "Cookies"
3. Time range: "Last hour"
4. Click "Clear Now"

---

## 🔧 FIX 4: Fix Multiple session_start() Calls

### Problem: Session started multiple times causes issues

This should already be fixed, but verify:

1. **Check files don't call session_start() twice:**
   - `includes/session.php` - starts session once ✅
   - Other files should NOT call `session_start()` directly

---

## 🔧 ALTERNATIVE FIX: Database-Only Cart (No Sessions)

If sessions continue to fail, modify cart to work without relying on session ID:

### Edit: `includes/cart_functions.php`

**Find line 26-35** (guest cart logic):

```php
} else {
    // Guest user (session-based)
    if (!isset($_SESSION['cart_session_id'])) {
        $_SESSION['cart_session_id'] = session_id();
        $pdo->prepare("INSERT INTO carts (session_id, status) VALUES (?, 'active')")->execute([$_SESSION['cart_session_id']]);
    }
    $stmt = $pdo->prepare("SELECT id FROM carts WHERE session_id=? and status = 'active' LIMIT 1");
    $stmt->execute([$_SESSION['cart_session_id']]);
    return $stmt->fetchColumn();
}
```

**Replace with:**

```php
} else {
    // Guest user - use persistent cart_session_id
    if (!isset($_SESSION['cart_session_id'])) {
        // Generate unique ID that persists
        $_SESSION['cart_session_id'] = 'guest_' . bin2hex(random_bytes(16));
    }

    // Try to find existing cart
    $stmt = $pdo->prepare("SELECT id FROM carts WHERE session_id=? and status = 'active' LIMIT 1");
    $stmt->execute([$_SESSION['cart_session_id']]);
    $cartId = $stmt->fetchColumn();

    // If no cart exists, create one
    if (!$cartId) {
        $pdo->prepare("INSERT INTO carts (session_id, status) VALUES (?, 'active')")->execute([$_SESSION['cart_session_id']]);
        $cartId = $pdo->lastInsertId();
    }

    return $cartId;
}
```

---

## 🔧 QUICK FIX: Use Logged-In User Only

### Simplest solution: Require login for cart

**Edit:** `includes/cart_functions.php`

**Change line 16-36** to:

```php
function getCartId($pdo) {
    // REQUIRE LOGIN - no guest carts
    if (!isset($_SESSION['user_id'])) {
        header("Location: " . (BASE_URL ?? '/') . "auth/login.php");
        exit;
    }

    // Logged-in user only
    $stmt = $pdo->prepare("SELECT id FROM carts WHERE user_id=? and status = 'active' LIMIT 1");
    $stmt->execute([$_SESSION['user_id']]);
    $cart = $stmt->fetchColumn();

    if (!$cart) {
        $pdo->prepare("INSERT INTO carts (user_id, status) VALUES (?, 'active')")->execute([$_SESSION['user_id']]);
        return $pdo->lastInsertId();
    }

    return $cart;
}
```

**This forces users to login before adding to cart - cart will persist because it's tied to user_id, not session_id.**

---

## 📊 Testing After Fix

### Test 1: Session Persistence
1. Visit: http://localhost/athletesgym/test_session.php
2. Refresh 5 times
3. Counter should increase: 1, 2, 3, 4, 5
4. Session ID should stay the same
5. ✅ If yes: Sessions fixed!

### Test 2: Cart Persistence
1. **Clear browser cache** (Ctrl+Shift+Delete)
2. **Login** to website
3. **Add item to cart** from shop
4. **Verify cart** shows 1 item
5. **Navigate away** (go to homepage)
6. **Go back to cart**
7. ✅ Item should still be there!

### Test 3: Checkout Flow
1. Add items to cart
2. View cart page
3. Click "Proceed to Checkout"
4. Checkout page should show items
5. ✅ Items persist through checkout!

---

## 🎯 Recommended Solution

**If you want cart without login:**
→ Use Alternative Fix (database-only cart)

**If requiring login is OK:**
→ Use Quick Fix (logged-in user only)

**For production:**
→ Fix session configuration (Fix 1 + Fix 2)

---

## 📝 Summary

The cart is clearing because:
1. Session ID changes between pages
2. New session = new cart
3. Old cart can't be found

**Solutions (pick one):**
1. ✅ **Fix sessions** - proper long-term solution
2. ✅ **Require login** - simplest, works immediately
3. ✅ **Persistent guest cart** - best of both worlds

---

**Start with test_session.php to diagnose the exact issue!**
