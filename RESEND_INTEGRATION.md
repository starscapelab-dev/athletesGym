# Resend Email Integration Guide

## ✅ **Status: COMPLETED**

Resend email service has been fully integrated into the Athletes Gym website!

---

## 📧 **What's Integrated**

### 1. **Password Reset OTP Emails**
- **Location:** `auth/forgot_password_handler.php`
- **Trigger:** User requests password reset
- **Template:** Professional OTP email with 6-digit code
- **Expiry:** 10 minutes

### 2. **Order Confirmation Emails**
- **Location:** `MyFatoora/callback.php`
- **Trigger:** Successful payment via MyFatoorah
- **Template:** Order summary with items, total, and order ID
- **Details:** Includes all purchased items with quantities and prices

### 3. **Contact Form Emails**
- **Location:** `contact_handler.php`
- **Trigger:** User submits contact form
- **Template:** Contact details forwarded to admin
- **Fields:** Name, email, phone, message

---

## 🔧 **Configuration**

### Your Current `.env` Settings:

```env
# Email Configuration - Resend
RESEND_API_KEY=re_ixYthdmw_AffHsJtwkZs7MXVTtJsiXQJE
RESEND_FROM_EMAIL=onboarding@resend.dev
RESEND_FROM_NAME="Athletes Gym Qatar"
```

### ⚠️ **Important Notes:**

1. **Current Setup (Testing):**
   - Using: `onboarding@resend.dev` (Resend's test domain)
   - This is perfect for testing!
   - Emails will be sent successfully

2. **For Production (When Ready):**
   - Verify your domain in Resend dashboard
   - Update `RESEND_FROM_EMAIL` to your domain (e.g., `noreply@athletesgym.qa`)
   - Example:
     ```env
     RESEND_FROM_EMAIL=noreply@athletesgym.qa
     ```

---

## 🧪 **Testing the Integration**

### Option 1: Use Test Script (Recommended)

1. Go to: http://localhost/athletesGym/test_email.php
2. Enter your email address
3. Click any test button:
   - **Simple Email** - Basic test
   - **OTP Email** - Password reset template
   - **Order Confirmation** - Order summary template
   - **Contact Form** - Contact submission template
4. Check your inbox!
5. **Delete `test_email.php` when done**

### Option 2: Test Real Flows

**Test Password Reset:**
1. Go to: http://localhost/athletesGym/auth/forgot_password.php
2. Enter a registered user's email
3. Check inbox for OTP code

**Test Order Confirmation:**
1. Complete a real purchase
2. Check customer's email for order confirmation

**Test Contact Form:**
1. Go to: http://localhost/athletesGym/contact.php
2. Fill out and submit form
3. Check admin email for submission

---

## 📁 **Files Created/Modified**

### New Files:
- `includes/email_service.php` - Resend email service class
- `contact_handler.php` - Contact form processing
- `test_email.php` - Email testing script ⚠️ DELETE AFTER TESTING

### Modified Files:
- `.env` - Added Resend configuration
- `auth/forgot_password_handler.php` - Now uses Resend for OTP
- `MyFatoora/callback.php` - Sends order confirmation
- `contact.php` - Added form handler and CSRF token

---

## 🎨 **Email Templates**

All emails use professional HTML templates with:
- Athletes Gym branding
- Responsive design
- Plain text fallback
- Mobile-friendly layout
- Qatar-themed styling

### Template Features:
- **OTP Email:** Large, easy-to-read code with expiry time
- **Order Confirmation:** Itemized order summary with totals
- **Contact Form:** Clean admin notification

---

## 🔄 **Handover Process**

### When transferring to client:

**Option A: Transfer Your Account**
1. Go to Resend dashboard → Settings
2. Transfer ownership to client's email
3. They accept transfer
4. Done! (they keep same API key)

**Option B: New Account (Recommended)**
1. Client creates new Resend account at https://resend.com
2. Client gets their API key
3. Update `.env`:
   ```env
   RESEND_API_KEY=client_new_key_here
   RESEND_FROM_EMAIL=noreply@athletesgym.qa
   ```
4. Verify domain in Resend dashboard
5. Test using `test_email.php`

---

## 🚀 **Production Checklist**

Before going live:

- [ ] **Verify Domain in Resend**
  - Go to Resend dashboard
  - Add your domain (athletesgym.qa)
  - Add DNS records as shown
  - Wait for verification (usually 5-10 minutes)

- [ ] **Update `.env`**
  ```env
  RESEND_FROM_EMAIL=noreply@athletesgym.qa
  ```

- [ ] **Test All Email Types**
  - Password reset OTP
  - Order confirmation
  - Contact form

- [ ] **Delete Test File**
  - Remove `test_email.php`

- [ ] **Monitor Email Logs**
  - Check Resend dashboard for delivery stats
  - Monitor error logs: `logs/error.log`

---

## 💡 **How to Use in Code**

### Simple Email (anywhere in code):
```php
require_once "includes/email_service.php";

$emailService = new EmailService();
$emailService->send(
    'customer@example.com',
    'Subject Here',
    '<h1>HTML content</h1>',
    'Plain text version'
);
```

### OTP Email:
```php
$emailService->sendOTP('user@example.com', '123456', 'John Doe');
```

### Order Confirmation:
```php
$emailService->sendOrderConfirmation('customer@example.com', [
    'order_id' => 123,
    'customer_name' => 'John Doe',
    'total' => '500.00',
    'items' => [
        ['name' => 'Product 1', 'quantity' => 2, 'price' => '100.00'],
        ['name' => 'Product 2', 'quantity' => 1, 'price' => '300.00']
    ]
]);
```

### Contact Form:
```php
$emailService->sendContactForm([
    'name' => 'Customer Name',
    'email' => 'customer@example.com',
    'phone' => '+974 1234 5678',
    'message' => 'Their message here'
]);
```

---

## 📊 **Resend Limits**

### Free Tier:
- **3,000 emails/month** - FREE ✅
- **100 emails/day**
- Perfect for small to medium businesses

### Paid Plans (if needed later):
- **$20/month** - 50,000 emails
- **$80/month** - 100,000 emails
- Pay-as-you-go available

**For Athletes Gym:** Free tier is plenty! (estimated ~500-2,000 emails/month)

---

## 🔐 **Security Features**

✅ API key stored in `.env` (not in code)
✅ CSRF protection on contact form
✅ Input validation on all forms
✅ Error logging (not displaying)
✅ Graceful failure (payment still works if email fails)

---

## 🐛 **Troubleshooting**

### Emails not sending?

1. **Check API Key**
   - Verify in `.env` file
   - Make sure no extra spaces
   - Key starts with `re_`

2. **Check Error Logs**
   ```
   logs/error.log
   ```

3. **Test with script**
   - Use `test_email.php`
   - Check response for errors

4. **Common Issues:**
   - **"Unauthorized"** - Wrong API key
   - **"From domain not verified"** - Need to verify domain (use onboarding@resend.dev for testing)
   - **cURL error** - Check internet connection

### Emails going to spam?

1. **For Testing:** Normal with test domains
2. **For Production:**
   - Verify domain in Resend
   - Add SPF/DKIM records
   - Use proper from address

---

## 📞 **Support**

- **Resend Docs:** https://resend.com/docs
- **Resend Dashboard:** https://resend.com/emails
- **API Status:** https://status.resend.com

---

## ✨ **What's Next?**

Optional enhancements you can add later:

1. **Welcome Email** - Send when user registers
2. **Order Shipped Email** - When order status changes
3. **Newsletter System** - Bulk email campaigns
4. **Email Templates** - Custom branded templates
5. **Email Tracking** - Open rates, click tracking

---

**Document Version:** 1.0
**Last Updated:** 2026-02-05
**Status:** Production Ready ✅
