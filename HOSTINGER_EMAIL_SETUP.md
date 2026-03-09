# Hostinger Email Setup Guide

This guide explains how to set up and use Hostinger's PHP mail() function for sending emails from your Athletes Gym website.

## Features

- ✅ **No External Dependencies** - Uses PHP's native `mail()` function
- ✅ **Hostinger Optimized** - Works seamlessly with Hostinger hosting
- ✅ **Beautiful HTML Templates** - Professional email designs for all notifications
- ✅ **Multiple Email Types** - OTP, Order Confirmation, Contact Form, Welcome emails

## Setup Instructions

### 1. Create Email Account in Hostinger cPanel

1. Login to your Hostinger account
2. Go to **hPanel** (Hostinger Panel)
3. Navigate to **Emails** → **Email Accounts**
4. Click **Create Email Account**
5. Create the following email addresses:
   - `noreply@athletesgym.qa` (for system emails)
   - `info@athletesgym.qa` (for customer inquiries)
   - `admin@athletesgym.qa` (for admin notifications)

### 2. Configure SPF and DKIM Records

For better email deliverability, set up SPF and DKIM:

1. In hPanel, go to **Domains** → **DNS / Nameservers**
2. **SPF Record** (if not already present):
   - Type: `TXT`
   - Name: `@`
   - Value: `v=spf1 include:_spf.hostinger.com ~all`
   - TTL: `14400`

3. **DKIM Record**:
   - In hPanel, go to **Emails** → **Authentication**
   - Enable **DKIM**
   - Copy the DKIM record and add it to your DNS

### 3. Update .env File

Add these lines to your `.env` file:

```env
# Email Configuration
MAIL_FROM_ADDRESS=noreply@athletesgym.qa
MAIL_FROM_NAME="Athletes Gym Qatar"
MAIL_REPLY_TO=info@athletesgym.qa
MAIL_ADMIN_ADDRESS=admin@athletesgym.qa

# Application URL
APP_URL=https://athletesgym.qa
```

### 4. Update Your Code

Replace the existing email service with Hostinger email service:

**Old Code (using Resend):**
```php
require_once __DIR__ . '/includes/email_service.php';
$emailService = new EmailService();
$emailService->sendOTP($email, $otp, $userName);
```

**New Code (using Hostinger):**
```php
require_once __DIR__ . '/includes/hostinger_email_service.php';
$emailService = new HostingerEmailService();
$emailService->sendOTP($email, $otp, $userName);
```

Or use the helper function:
```php
require_once __DIR__ . '/includes/hostinger_email_service.php';
sendHostingerEmail($to, $subject, $htmlBody, $textBody);
```

## Usage Examples

### Send OTP Email
```php
require_once __DIR__ . '/includes/hostinger_email_service.php';

$emailService = new HostingerEmailService();
$emailService->sendOTP('customer@example.com', '123456', 'John Doe');
```

### Send Order Confirmation
```php
$orderData = [
    'order_id' => '12345',
    'customer_name' => 'John Doe',
    'total' => '250.00',
    'items' => [
        ['name' => 'Gym T-Shirt', 'quantity' => 2, 'price' => '50.00'],
        ['name' => 'Water Bottle', 'quantity' => 3, 'price' => '50.00']
    ]
];

$emailService->sendOrderConfirmation('customer@example.com', $orderData);
```

### Send Contact Form to Admin
```php
$formData = [
    'name' => 'John Doe',
    'email' => 'john@example.com',
    'phone' => '+974 1234 5678',
    'message' => 'I have a question about your membership...'
];

$emailService->sendContactForm($formData);
```

### Send Welcome Email
```php
$emailService->sendWelcomeEmail('newuser@example.com', 'John Doe');
```

## Files to Update

Update the following files to use Hostinger email service:

1. **Password Reset** - `auth/forgot_password.php`
2. **Order Confirmation** - `order_success.php` or payment callback
3. **Contact Form** - `contact.php`
4. **User Registration** - `auth/register.php`

### Example: Update Password Reset

**File:** `auth/forgot_password.php`

```php
// OLD
require_once __DIR__ . '/../includes/email_service.php';
$emailService = new EmailService();

// NEW
require_once __DIR__ . '/../includes/hostinger_email_service.php';
$emailService = new HostingerEmailService();
```

## Testing

### Test Email Sending

Create a test file `test_hostinger_email.php`:

```php
<?php
require_once __DIR__ . '/includes/env.php';
require_once __DIR__ . '/includes/hostinger_email_service.php';

$emailService = new HostingerEmailService();

// Test OTP Email
$result = $emailService->sendOTP('your-email@example.com', '123456', 'Test User');

if ($result) {
    echo "✓ Email sent successfully!";
} else {
    echo "✗ Failed to send email. Check error log.";
}
?>
```

Upload this file to your server and access it via browser. Check your inbox!

## Troubleshooting

### Emails Not Sending

1. **Check PHP mail() function is enabled:**
   ```php
   <?php
   if (function_exists('mail')) {
       echo "PHP mail() is available";
   } else {
       echo "PHP mail() is NOT available";
   }
   ?>
   ```

2. **Check email logs in cPanel:**
   - Go to hPanel → **Emails** → **Email Delivery Reports**
   - Check for bounced or failed emails

3. **Verify SPF and DKIM:**
   - Use online tools like [MXToolbox](https://mxtoolbox.com/spf.aspx)
   - Test your domain's email authentication

4. **Check spam folder:**
   - Test emails may land in spam initially
   - Mark as "Not Spam" to improve future deliverability

### Emails Going to Spam

1. **Ensure SPF and DKIM are properly configured**
2. **Use a professional "From" address** (e.g., noreply@athletesgym.qa, not gmail.com)
3. **Warm up your domain** - Start by sending fewer emails
4. **Avoid spam trigger words** in subject lines
5. **Include unsubscribe link** for marketing emails

### Email Headers Not Working

If custom headers aren't working, simplify the headers:

```php
$headers = "From: Athletes Gym <noreply@athletesgym.qa>\r\n";
$headers .= "MIME-Version: 1.0\r\n";
$headers .= "Content-Type: text/html; charset=UTF-8\r\n";
```

## Advantages of Hostinger Email

✅ **No API Keys Required** - No third-party service dependencies
✅ **No Monthly Costs** - Included with your hosting plan
✅ **Unlimited Sending** - No rate limits (within reason)
✅ **Easy Setup** - No complex configuration
✅ **Better Deliverability** - Emails from your domain are more trusted

## Migration from Resend to Hostinger

1. Keep the old `email_service.php` file as backup
2. Update all files using `EmailService` to use `HostingerEmailService`
3. Test all email functionality
4. Update `.env` file with Hostinger settings
5. Remove Resend API keys from `.env`

## Support

If you encounter issues:
- Check Hostinger documentation: https://support.hostinger.com/
- Contact Hostinger support for email delivery issues
- Check server error logs: `/home/username/public_html/error_log`

---

**Last Updated:** March 2026
**Compatible With:** Hostinger Shared Hosting, Cloud Hosting, VPS
