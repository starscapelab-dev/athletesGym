# Complete Localhost Testing Guide - Athletes Gym

## 🚀 How to Test Your Website on Localhost

This guide will walk you through testing every feature of your Athletes Gym website locally before deploying to production.

---

## 📋 Prerequisites Checklist

Before testing, make sure:

- [x] XAMPP is installed
- [ ] Apache is running (green in XAMPP Control Panel)
- [ ] MySQL is running (green in XAMPP Control Panel)
- [ ] Database `athletesGym` exists and has data
- [ ] All files are in `c:\xampp\htdocs\athletesGym\`
- [ ] .env file is configured correctly

---

## 🔧 Step 1: Start XAMPP (1 minute)

### 1.1 Open XAMPP Control Panel
```
Location: C:\xampp\xampp-control.exe
Double-click to open
```

### 1.2 Start Services
1. Click **"Start"** next to **Apache** → Should turn green
2. Click **"Start"** next to **MySQL** → Should turn green

**If they don't start:**
- Port 80 might be in use (stop IIS or Skype)
- Port 3306 might be in use (stop other MySQL instances)
- Check logs by clicking "Logs" button

---

## 🗄️ Step 2: Verify Database (2 minutes)

### 2.1 Open phpMyAdmin
```
Browser: http://localhost/phpmyadmin
```

### 2.2 Check Database Exists
1. Look for `athletesGym` database in left sidebar
2. If missing, you need to import it

### 2.3 Import Database (if needed)
1. Click **"Import"** tab
2. Click **"Choose File"**
3. Select your database backup file
4. Click **"Go"**
5. Wait for success message

### 2.4 Verify Required Tables
Database should have these tables:
```
✓ users
✓ products
✓ categories
✓ orders
✓ order_items
✓ carts
✓ cart_items
✓ sizes
✓ colors
✓ reviews
✓ admin_users
```

### 2.5 Check Password Reset Columns
```sql
-- Run this in phpMyAdmin SQL tab:
DESCRIBE users;

-- Should show:
-- reset_otp (varchar(6))
-- reset_expires (datetime)
```

**If missing, run:**
```sql
ALTER TABLE users
ADD COLUMN reset_otp VARCHAR(6) DEFAULT NULL,
ADD COLUMN reset_expires DATETIME DEFAULT NULL;
```

---

## 🌐 Step 3: Test Website Homepage (2 minutes)

### 3.1 Visit Homepage
```
URL: http://localhost/athletesGym/
or
URL: http://localhost/athletesGym/index.php
```

### 3.2 What to Check:
- ✅ Page loads without errors
- ✅ Images display correctly
- ✅ Navigation menu works
- ✅ No PHP errors visible
- ✅ Cart icon shows in header
- ✅ Styling looks correct

### 3.3 Test Navigation:
Click each menu item:
- Home → Should load
- About → Should scroll/navigate
- Shop → Should show products
- Classes → Should show classes
- Contact → Should show contact form

---

## 🔐 Step 4: Test User Registration (3 minutes)

### 4.1 Go to Registration Page
```
URL: http://localhost/athletesGym/auth/register.php
```

### 4.2 Fill Out Registration Form:
```
Name: Test User
Email: testuser@example.com
Phone: 12345678
Password: TestPass123
Confirm Password: TestPass123
Gender: Male
Date of Birth: 1990-01-01
Country: Qatar
City: Doha
```

### 4.3 Click "Register"

**Expected Results:**
- ✅ Should redirect to login or homepage
- ✅ No errors displayed
- ✅ User created in database

### 4.4 Verify in Database:
```
phpMyAdmin → athletesGym → users table
Should see new user with hashed password
```

---

## 🔓 Step 5: Test User Login (2 minutes)

### 5.1 Go to Login Page
```
URL: http://localhost/athletesGym/auth/login.php
```

### 5.2 Login with Test Account:
```
Email: testuser@example.com
Password: TestPass123
```

### 5.3 Click "Login"

**Expected Results:**
- ✅ Should redirect to homepage or dashboard
- ✅ Username shows in header (if logged in state visible)
- ✅ "Logout" option appears
- ✅ No error messages

### 5.4 Check Session:
```
URL: http://localhost/athletesGym/test_session.php
Should show:
- Session ID (consistent on refresh)
- Counter incrementing
- Sessions working correctly
```

---

## 🔑 Step 6: Test Password Reset (5 minutes)

### 6.1 Test Forgot Password Flow

**Step 1: Request OTP**
```
URL: http://localhost/athletesGym/auth/forgot_password.php

Enter email: testuser@example.com
Click "Send OTP"
```

**Expected:**
- ✅ Success message appears
- ✅ Email sent (check test_resend_simple.php if needed)
- ✅ Redirects to verify OTP page

**Step 2: Check Email**
```
Option A: Check your email inbox (if Resend is working)
Option B: Check database for OTP:
  phpMyAdmin → users table → reset_otp column
  Find OTP code (6 digits)
```

**Step 3: Verify OTP**
```
URL: http://localhost/athletesGym/auth/verify_otp.php
(Should auto-redirect from forgot_password.php)

Enter OTP: 123456 (or code from email/database)
Click "Verify OTP"
```

**Expected:**
- ✅ OTP validates successfully
- ✅ Redirects to reset password page

**Step 4: Reset Password**
```
URL: http://localhost/athletesGym/auth/reset_password.php
(Should auto-redirect from verify_otp.php)

New Password: NewPass123
Confirm Password: NewPass123
Click "Update Password"
```

**Expected:**
- ✅ Password updated successfully
- ✅ Redirects to login page with success message

**Step 5: Login with New Password**
```
URL: http://localhost/athletesGym/auth/login.php

Email: testuser@example.com
Password: NewPass123
Click "Login"
```

**Expected:**
- ✅ Login successful
- ✅ Password reset flow complete!

---

## 🛍️ Step 7: Test Shopping Features (10 minutes)

### 7.1 Browse Products
```
URL: http://localhost/athletesGym/shop.php
```

**Check:**
- ✅ Products display in grid
- ✅ Images load correctly
- ✅ Prices show correctly
- ✅ Categories filter works
- ✅ Search works (if implemented)

### 7.2 View Product Details
```
Click on any product
URL: http://localhost/athletesGym/product_detail.php?id=1
```

**Check:**
- ✅ Product details load
- ✅ Images display
- ✅ Price shows
- ✅ Size/color options work
- ✅ Stock information displays
- ✅ "Add to Cart" button works

### 7.3 Add to Cart
```
1. Select size/color (if applicable)
2. Click "Add to Cart"
```

**Expected:**
- ✅ Success message appears
- ✅ Cart icon updates (shows quantity)
- ✅ Cart count increases in header

### 7.4 View Cart
```
URL: http://localhost/athletesGym/cart.php
or click cart icon in header
```

**Check:**
- ✅ Cart items display
- ✅ Can update quantities (+/- buttons)
- ✅ Can remove items
- ✅ Subtotal calculates correctly
- ✅ "Proceed to Checkout" button works

### 7.5 Test Cart Persistence
```
1. Add items to cart
2. Navigate to different pages
3. Return to cart
```

**Expected:**
- ✅ Cart items persist across pages
- ✅ Cart doesn't empty randomly
- ✅ Quantities remain correct

---

## 💳 Step 8: Test Checkout Process (7 minutes)

### 8.1 Go to Checkout
```
URL: http://localhost/athletesGym/checkout.php
or click "Proceed to Checkout" from cart
```

**Important Note:**
🚨 MyFatoorah payment will show an error on localhost because the callback URL is not publicly accessible. This is NORMAL and expected!

### 8.2 Fill Out Checkout Form
```
Full Name: Test Customer
Email: test@example.com
Phone: 12345678
Address: Test Address, Doha, Qatar
```

### 8.3 Click "Place Order"

**Expected Results:**

**On Localhost:**
- ✅ Form submits without CSRF error
- ✅ Order is created in database
- ⚠️ MyFatoorah error appears (expected - callback URL not accessible)
- ✅ Stock is deducted from products
- ✅ Error message displays clearly (not silent reload)

**Check Order Created:**
```
phpMyAdmin → athletesGym → orders table
Should see new order with status "pending"
```

**Check Stock Deducted:**
```
phpMyAdmin → athletesGym → products table
Stock count should be reduced
```

### 8.4 Debug Checkout (if needed)
```
URL: http://localhost/athletesGym/checkout_debug.php

Fill form and submit to see:
- POST data received
- CSRF token validation
- Session information
- Cart items
- Database connection
```

---

## 📧 Step 9: Test Email System (5 minutes)

### 9.1 Open Email Test Page
```
URL: http://localhost/athletesGym/test_resend_simple.php
```

### 9.2 Configuration Check
**Should show:**
- ✅ API Key: re_bLYzBP...cbY ✓ Set
- ✅ From Email: onboarding@resend.dev
- ✅ From Name: Athletes Gym Qatar
- ✅ Admin Email: info@athletesgym.qa

### 9.3 Send Test Email

**Step 1:** Enter your email address
```
Your Email: your@email.com
```

**Step 2:** Select test type:
- Simple Test Email
- Password Reset OTP Email
- Order Confirmation Email

**Step 3:** Click "Send Test Email"

**Expected:**
- ✅ Success message appears
- ✅ Email ID shown
- ✅ Check your inbox (arrives within seconds)
- ✅ Email has black/white branding
- ✅ Professional design
- ✅ Mobile responsive

### 9.4 Check Email Quality
**Email should have:**
- ✅ Black header with icon
- ✅ Montserrat font
- ✅ Professional layout
- ✅ Correct content
- ✅ No broken images
- ✅ "Athletes Gym Qatar" branding

---

## 🔒 Step 10: Test Admin Panel (5 minutes)

### 10.1 Access Admin Login
```
URL: http://localhost/athletesGym/admin/login.php
```

### 10.2 Login as Admin
```
Default credentials (check your database):
Username: admin
Password: admin123
(or whatever you set up)
```

**If you don't have admin account, create one:**
```sql
-- In phpMyAdmin SQL tab:
INSERT INTO admin_users (username, password)
VALUES ('admin', '$2y$10$YourHashedPasswordHere');

-- Or create via PHP:
-- password_hash('admin123', PASSWORD_BCRYPT)
```

### 10.3 Test Admin Features

**Dashboard:**
```
URL: http://localhost/athletesGym/admin/
Should show:
- Order statistics
- Recent orders
- Sales data
```

**Manage Products:**
```
URL: http://localhost/athletesGym/admin/products/list.php
Test:
- ✅ Add new product
- ✅ Edit existing product
- ✅ Delete product
- ✅ View product list
```

**Manage Orders:**
```
URL: http://localhost/athletesGym/admin/orders/list.php
Test:
- ✅ View all orders
- ✅ Update order status
- ✅ View order details
```

**Manage Categories:**
```
URL: http://localhost/athletesGym/admin/category/list.php
Test:
- ✅ Add category
- ✅ Edit category
- ✅ Delete category
```

---

## 🎨 Step 11: Test Custom Pages (3 minutes)

### 11.1 Test 404 Page
```
URL: http://localhost/athletesGym/nonexistent-page

Should show:
- ✅ Custom 404 error page
- ✅ Black/white branding
- ✅ Dumbbell icon 🏋️
- ✅ "Back to Home" button
- ✅ Quick links section
- ✅ Matches website theme
```

### 11.2 Test Error Handling
```
Try various wrong URLs to ensure 404 page appears
```

---

## 📱 Step 12: Test Responsive Design (5 minutes)

### 12.1 Test Mobile View

**In Chrome:**
1. Press **F12** to open DevTools
2. Click **Toggle Device Toolbar** (phone icon)
3. Select device: iPhone 12 Pro

**Test pages:**
- Homepage
- Shop
- Product detail
- Cart
- Checkout
- 404 page

**Check:**
- ✅ Layout adjusts for mobile
- ✅ Navigation menu works (hamburger)
- ✅ Images scale properly
- ✅ Buttons are touch-friendly
- ✅ Forms are usable
- ✅ Text is readable

---

## 🐛 Step 13: Check for Errors (3 minutes)

### 13.1 Check PHP Errors
```
Look for red error messages on any page
Should see NONE if APP_DEBUG=false
```

### 13.2 Check Browser Console
```
Press F12 → Console tab
Look for JavaScript errors (red text)
Should see NONE or only warnings
```

### 13.3 Check Error Logs
```
File: C:\xampp\apache\logs\error.log
Look for recent errors
Filter for "athletesGym" or today's date
```

### 13.4 Common Errors to Check:

**Database Connection:**
```
Error: "Could not connect to database"
Fix: Check .env DB credentials
```

**Session Errors:**
```
Error: "session_start(): already started"
Fix: Already fixed in our code ✅
```

**File Not Found:**
```
Error: "include(): Failed opening"
Fix: Check file paths are correct
```

**CSRF Errors:**
```
Error: "CSRF token validation failed"
Fix: Already fixed ✅
```

---

## ✅ Complete Testing Checklist

Use this to track your testing:

### Basic Functionality:
- [ ] Apache & MySQL running
- [ ] Database exists and has data
- [ ] Homepage loads correctly
- [ ] Images display
- [ ] Navigation works

### Authentication:
- [ ] User registration works
- [ ] User login works
- [ ] Password reset flow (forgot → OTP → reset)
- [ ] Admin login works
- [ ] Logout works

### Shopping:
- [ ] Browse products
- [ ] View product details
- [ ] Add to cart
- [ ] Update cart quantities
- [ ] Remove from cart
- [ ] Cart persists across pages

### Checkout:
- [ ] Checkout form displays
- [ ] Can fill out form
- [ ] Form submits (no CSRF error)
- [ ] Order created in database
- [ ] Stock deducted
- [ ] Error message displays (MyFatoorah - expected)

### Emails:
- [ ] Email test page works
- [ ] Can send test emails
- [ ] Emails arrive in inbox
- [ ] Email design looks professional
- [ ] Password reset OTP emails work

### Admin:
- [ ] Admin login works
- [ ] Dashboard displays
- [ ] Can manage products
- [ ] Can manage orders
- [ ] Can manage categories

### Design & UX:
- [ ] 404 page works and looks good
- [ ] Mobile responsive
- [ ] No broken images
- [ ] Fonts load correctly
- [ ] Colors match theme (black/white)

### Error Handling:
- [ ] No PHP errors visible
- [ ] No JavaScript console errors
- [ ] Error messages are user-friendly
- [ ] No duplicate session warnings

---

## 🎯 Expected Behavior Summary

### ✅ What SHOULD Work on Localhost:

- ✅ All pages load
- ✅ User registration/login
- ✅ Password reset with OTP emails
- ✅ Browse and search products
- ✅ Add to cart
- ✅ Cart persistence
- ✅ Checkout form submission
- ✅ Order creation in database
- ✅ Stock management
- ✅ Admin panel
- ✅ Email sending (Resend)
- ✅ 404 error page

### ⚠️ What WON'T Work on Localhost:

- ⚠️ MyFatoorah payment completion (callback URL not accessible)
  - Order is created ✅
  - Payment redirect fails ⚠️
  - This is NORMAL - test on live server

- ⚠️ Payment status updates from callback
  - Cart won't clear automatically on localhost
  - This is NORMAL - works on production

### How to Handle MyFatoorah Testing:

**On Localhost:**
```
Test everything except payment:
1. Add to cart ✅
2. Checkout form ✅
3. Order creation ✅
4. Stock deduction ✅
5. Payment redirect ⚠️ (will fail - OK!)
```

**On Production Server:**
```
Full payment flow will work:
1. Add to cart ✅
2. Checkout ✅
3. Redirect to MyFatoorah ✅
4. Complete payment ✅
5. Return to site ✅
6. Cart cleared ✅
7. Order confirmed ✅
```

---

## 🆘 Troubleshooting

### Problem: Apache won't start
**Solution:**
- Port 80 in use → Stop IIS, Skype, or other web servers
- Check XAMPP logs
- Try different port in httpd.conf

### Problem: MySQL won't start
**Solution:**
- Port 3306 in use → Stop other MySQL instances
- Check XAMPP logs
- Try MySQL service in Services

### Problem: "Page not found" errors
**Solution:**
- Check Apache is running
- Verify URL: http://localhost/athletesGym/ (with capital G)
- Check files are in C:\xampp\htdocs\athletesGym\

### Problem: "Database connection failed"
**Solution:**
- Check MySQL is running
- Verify .env credentials:
  ```env
  DB_HOST=127.0.0.1
  DB_NAME=athletesGym
  DB_USER=root
  DB_PASS=
  ```
- Test connection in phpMyAdmin

### Problem: "CSRF token validation failed"
**Solution:**
- Already fixed! ✅
- If still happening: Clear browser cookies
- Check session is working (test_session.php)

### Problem: Cart empties randomly
**Solution:**
- Already fixed! ✅
- Cart only clears after payment confirmation
- Test cart persistence by navigating pages

### Problem: Emails not sending
**Solution:**
- Check Resend API key in .env
- Test with: test_resend_simple.php
- Check spam folder
- Verify API key at: https://resend.com/api-keys

---

## 📊 Testing Time Estimate

**Quick Test (Essential Features):** 15 minutes
- Homepage, login, shop, add to cart, checkout form

**Full Test (All Features):** 45-60 minutes
- Everything in this guide

**Recommended:** Start with Quick Test, then do Full Test before deployment

---

## 🚀 After Testing Successfully

Once all tests pass:

1. **Review Results:**
   - All checkboxes checked ✅
   - No critical errors
   - Core functionality works

2. **Prepare for Deployment:**
   - Update .env for production
   - Export database
   - Review DEPLOYMENT_CHECKLIST.md

3. **Deploy to Hostinger:**
   - Follow deployment guide
   - Test on live server
   - Verify MyFatoorah payments work

---

## 📝 Quick Test URLs Reference

```
Homepage:           http://localhost/athletesGym/
Register:           http://localhost/athletesGym/auth/register.php
Login:              http://localhost/athletesGym/auth/login.php
Forgot Password:    http://localhost/athletesGym/auth/forgot_password.php
Shop:               http://localhost/athletesGym/shop.php
Cart:               http://localhost/athletesGym/cart.php
Checkout:           http://localhost/athletesGym/checkout.php
Admin Login:        http://localhost/athletesGym/admin/login.php
Test Session:       http://localhost/athletesGym/test_session.php
Test Email:         http://localhost/athletesGym/test_resend_simple.php
Test Checkout:      http://localhost/athletesGym/checkout_debug.php
Test CSRF:          http://localhost/athletesGym/test_csrf.php
404 Page:           http://localhost/athletesGym/nonexistent-page
phpMyAdmin:         http://localhost/phpmyadmin
```

---

**Start Testing!** 🎯

Follow this guide step-by-step to ensure everything works perfectly before deploying to production.
