# Hostinger Deployment Guide - Subdomain Testing

**Project:** Athletes Gym Website
**Test URL:** athletesgym.akshayvt.com
**Date:** March 1, 2026

---

## 📋 Prerequisites

Before you start, you'll need:
- ✅ Hostinger account with active hosting plan
- ✅ Access to hPanel (Hostinger control panel)
- ✅ Domain akshayvt.com already added to your hosting
- ✅ Your Athletes Gym website files
- ✅ Database backup file (wkfbjlmy_WPATH.sql)

---

## 🚀 STEP-BY-STEP DEPLOYMENT

### STEP 1: Create Subdomain in hPanel

1. **Log into Hostinger hPanel**
   - Go to: https://hpanel.hostinger.com
   - Login with your credentials

2. **Navigate to Domains Section**
   - Click on "Domains" in the left sidebar
   - Find your domain: `akshayvt.com`
   - Click on "Manage"

3. **Create Subdomain**
   - Scroll to "Subdomains" section
   - Click "Create Subdomain"
   - Enter subdomain name: `athletesgym`
   - Document root should auto-fill as: `public_html/athletesgym`
   - Click "Create"

**Result:** Subdomain `athletesgym.akshayvt.com` is now created

---

### STEP 2: Upload Website Files

#### Option A: Using File Manager (Recommended for beginners)

1. **Open File Manager**
   - In hPanel, click "File Manager"
   - Navigate to: `public_html/athletesgym/`

2. **Upload Files**
   - Click "Upload Files" button
   - Create a ZIP file of your Athletes Gym folder first:
     ```
     athletesGym/
     ├── admin/
     ├── auth/
     ├── includes/
     ├── layouts/
     ├── MyFatoora/
     ├── uploads/
     ├── index.php
     ├── shop.php
     ├── checkout.php
     └── ... (all other files)
     ```
   - Upload the ZIP file
   - Right-click the ZIP file → Extract
   - Delete the ZIP file after extraction

3. **Verify Structure**
   - Your files should be in: `public_html/athletesgym/`
   - NOT in: `public_html/athletesgym/athletesGym/`
   - If nested, move everything up one level

#### Option B: Using FTP (For advanced users)

1. **Get FTP Credentials**
   - In hPanel, go to "FTP Accounts"
   - Note: Hostname, Username, Password, Port (21)

2. **Connect with FTP Client**
   - Download FileZilla: https://filezilla-project.org/
   - Host: `ftp.yourhostingserver.com` (from hPanel)
   - Username: Your FTP username
   - Password: Your FTP password
   - Port: 21

3. **Upload Files**
   - Navigate to: `/public_html/athletesgym/`
   - Upload all files from your local `athletesGym` folder
   - This may take 10-15 minutes depending on connection speed

---

### STEP 3: Create MySQL Database

1. **Open MySQL Databases**
   - In hPanel, click "MySQL Databases"
   - Click "Create Database"

2. **Create Database**
   - Database name: `athletesgym_db` (or any name you prefer)
   - Click "Create"
   - **Note down the database name** (will be prefixed with your username)
   - Example: `u123456789_athletesgym_db`

3. **Create Database User**
   - In the same page, scroll to "MySQL Users"
   - Click "Create User"
   - Username: `athletesgym_user`
   - Password: **Generate a strong password** (click generate)
   - **IMPORTANT: Save this password securely!**
   - Click "Create User"

4. **Link User to Database**
   - Scroll to "Add User to Database"
   - Select user: `athletesgym_user`
   - Select database: `athletesgym_db`
   - Grant all privileges
   - Click "Add"

**Note down these details:**
```
Database Name: u123456789_athletesgym_db
Database User: u123456789_athletesgym_user
Database Password: [the password you generated]
Database Host: localhost
```

---

### STEP 4: Import Database

1. **Open phpMyAdmin**
   - In hPanel, click "phpMyAdmin"
   - Select your database from left sidebar: `u123456789_athletesgym_db`

2. **Import SQL File**
   - Click "Import" tab at the top
   - Click "Choose File"
   - Select your `wkfbjlmy_WPATH.sql` file
   - Scroll down and click "Go"
   - Wait for import to complete (may take 1-2 minutes)

3. **Verify Import**
   - Click on your database name in left sidebar
   - You should see tables: users, products, orders, categories, etc.
   - If tables are visible, import was successful ✅

---

### STEP 5: Configure .env File for Testing

1. **Open File Manager**
   - Navigate to: `public_html/athletesgym/`
   - Find the `.env` file

2. **Edit .env File**
   - Right-click `.env` → Edit
   - Update with these values:

```env
# Database Configuration (UPDATE THESE)
DB_HOST=localhost
DB_NAME=u123456789_athletesgym_db
DB_USER=u123456789_athletesgym_user
DB_PASS=YOUR_GENERATED_PASSWORD_HERE

# MyFatoorah Payment Gateway (KEEP TEST MODE FOR NOW)
MYFATOORAH_API_KEY=gfOTCr56XFqO6g1ENMzvC4t_6mbjhJ_M0sKvowTkawPRq5qAWDj0E9pkcCm_M2p9AvmmyNDYOEvSFMgSS5bwVgtpQiEIzCJqAfK5Yz8jn2EkeVbXKdrfU-nEHsDtss3ZnrvSAerGPt_FXq6WggzdGsuo7zHr25pfRPbPwlJYPhNT940hS13NK4PiDLhEjQHYpWUqKMWlZuX8N461XqABRbdPv8tZbksRJPJjFQjISgJ53741eWj9njI2AKd5vDwAC8j3LuXLqvpy7c5hVk6yhKoXU2BsB3j-wQMIVQV78083LctQDfXtmBZs_kyge-SWwf7eWaklEvuY6w-xk2JAwEhEb0xdC8BuTT0uv4srR-0lTolAGwrg0LNoADSJai-DNYt0Opm_sqogBz-Olh5-Pt6_Q94K0I6odJc4av80wNzXFvdmL8MYKVqZJdfPTz27AEjWqrUbfbfagUberVPdPSfKvSpq_MAIy1450wcUezi56dq7lDg9_0HprlTA8hKQtG2miUU6UUdgVKic_AWfAEvWZ7b8Jq-NWsUI57Yq9k1ieJRtifY3ZsZUQQW_wkG6AKX3YY3lkWTR0TfB4sarvzl3I9XLy1gZ8-GGuXzGg_kNLLTFZs4aX9nIsDoxPtpbv_vNuShu5lL1HJiQ-cQordl0AyMAnXIbNosz3knzPDtIiopa
MYFATOORAH_BASE_URL=https://apitest.myfatoorah.com
MYFATOORAH_TEST_MODE=true

# Application Settings (TESTING MODE)
APP_ENV=development
APP_DEBUG=true
APP_URL=https://athletesgym.akshayvt.com

# Email Configuration - Resend
RESEND_API_KEY=re_ixYthdmw_AffHsJtwkZs7MXVTtJsiXQJE
RESEND_FROM_EMAIL=onboarding@resend.dev
RESEND_FROM_NAME="Athletes Gym Qatar"
RESEND_ADMIN_EMAIL=starscapelab@gmail.com

# Session Configuration (UPDATE WHEN SSL IS ACTIVE)
SESSION_LIFETIME=7200
SESSION_SECURE=false

# Security
CSRF_TOKEN_NAME=csrf_token
```

3. **Save the file**
   - Click "Save Changes"
   - Click "Close"

**IMPORTANT NOTES:**
- Replace `u123456789_athletesgym_db` with YOUR actual database name
- Replace `u123456789_athletesgym_user` with YOUR actual database user
- Replace `YOUR_GENERATED_PASSWORD_HERE` with your actual password
- Keep `APP_DEBUG=true` for testing (we'll change it later)
- Keep `SESSION_SECURE=false` until SSL is setup

---

### STEP 6: Set File Permissions

1. **Set Permissions for Uploads Directory**
   - In File Manager, navigate to: `public_html/athletesgym/uploads/`
   - Right-click on `uploads` folder → Permissions
   - Set to: **755** (or check: Read, Write, Execute for owner; Read, Execute for others)
   - Check "Recurse into subdirectories"
   - Click "Change Permissions"

2. **Create Logs Directory**
   - In `public_html/athletesgym/`, create new folder: `logs`
   - Right-click `logs` → Permissions → Set to **755**

3. **Secure .env File**
   - Right-click `.env` → Permissions
   - Set to: **644** (Read/Write for owner, Read for others)

---

### STEP 7: Enable SSL Certificate (HTTPS)

1. **Install SSL**
   - In hPanel, go to "SSL" section
   - Find `athletesgym.akshayvt.com`
   - Click "Install SSL"
   - Choose "Free Let's Encrypt SSL"
   - Click "Install"
   - Wait 5-10 minutes for activation

2. **Force HTTPS** (After SSL is active)
   - Navigate to File Manager: `public_html/athletesgym/`
   - Create or edit `.htaccess` file
   - Add this code at the top:

```apache
# Force HTTPS
RewriteEngine On
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]

# Custom 404 Error Page
ErrorDocument 404 /404.html

# Security Headers
<IfModule mod_headers.c>
    Header set X-Content-Type-Options "nosniff"
    Header set X-Frame-Options "SAMEORIGIN"
    Header set X-XSS-Protection "1; mode=block"
</IfModule>
```

3. **Update .env for SSL**
   - Edit `.env` file
   - Change: `SESSION_SECURE=false` to `SESSION_SECURE=true`
   - Save

---

### STEP 8: Configure PHP Version

1. **Set PHP Version**
   - In hPanel, go to "Advanced" → "PHP Configuration"
   - Select your subdomain: `athletesgym.akshayvt.com`
   - Choose PHP version: **8.0** or **8.1** (recommended)
   - Click "Update"

2. **Verify PHP Settings**
   - In same page, check these values:
     - `upload_max_filesize`: 64M or higher
     - `post_max_size`: 64M or higher
     - `max_execution_time`: 300 or higher
     - `memory_limit`: 256M or higher

---

### STEP 9: Test the Website

1. **Visit Your Website**
   - Open browser
   - Go to: `https://athletesgym.akshayvt.com`
   - You should see the homepage

2. **Test Basic Functions**
   - [ ] Homepage loads correctly
   - [ ] Navigation menu works
   - [ ] Shop page displays products
   - [ ] Product details page works
   - [ ] Images load correctly

3. **Test User Features**
   - [ ] Register new account (test with 8+ character password)
   - [ ] Login with account
   - [ ] Add product to cart
   - [ ] View cart
   - [ ] Update cart quantities
   - [ ] Remove from cart
   - [ ] Proceed to checkout
   - [ ] Fill checkout form
   - [ ] Test payment (will use test mode)

4. **Test Admin Panel**
   - Go to: `https://athletesgym.akshayvt.com/admin/`
   - Login with admin credentials
   - Test adding a product
   - Test editing a product
   - Test viewing orders
   - Test managing categories

5. **Test Email System**
   - Try "Forgot Password" feature
   - Check if OTP email is received
   - Try contact form
   - Place a test order and check confirmation email

---

### STEP 10: Common Issues & Solutions

#### Issue 1: "Database Connection Failed"
**Solution:**
- Double-check database credentials in `.env`
- Verify database name includes hosting prefix
- Check database user has all privileges
- Test connection in phpMyAdmin

#### Issue 2: "Page Not Found" or 404 Errors
**Solution:**
- Check file permissions (755 for directories, 644 for files)
- Verify .htaccess file exists
- Check that files are in correct directory (not nested)
- Enable mod_rewrite in PHP Configuration

#### Issue 3: Images Not Loading
**Solution:**
- Check uploads/ directory permissions (755)
- Verify images exist in uploads/ folder
- Check image paths in database
- Upload sample images to test

#### Issue 4: "CSRF Token Validation Failed"
**Solution:**
- Clear browser cookies
- Check that session is working
- Verify `includes/csrf.php` exists
- If SSL is active, ensure `SESSION_SECURE=true` in `.env`

#### Issue 5: Emails Not Sending
**Solution:**
- Verify Resend API key is correct
- Check `includes/email_service.php` exists
- Review error logs in `logs/error.log`
- Test with a simple email first

#### Issue 6: "Internal Server Error" (500)
**Solution:**
- Check `.htaccess` for syntax errors
- Review PHP error logs in hPanel
- Verify PHP version is 8.0+
- Check file permissions
- Review `logs/error.log`

#### Issue 7: Admin Panel Not Accessible
**Solution:**
- Go to: `https://athletesgym.akshayvt.com/admin/index.php`
- Check admin credentials in database (admins table)
- Reset admin password if needed using script

---

### STEP 11: Create Admin Account (If needed)

If you need to create an admin account:

1. **Using phpMyAdmin**
   - Open phpMyAdmin
   - Select your database
   - Click "admins" table
   - Click "Insert" tab
   - Fill in:
     - `username`: admin
     - `password`: (leave blank for now)
     - `email`: admin@athletesgym.qa
   - Click "Go"

2. **Set Password**
   - Navigate to: `https://athletesgym.akshayvt.com/admin/reset_admin_password.php`
   - Follow instructions to set password

OR upload this file to `public_html/athletesgym/` as `create_admin.php`:

```php
<?php
require_once "admin/includes/db.php";

$username = "admin";
$password = "Admin@123456"; // Change this!
$email = "admin@athletesgym.qa";

$hash = password_hash($password, PASSWORD_DEFAULT);

try {
    $stmt = $pdo->prepare("INSERT INTO admins (username, password, email) VALUES (?, ?, ?)");
    $stmt->execute([$username, $hash, $email]);
    echo "Admin created successfully!<br>";
    echo "Username: $username<br>";
    echo "Password: $password<br>";
    echo "<strong>DELETE THIS FILE IMMEDIATELY!</strong>";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
```

Then:
- Visit: `https://athletesgym.akshayvt.com/create_admin.php`
- Note the credentials
- **DELETE the file immediately!**

---

## 🔧 OPTIONAL: Advanced Configuration

### Enable Error Logging

Add to `.htaccess`:
```apache
# Enable error logging
php_flag display_errors off
php_flag log_errors on
php_value error_log /home/yourusername/public_html/athletesgym/logs/php_error.log
```

### Improve Performance

Add to `.htaccess`:
```apache
# Enable Gzip Compression
<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/html text/plain text/xml text/css text/javascript application/javascript
</IfModule>

# Browser Caching
<IfModule mod_expires.c>
    ExpiresActive On
    ExpiresByType image/jpg "access plus 1 year"
    ExpiresByType image/jpeg "access plus 1 year"
    ExpiresByType image/gif "access plus 1 year"
    ExpiresByType image/png "access plus 1 year"
    ExpiresByType text/css "access plus 1 month"
    ExpiresByType application/javascript "access plus 1 month"
</IfModule>
```

---

## 📊 Testing Checklist

Before showing to client:

### Functionality Tests:
- [ ] Homepage loads
- [ ] All navigation links work
- [ ] Shop page displays products
- [ ] Product search works
- [ ] Product details page loads
- [ ] Add to cart works
- [ ] Cart displays correctly
- [ ] Checkout process works
- [ ] User registration works (8+ chars password)
- [ ] User login works
- [ ] Logout works
- [ ] Forgot password / OTP works
- [ ] Contact form works
- [ ] Admin login works
- [ ] Admin can add products
- [ ] Admin can edit products
- [ ] Admin can manage orders
- [ ] Payment test completes (test mode)

### Security Tests:
- [ ] HTTPS is active
- [ ] CSRF tokens present on all forms
- [ ] Admin panel requires login
- [ ] Can't access other users' orders
- [ ] Passwords enforced (8+ characters)
- [ ] Error messages don't expose system info

### Performance Tests:
- [ ] Page load time < 3 seconds
- [ ] Images load properly
- [ ] Mobile responsive design works
- [ ] No console errors in browser

---

## 🚀 WHEN READY FOR PRODUCTION

### Switch to Production Mode:

1. **Update .env file:**
```env
APP_ENV=production
APP_DEBUG=false          # CRITICAL
SESSION_SECURE=true      # If HTTPS is active
MYFATOORAH_TEST_MODE=false  # For live payments
```

2. **Update Payment Settings:**
   - Get production MyFatoorah API key
   - Update `MYFATOORAH_API_KEY` in `.env`
   - Change `MYFATOORAH_BASE_URL` to: `https://api.myfatoorah.com`

3. **Update Email Settings:**
   - Verify your domain in Resend
   - Update `RESEND_FROM_EMAIL` to: `noreply@athletesgym.qa`

4. **Test Everything Again:**
   - Test with real payment (small amount)
   - Verify emails send correctly
   - Check error logging works
   - Monitor for any issues

---

## 📞 Need Help?

**Common Resources:**
- Hostinger Help Center: https://support.hostinger.com
- Hostinger Live Chat: Available in hPanel
- PHP Documentation: https://www.php.net/docs.php

**Check These First:**
1. Error logs in: `logs/error.log`
2. PHP error log in hPanel
3. Database connection in phpMyAdmin
4. File permissions (755/644)

---

## ✅ Deployment Summary

Once completed, you'll have:
- ✅ Website running at: `https://athletesgym.akshayvt.com`
- ✅ SSL certificate installed (HTTPS)
- ✅ Database configured and working
- ✅ All security fixes active
- ✅ Email system operational
- ✅ Admin panel accessible
- ✅ Ready for testing

**Estimated Time:** 1-2 hours (depending on file upload speed)

---

**Good luck with your deployment! 🚀**
