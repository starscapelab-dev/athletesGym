# Deployment Checklist - Athletes Gym

## ✅ What's Already Working Locally
- CSRF protection on all forms
- Session management
- Cart persistence
- Order creation and stock management
- Error handling and user feedback
- Phone number formatting for MyFatoorah
- Dynamic callback URLs based on environment

---

## 🔧 Required Changes Before Uploading

### 1. Update .env File for Production

**File:** `.env`

**Changes needed:**

```env
# Database Configuration (Update with hosting database credentials)
DB_HOST=localhost
DB_NAME=your_production_database_name
DB_USER=your_production_database_user
DB_PASS=your_production_database_password

# MyFatoorah Payment Gateway
MYFATOORAH_API_KEY=your_production_api_key_here
MYFATOORAH_BASE_URL=https://api.myfatoorah.com  # Change from apitest to api
MYFATOORAH_TEST_MODE=false  # Change from true to false

# Application Settings
APP_ENV=production  # Change from development
APP_DEBUG=false  # Change from true - IMPORTANT for security
APP_URL=https://athletesgym.qa  # Your production domain

# Email Configuration - Resend
RESEND_API_KEY=re_ixYthdmw_AffHsJtwkZs7MXVTtJsiXQJE
RESEND_FROM_EMAIL=noreply@athletesgym.qa  # Change to your domain
RESEND_FROM_NAME="Athletes Gym Qatar"
RESEND_ADMIN_EMAIL=starscapelab@gmail.com

# Session Configuration
SESSION_LIFETIME=7200
SESSION_SECURE=true  # Change from false - Forces HTTPS for cookies

# Security
CSRF_TOKEN_NAME=csrf_token
```

### 2. Get Production MyFatoorah API Key

**Important:** The test API key you're using now will NOT work in production.

**Steps:**
1. Login to MyFatoorah dashboard: https://portal.myfatoorah.com/
2. Switch to **Live Mode** (not Test Mode)
3. Go to **Integration Settings** → **API Keys**
4. Copy your **Live API Key**
5. Update `.env`: `MYFATOORAH_API_KEY=your_live_api_key_here`
6. Update `.env`: `MYFATOORAH_TEST_MODE=false`
7. Update `.env`: `MYFATOORAH_BASE_URL=https://api.myfatoorah.com`

---

## 📤 Upload Process

### Method 1: FTP/SFTP Upload (Recommended)

1. **Connect to your hosting via FTP**
   - Host: Your hosting FTP address
   - Username: Your FTP username
   - Password: Your FTP password
   - Port: 21 (FTP) or 22 (SFTP)

2. **Upload these folders/files:**
   ```
   ✅ admin/
   ✅ assets/
   ✅ auth/
   ✅ cards/
   ✅ includes/
   ✅ layouts/
   ✅ MyFatoora/
   ✅ uploads/
   ✅ .env (after updating for production)
   ✅ .htaccess
   ✅ *.php files (all root PHP files)
   ```

3. **DO NOT upload:**
   ```
   ❌ .git/
   ❌ test_*.php files (optional - these are just for testing)
   ❌ *_DEBUG.md files
   ❌ *_FIX.md files
   ❌ node_modules/ (if exists)
   ```

### Method 2: cPanel File Manager

1. Login to cPanel
2. Go to **File Manager**
3. Navigate to `public_html` or your domain folder
4. Click **Upload**
5. Zip your athletesGym folder locally first
6. Upload the zip file
7. Extract in the correct directory
8. Update .env file using File Manager editor

---

## 🗄️ Database Setup on Hosting

### Option 1: Export and Import

**On localhost:**
```bash
# Export database
mysqldump -u root wkfbjlmy_WPATH > athletesgym_backup.sql
```

**On hosting (cPanel phpMyAdmin):**
1. Login to cPanel → phpMyAdmin
2. Create new database (or use existing)
3. Click **Import**
4. Upload `athletesgym_backup.sql`
5. Click **Go**
6. Update `.env` with new database credentials

### Option 2: Remote Database Connection

If your hosting allows remote MySQL connections, you can keep using your current database for testing, but this is NOT recommended for production.

---

## ⚙️ After Upload - Configuration Check

### 1. File Permissions

Set correct permissions via FTP or cPanel:

```
Folders: 755
Files: 644
.env file: 600 (secure - not readable by others)
uploads/ folder: 755 or 775 (needs write permission)
```

**Via cPanel File Manager:**
- Right-click folder/file → **Change Permissions**
- Set as shown above

### 2. Test These URLs

After uploading, test each:

✅ Homepage:
```
https://athletesgym.qa/
```

✅ Login:
```
https://athletesgym.qa/auth/login.php
```

✅ Shop:
```
https://athletesgym.qa/shop.php
```

✅ Cart:
```
https://athletesgym.qa/cart.php
```

✅ Checkout:
```
https://athletesgym.qa/checkout.php
```

✅ Admin:
```
https://athletesgym.qa/admin/login.php
```

### 3. Test Checkout Flow (Full Test)

1. **Create test account** or login
2. **Add item to cart**
3. **Go to checkout**
4. **Fill in form** with real details
5. **Click "Place Order"**
6. **Should redirect** to MyFatoorah payment page
7. **Complete payment** (use test card if in test mode)
8. **Should redirect back** to order success page
9. **Check database**: Order status should update to "paid"
10. **Check cart**: Should be cleared

---

## 🔒 Security Checklist

Before going live, verify:

- [ ] `.env` has `APP_DEBUG=false`
- [ ] `.env` has `APP_ENV=production`
- [ ] `.env` has `SESSION_SECURE=true`
- [ ] `.env` has `MYFATOORAH_TEST_MODE=false`
- [ ] `.env` file permissions set to 600
- [ ] Remove or disable test files (test_*.php, checkout_debug.php)
- [ ] SSL certificate is active (HTTPS working)
- [ ] Database password is strong
- [ ] Admin password is strong
- [ ] MyFatoorah Live API key is configured

---

## 🚨 Common Issues After Upload

### Issue 1: "500 Internal Server Error"

**Cause:** Syntax error or wrong permissions

**Fix:**
1. Check error logs: cPanel → **Error Log**
2. Check .htaccess file exists
3. Check file permissions (folders: 755, files: 644)
4. Check PHP version (needs PHP 8.0+)

### Issue 2: "Database Connection Failed"

**Cause:** Wrong database credentials in .env

**Fix:**
1. Double-check DB_HOST, DB_NAME, DB_USER, DB_PASS in .env
2. Host might be "localhost" or "127.0.0.1" or specific IP
3. Test connection using cPanel phpMyAdmin

### Issue 3: "Page not found" or blank pages

**Cause:** .htaccess not working or mod_rewrite disabled

**Fix:**
1. Ensure .htaccess file is uploaded
2. Contact hosting support to enable mod_rewrite
3. Check APP_URL in .env matches your domain

### Issue 4: Images not loading

**Cause:** Wrong BASE_URL or uploads folder permissions

**Fix:**
1. Check `layouts/config.php` BASE_URL setting
2. Set uploads folder permission to 755 or 775
3. Ensure images exist in uploads/ folder

### Issue 5: MyFatoorah still shows error

**Cause:** Still using test API key or test mode enabled

**Fix:**
1. Update to LIVE API key in .env
2. Set `MYFATOORAH_TEST_MODE=false`
3. Set `MYFATOORAH_BASE_URL=https://api.myfatoorah.com` (not apitest)
4. Check callback URL is accessible: https://athletesgym.qa/MyFatoora/callback.php

### Issue 6: Sessions not working / Cart emptying

**Cause:** Session path not writable or session cookies not working

**Fix:**
1. Check hosting session path is writable
2. Ensure `SESSION_SECURE=true` in .env (forces HTTPS)
3. Clear browser cookies and try again
4. Check if HTTPS is working (no mixed content)

---

## 📧 Email Configuration (Resend)

Your current setup uses Resend for emails. After deployment:

### 1. Verify Domain in Resend

1. Login to Resend: https://resend.com/
2. Go to **Domains**
3. Add your domain: `athletesgym.qa`
4. Add DNS records to your domain (in your domain registrar/DNS settings)
5. Wait for verification
6. Update `.env`: `RESEND_FROM_EMAIL=noreply@athletesgym.qa`

### 2. Test Email Sending

After domain verification, test:
- User registration email
- Order confirmation email
- Password reset email

If emails not working:
- Check Resend dashboard for errors
- Check error logs on hosting
- Verify API key is correct in .env

---

## 🧪 Testing Checklist After Deployment

### Basic Functionality
- [ ] Homepage loads
- [ ] Can browse products
- [ ] Can view product details
- [ ] Can register new account
- [ ] Can login
- [ ] Can add items to cart
- [ ] Cart persists after navigation
- [ ] Can update cart quantities
- [ ] Can remove items from cart

### Checkout Process
- [ ] Can access checkout page
- [ ] Form shows prefilled user data
- [ ] CSRF token working (no reload on submit)
- [ ] Error messages display if validation fails
- [ ] Redirects to MyFatoorah on success
- [ ] Can complete payment
- [ ] Redirects back after payment
- [ ] Order shows in admin panel
- [ ] Stock is deducted correctly
- [ ] Cart is cleared after successful payment
- [ ] Order confirmation email sent

### Admin Panel
- [ ] Can login to admin
- [ ] Can view orders
- [ ] Can update order status
- [ ] Can manage products
- [ ] Can manage categories
- [ ] Can view reports/statistics

### Security
- [ ] HTTPS is working (green padlock)
- [ ] Debug mode is OFF (no errors visible to users)
- [ ] .env file is not accessible via browser
- [ ] Admin area requires authentication
- [ ] CSRF protection working on all forms
- [ ] SQL injection protection working
- [ ] Session security working

---

## 📞 Support Resources

### If You Need Help:

1. **Hosting Support**
   - Check your hosting provider's documentation
   - Contact support for server-specific issues
   - Ask about PHP version, mod_rewrite, session configuration

2. **MyFatoorah Support**
   - Documentation: https://myfatoorah.readme.io/
   - Support: Contact via MyFatoorah dashboard
   - Test your integration using their test tools

3. **Check Error Logs**
   - cPanel → Error Log
   - Shows PHP errors and warnings
   - Helps identify specific issues

---

## 📋 Quick Reference - What to Update

| File | Setting | Change From | Change To |
|------|---------|-------------|-----------|
| .env | APP_ENV | development | production |
| .env | APP_DEBUG | true | false |
| .env | APP_URL | http://localhost/athletesGym | https://athletesgym.qa |
| .env | SESSION_SECURE | false | true |
| .env | MYFATOORAH_TEST_MODE | true | false |
| .env | MYFATOORAH_BASE_URL | https://apitest.myfatoorah.com | https://api.myfatoorah.com |
| .env | MYFATOORAH_API_KEY | test_key... | live_key... |
| .env | DB_HOST | localhost | (hosting provided) |
| .env | DB_NAME | wkfbjlmy_WPATH | (hosting provided) |
| .env | DB_USER | root | (hosting provided) |
| .env | DB_PASS | (empty) | (hosting provided) |
| .env | RESEND_FROM_EMAIL | onboarding@resend.dev | noreply@athletesgym.qa |

---

## ✅ Final Steps

1. Update .env with production values
2. Export database from localhost
3. Upload files via FTP/cPanel
4. Import database on hosting
5. Set file permissions
6. Test all URLs
7. Complete a test order
8. Monitor error logs
9. Go live! 🚀

---

**You're ready to deploy!** The code is working correctly - just follow this checklist to ensure smooth deployment.
