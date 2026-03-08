# 🚀 Quick Start - Test in 5 Minutes!

## Step-by-Step Testing for Beginners

---

## ⚡ 5-Minute Quick Test

### 1️⃣ Start XAMPP (30 seconds)

```
1. Open: C:\xampp\xampp-control.exe
2. Click "Start" next to Apache
3. Click "Start" next to MySQL
4. Wait for both to turn GREEN
```

### 2️⃣ Open Your Website (10 seconds)

```
Browser: http://localhost/athletesGym/
```

**Should see:** Homepage with gym content ✅

---

### 3️⃣ Test Registration (1 minute)

```
URL: http://localhost/athletesGym/auth/register.php

Fill in:
Name: Test User
Email: test@test.com
Phone: 12345678
Password: Test1234
Confirm: Test1234

Click "Register"
```

**Success:** Redirects to login/home ✅

---

### 4️⃣ Test Login (30 seconds)

```
URL: http://localhost/athletesGym/auth/login.php

Login with:
Email: test@test.com
Password: Test1234

Click "Login"
```

**Success:** Logged in, see your name in header ✅

---

### 5️⃣ Test Shopping (2 minutes)

```
1. Go to Shop: http://localhost/athletesGym/shop.php
2. Click any product
3. Click "Add to Cart"
4. Click cart icon in header
5. See item in cart ✅
6. Click "Proceed to Checkout"
7. Fill checkout form:
   Name: Test Customer
   Email: test@test.com
   Phone: 12345678
   Address: Test Address
8. Click "Place Order"
```

**Expected:**
- ✅ Order created (check database)
- ⚠️ MyFatoorah error (normal on localhost - works on production)

---

### 6️⃣ Test Password Reset (1 minute)

```
1. Go to: http://localhost/athletesGym/auth/forgot_password.php
2. Enter: test@test.com
3. Check database for OTP:
   phpMyAdmin → athletesGym → users → reset_otp column
4. Copy the 6-digit code
5. Enter code in verify page
6. Set new password: Test5678
7. Login with new password
```

**Success:** Password reset works ✅

---

### 7️⃣ Test Email (30 seconds)

```
URL: http://localhost/athletesGym/test_resend_simple.php

1. Enter your email
2. Select "OTP Email Test"
3. Click "Send Test Email"
4. Check your inbox
```

**Success:** Email arrives with professional design ✅

---

## ✅ Done! Your site works!

**What you tested:**
- ✅ Registration
- ✅ Login
- ✅ Shopping cart
- ✅ Checkout
- ✅ Password reset
- ✅ Email system

**Ready for production!** 🎉

---

## 🔗 Important URLs

```
Website:        http://localhost/athletesGym/
Admin Panel:    http://localhost/athletesGym/admin/
phpMyAdmin:     http://localhost/phpmyadmin
Test Email:     http://localhost/athletesGym/test_resend_simple.php
Test Session:   http://localhost/athletesGym/test_session.php
```

---

## 🆘 Quick Troubleshooting

### Apache won't start?
- Close Skype
- Stop IIS
- Restart XAMPP

### MySQL won't start?
- Stop other MySQL services
- Restart XAMPP

### Page not found?
- Check spelling: athletesGym (capital G)
- Verify Apache is running (green)

### Database error?
- Open phpMyAdmin
- Check database exists
- Verify .env settings

---

**Need detailed testing?** See: [LOCALHOST_TESTING_GUIDE.md](LOCALHOST_TESTING_GUIDE.md)
