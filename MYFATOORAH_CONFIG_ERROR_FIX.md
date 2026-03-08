# MyFatoorah Configuration Error Fix

## 🔴 Error Message
```
Payment gateway error: Kindly review your MyFatoorah admin configuration due to a wrong entry.
```

## ✅ Progress Made
- **CSRF validation is now working** ✓
- **Error messages are displaying properly** ✓
- **Order is being created in database** ✓
- **Stock is being deducted** ✓
- Problem is now with **MyFatoorah payment gateway configuration**

---

## 🔍 Root Cause

The error "wrong entry in MyFatoorah admin configuration" typically means:

1. **Callback URL mismatch** - You're testing on localhost but callback URL points to production
2. **Invalid phone number format** - MyFatoorah is strict about phone format
3. **Missing required fields** - Some required field might be empty
4. **API key restrictions** - Test API key might have domain restrictions

---

## ✅ Fix Applied

### Changed: checkout_process.php (Lines 183-200)

**Before:**
```php
'CallBackUrl'  => 'https://athletesgym.qa/MyFatoora/callback.php',
'ErrorUrl'     => 'https://athletesgym.qa/MyFatoora/callback.php',
```

**After:**
```php
// Determine callback URL based on environment
$baseCallbackUrl = env('APP_ENV') === 'production'
    ? 'https://athletesgym.qa'
    : 'http://localhost/athletesGym';

$postFields = [
    // ... other fields ...
    'CallBackUrl'  => $baseCallbackUrl . '/MyFatoora/callback.php',
    'ErrorUrl'     => $baseCallbackUrl . '/MyFatoora/callback.php',
];
```

This dynamically sets the callback URL based on your environment:
- **Development**: `http://localhost/athletesGym/MyFatoora/callback.php`
- **Production**: `https://athletesgym.qa/MyFatoora/callback.php`

---

## 🔧 Additional Fixes Needed

### 1. Phone Number Format

MyFatoorah expects phone in specific format: `974XXXXXXXX` (country code + number)

**Check in checkout_process.php around line 173:**

Current code:
```php
'CustomerMobile' => $phone,
```

Should be:
```php
// Format phone for MyFatoorah (must include country code)
$formattedPhone = $phone;
if (!str_starts_with($phone, '974')) {
    $formattedPhone = '974' . preg_replace('/[^0-9]/', '', $phone);
}

$postFields = [
    // ...
    'CustomerMobile' => $formattedPhone,
    // ...
];
```

### 2. Verify Required Fields

All these fields are required by MyFatoorah:
- ✓ InvoiceValue (must be > 0)
- ✓ CustomerName (must not be empty)
- ✓ CustomerMobile (must be valid format with country code)
- ✓ CustomerEmail (must be valid email)
- ✓ CallBackUrl (must be accessible URL)
- ✓ ErrorUrl (must be accessible URL)

### 3. Check Invoice Items Format

Each invoice item must have:
```php
[
    'ItemName'   => 'Product name',  // Required, not empty
    'Quantity'   => 1,                // Required, > 0
    'UnitPrice'  => 100.00           // Required, > 0
]
```

---

## 🧪 Debugging Steps

### Step 1: Check What's Being Sent to MyFatoorah

Add debug logging in checkout_process.php before line 206:

```php
// Debug: Log what we're sending to MyFatoorah
error_log("MyFatoorah Payment Request:");
error_log("Order ID: " . $orderId);
error_log("Total: " . $total);
error_log("Phone: " . $phone);
error_log("Invoice Items: " . json_encode($invoiceItems));
error_log("POST Fields: " . json_encode($postFields));

try {
    $mfPayment = new PaymentMyfatoorahApiV2($apiKey, $countryCode, $isTestMode);
    $data      = $mfPayment->sendPayment($postFields, $paymentMethodId);
```

### Step 2: Check Error Logs

**Location:** `C:\xampp\apache\logs\error.log`

Look for:
- The debug output from above
- Any specific error from MyFatoorah API
- Field validation errors

### Step 3: Test with Minimal Data

Try with the absolute minimum required fields first:

```php
$postFields = [
    'InvoiceValue' => 10, // Fixed small amount for testing
    'CustomerName' => 'Test User',
    'CustomerMobile' => '97470700707', // Valid Qatar number with country code
    'CustomerEmail' => 'test@example.com',
    'CallBackUrl'  => $baseCallbackUrl . '/MyFatoora/callback.php',
    'ErrorUrl'     => $baseCallbackUrl . '/MyFatoora/callback.php',
];
```

If this works, then gradually add back other fields.

---

## 🔍 Common MyFatoorah Configuration Issues

### Issue 1: Invalid Phone Number
**Error:** "wrong entry in admin configuration"
**Cause:** Phone number not in correct format
**Fix:** Must be `974XXXXXXXX` (country code + 8 digits)

### Issue 2: Callback URL Not Accessible
**Error:** "wrong entry in admin configuration"
**Cause:** MyFatoorah can't reach callback URL
**Fix:**
- For localhost testing: MyFatoorah **cannot** reach localhost URLs
- Solution: Either use ngrok/tunneling service OR wait until deployed to live server

### Issue 3: Test Mode API Key Restrictions
**Error:** "wrong entry in admin configuration"
**Cause:** Test API key might have restrictions
**Fix:** Check MyFatoorah dashboard for API key restrictions

### Issue 4: Missing or Empty Invoice Items
**Error:** "wrong entry in admin configuration"
**Cause:** InvoiceItems array is empty or has invalid data
**Fix:** Ensure $invoiceItems is populated before sending

---

## ⚠️ IMPORTANT: Localhost Testing Limitation

**MyFatoorah CANNOT send callbacks to localhost URLs.**

This means:
- Payment will process
- MyFatoorah will try to send callback
- Callback will fail (can't reach localhost)
- Order status won't update automatically

**Solutions:**

### Option 1: Use ngrok (Recommended for testing)
```bash
# Install ngrok
# Run: ngrok http 80
# Get public URL like: https://abc123.ngrok.io
# Update .env: APP_URL=https://abc123.ngrok.io/athletesGym
```

### Option 2: Test on Live Server
Deploy to: https://athletesgym.akshayvt.com
- Callbacks will work
- Can test full payment flow

### Option 3: Skip Payment Testing (Not Recommended)
Comment out MyFatoorah code temporarily and test order creation only

---

## 🎯 Recommended Testing Flow

### For Localhost:
1. ✓ Test order creation (database, stock deduction)
2. ✓ Test error handling
3. ✓ Test cart clearing
4. ✗ **Can't test payment callback** (MyFatoorah can't reach localhost)
5. Deploy to live server for payment testing

### For Live Server (athletesgym.akshatvt.com):
1. ✓ Test full payment flow
2. ✓ Test callback handling
3. ✓ Test order status updates
4. ✓ Test cart clearing after payment

---

## 📋 Quick Fix Checklist

- [x] Update callback URLs to use environment-based URLs
- [ ] Format phone number with country code (974XXXXXXXX)
- [ ] Add debug logging to see what's sent to MyFatoorah
- [ ] Check error logs for specific MyFatoorah errors
- [ ] Verify invoice items are not empty
- [ ] Test with minimal fields first
- [ ] Consider testing on live server instead of localhost

---

## 🚀 Next Steps

1. **Apply phone number formatting fix** (see section 1 above)
2. **Add debug logging** (see Step 1 in Debugging)
3. **Try checkout again** and check error logs
4. **If still failing**: Deploy to live server for full testing

OR

**Skip payment testing on localhost** and deploy directly to live server:
- https://athletesgym.akshatvt.com
- Update .env to production values
- Test full payment flow there

---

## 📝 Files Modified

1. ✅ `checkout_process.php` - Dynamic callback URLs based on environment

## 📝 Files to Modify (Optional Fixes)

1. `checkout_process.php` - Add phone formatting (lines 173-175)
2. `checkout_process.php` - Add debug logging (before line 206)

---

**Recommendation:** The easiest path is to deploy to your live server (athletesgym.akshatvt.com) and test there, since MyFatoorah callbacks won't work on localhost anyway.
