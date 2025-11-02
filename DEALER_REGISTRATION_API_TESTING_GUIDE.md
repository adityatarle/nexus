# Dealer Registration API - Complete Testing Guide

## Overview

This guide walks you through testing the complete dealer registration flow via API. All endpoints are available and ready to use.

---

## Base URL

```
Production: https://nexus.heuristictechpark.com
Local/Dev: http://localhost/nexus/nexus/public
```

**Note:** Make sure to include `/public` in your local URL if testing locally.

---

## Complete Dealer Registration Flow

The dealer registration process consists of **3 steps**:

### Step 1: Register User Account as Dealer
### Step 2: Login to Get Authentication Token
### Step 3: Submit Dealer Registration Form

---

## Step 1: Register User Account as Dealer

**Endpoint:** `POST /api/v1/register`

**Description:** Register a new user account with dealer role. This creates the user account first.

**Authentication:** Not required (public endpoint)

**Content-Type:** `application/json`

### Request Body:

```json
{
  "name": "John Dealer",
  "email": "john.dealer@example.com",
  "password": "password123",
  "password_confirmation": "password123",
  "role": "dealer",
  "phone": "+919876543210"
}
```

### Required Fields:
- `name` - Full name
- `email` - Valid email address (must be unique)
- `password` - Minimum 6 characters
- `password_confirmation` - Must match password
- `role` - Must be `"dealer"` (or `"customer"` for regular users)
- `phone` - Phone number

### cURL Example:

```bash
curl -X POST "https://nexus.heuristictechpark.com/api/v1/register" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "John Dealer",
    "email": "john.dealer@example.com",
    "password": "password123",
    "password_confirmation": "password123",
    "role": "dealer",
    "phone": "+919876543210"
  }'
```

### Postman Setup:

1. **Method:** POST
2. **URL:** `https://nexus.heuristictechpark.com/api/v1/register`
3. **Headers:**
   - `Content-Type: application/json`
4. **Body (raw JSON):**
   ```json
   {
     "name": "John Dealer",
     "email": "john.dealer@example.com",
     "password": "password123",
     "password_confirmation": "password123",
     "role": "dealer",
     "phone": "+919876543210"
   }
   ```

### Success Response (201):

```json
{
  "success": true,
  "message": "Registration successful",
  "data": {
    "user": {
      "id": 10,
      "name": "John Dealer",
      "email": "john.dealer@example.com",
      "role": "dealer",
      "phone": "+919876543210",
      "created_at": "2025-10-30T10:00:00.000000Z",
      "updated_at": "2025-10-30T10:00:00.000000Z"
    },
    "token": "1|abcdefghijklmnopqrstuvwxyz1234567890"
  }
}
```

**⚠️ IMPORTANT:** Save the `token` from the response! You'll need it for the next steps.

### Error Response (422 - Validation Error):

```json
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "email": ["The email has already been taken."],
    "password": ["The password confirmation does not match."]
  }
}
```

---

## Step 2: Login to Get Authentication Token

**Endpoint:** `POST /api/v1/login`

**Description:** Login with email and password to get authentication token.

**Authentication:** Not required (public endpoint)

**Content-Type:** `application/json`

### Request Body:

```json
{
  "email": "john.dealer@example.com",
  "password": "password123"
}
```

### cURL Example:

```bash
curl -X POST "https://nexus.heuristictechpark.com/api/v1/login" \
  -H "Content-Type: application/json" \
  -d '{
    "email": "john.dealer@example.com",
    "password": "password123"
  }'
```

### Postman Setup:

1. **Method:** POST
2. **URL:** `https://nexus.heuristictechpark.com/api/v1/login`
3. **Headers:**
   - `Content-Type: application/json`
4. **Body (raw JSON):**
   ```json
   {
     "email": "john.dealer@example.com",
     "password": "password123"
   }
   ```

### Success Response (200):

```json
{
  "success": true,
  "message": "Login successful",
  "data": {
    "user": {
      "id": 10,
      "name": "John Dealer",
      "email": "john.dealer@example.com",
      "role": "dealer",
      "is_dealer": true,
      "is_approved_dealer": false,
      "can_access_dealer_pricing": false
    },
    "token": "2|xyzabcdefghijklmnopqrstuvwxyz9876543210"
  }
}
```

**⚠️ IMPORTANT:** Save the `token` from the response! You'll need it for dealer registration.

### Error Response (401):

```json
{
  "success": false,
  "message": "Invalid credentials"
}
```

---

## Step 3: Submit Dealer Registration Form

**Endpoint:** `POST /api/v1/dealer/register`

**Description:** Submit dealer business registration form with business details.

**Authentication:** **REQUIRED** - Bearer token from login

**Content-Type:** `multipart/form-data` (for file uploads) or `application/json` (for text only)

### Request Body (JSON - without files):

```json
{
  "name": "John Dealer",
  "email": "contact@johndealer.com",
  "business_name": "John's Agricultural Supplies",
  "gst_number": "27ABCDE1234F1Z5",
  "business_address": "123 Business Street, Industrial Area, Mumbai",
  "phone": "+919876543210",
  "company_website": "https://www.johndealer.com",
  "business_description": "We supply high-quality agricultural equipment and tools to farmers across Maharashtra.",
  "terms_accepted": true
}
```

### Request Body (Form Data - with files):

**Fields:**
- `name`: "John Dealer" (required)
- `email`: "contact@johndealer.com" (required)
- `business_name`: "John's Agricultural Supplies" (required)
- `gst_number`: "27ABCDE1234F1Z5" (required, must be unique)
- `business_address`: "123 Business Street..." (required)
- `phone`: "+919876543210" (required)
- `company_website`: "https://www.johndealer.com" (optional)
- `business_description`: "We supply..." (required)
- `terms_accepted`: "true" (required)
- `gst_certificate`: [file] (optional, PDF/JPG/PNG, max 5MB)
- `pan_certificate`: [file] (optional, PDF/JPG/PNG, max 5MB)
- `business_license`: [file] (optional, PDF/JPG/PNG, max 5MB)

### cURL Example (JSON - No Files):

```bash
curl -X POST "https://nexus.heuristictechpark.com/api/v1/dealer/register" \
  -H "Authorization: Bearer 2|xyzabcdefghijklmnopqrstuvwxyz9876543210" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "John Dealer",
    "email": "contact@johndealer.com",
    "business_name": "John'\''s Agricultural Supplies",
    "gst_number": "27ABCDE1234F1Z5",
    "business_address": "123 Business Street, Industrial Area, Mumbai",
    "phone": "+919876543210",
    "company_website": "https://www.johndealer.com",
    "business_description": "We supply high-quality agricultural equipment and tools to farmers across Maharashtra.",
    "terms_accepted": true
  }'
```

### cURL Example (Multipart Form - With Files):

```bash
curl -X POST "https://nexus.heuristictechpark.com/api/v1/dealer/register" \
  -H "Authorization: Bearer 2|xyzabcdefghijklmnopqrstuvwxyz9876543210" \
  -F "name=John Dealer" \
  -F "email=contact@johndealer.com" \
  -F "business_name=John's Agricultural Supplies" \
  -F "gst_number=27ABCDE1234F1Z5" \
  -F "business_address=123 Business Street, Industrial Area, Mumbai" \
  -F "phone=+919876543210" \
  -F "company_website=https://www.johndealer.com" \
  -F "business_description=We supply high-quality agricultural equipment and tools to farmers across Maharashtra." \
  -F "terms_accepted=true" \
  -F "gst_certificate=@/path/to/gst_certificate.pdf" \
  -F "pan_certificate=@/path/to/pan_certificate.pdf" \
  -F "business_license=@/path/to/business_license.pdf"
```

### Postman Setup (JSON):

1. **Method:** POST
2. **URL:** `https://nexus.heuristictechpark.com/api/v1/dealer/register`
3. **Headers:**
   - `Authorization: Bearer 2|xyzabcdefghijklmnopqrstuvwxyz9876543210`
   - `Content-Type: application/json`
4. **Body (raw JSON):**
   ```json
   {
     "name": "John Dealer",
     "email": "contact@johndealer.com",
     "business_name": "John's Agricultural Supplies",
     "gst_number": "27ABCDE1234F1Z5",
     "business_address": "123 Business Street, Industrial Area, Mumbai",
     "phone": "+919876543210",
     "company_website": "https://www.johndealer.com",
     "business_description": "We supply high-quality agricultural equipment and tools to farmers across Maharashtra.",
     "terms_accepted": true
   }
   ```

### Postman Setup (Form Data with Files):

1. **Method:** POST
2. **URL:** `https://nexus.heuristictechpark.com/api/v1/dealer/register`
3. **Headers:**
   - `Authorization: Bearer 2|xyzabcdefghijklmnopqrstuvwxyz9876543210`
   - **Note:** Don't set Content-Type header when using form-data - Postman will set it automatically
4. **Body (form-data):**
   - `name`: `John Dealer`
   - `email`: `contact@johndealer.com`
   - `business_name`: `John's Agricultural Supplies`
   - `gst_number`: `27ABCDE1234F1Z5`
   - `business_address`: `123 Business Street, Industrial Area, Mumbai`
   - `phone`: `+919876543210`
   - `company_website`: `https://www.johndealer.com`
   - `business_description`: `We supply high-quality agricultural equipment and tools to farmers across Maharashtra.`
   - `terms_accepted`: `true`
   - `gst_certificate`: [Select File] (type: File)
   - `pan_certificate`: [Select File] (type: File)
   - `business_license`: [Select File] (type: File)

### Success Response (201):

```json
{
  "success": true,
  "message": "Dealer registration submitted successfully. Your application is under review.",
  "data": {
    "registration": {
      "id": 1,
      "name": "John Dealer",
      "email": "contact@johndealer.com",
      "business_name": "John's Agricultural Supplies",
      "gst_number": "27ABCDE1234F1Z5",
      "business_address": "123 Business Street, Industrial Area, Mumbai",
      "phone": "+919876543210",
      "company_website": "https://www.johndealer.com",
      "business_description": "We supply high-quality agricultural equipment and tools to farmers across Maharashtra.",
      "status": "pending",
      "created_at": "2025-10-30T10:15:00.000000Z"
    }
  }
}
```

### Error Response (400 - User Not Dealer):

```json
{
  "success": false,
  "message": "User account is not registered as dealer. Please register with role=dealer first."
}
```

### Error Response (400 - Already Submitted):

```json
{
  "success": false,
  "message": "Dealer registration already submitted",
  "data": {
    "status": "pending",
    "registration": { ... }
  }
}
```

### Error Response (422 - Validation Error):

```json
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "gst_number": ["The gst number has already been taken."],
    "email": ["The email must be a valid email address."],
    "name": ["The name field is required."]
  }
}
```

---

## Step 4: Check Dealer Registration Status

**Endpoint:** `GET /api/v1/dealer/status`

**Description:** Check the status of dealer registration (pending, approved, rejected).

**Authentication:** **REQUIRED** - Bearer token

### cURL Example:

```bash
curl -X GET "https://nexus.heuristictechpark.com/api/v1/dealer/status" \
  -H "Authorization: Bearer 2|xyzabcdefghijklmnopqrstuvwxyz9876543210"
```

### Postman Setup:

1. **Method:** GET
2. **URL:** `https://nexus.heuristictechpark.com/api/v1/dealer/status`
3. **Headers:**
   - `Authorization: Bearer 2|xyzabcdefghijklmnopqrstuvwxyz9876543210`

### Success Response (Pending):

```json
{
  "success": true,
  "data": {
    "is_registered": true,
    "is_approved": false,
    "status": "pending",
    "message": "Your dealer registration is under review. We will notify you once it is approved.",
    "registration": {
      "id": 1,
      "name": "John Dealer",
      "email": "contact@johndealer.com",
      "business_name": "John's Agricultural Supplies",
      "gst_number": "27ABCDE1234F1Z5",
      "status": "pending",
      ...
    }
  }
}
```

### Success Response (Approved):

```json
{
  "success": true,
  "data": {
    "is_registered": true,
    "is_approved": true,
    "status": "approved",
    "message": "Your dealer registration has been approved. You can now access dealer pricing.",
    "registration": { ... }
  }
}
```

### Success Response (Not Submitted):

```json
{
  "success": true,
  "data": {
    "is_registered": false,
    "is_approved": false,
    "status": "not_submitted",
    "message": "Dealer registration not submitted yet"
  }
}
```

---

## Step 5: Get User Info (Check Dealer Status)

**Endpoint:** `GET /api/v1/user`

**Description:** Get current authenticated user information including dealer status.

**Authentication:** **REQUIRED** - Bearer token

### cURL Example:

```bash
curl -X GET "https://nexus.heuristictechpark.com/api/v1/user" \
  -H "Authorization: Bearer 2|xyzabcdefghijklmnopqrstuvwxyz9876543210"
```

### Postman Setup:

1. **Method:** GET
2. **URL:** `https://nexus.heuristictechpark.com/api/v1/user`
3. **Headers:**
   - `Authorization: Bearer 2|xyzabcdefghijklmnopqrstuvwxyz9876543210`

### Success Response:

```json
{
  "success": true,
  "data": {
    "user": {
      "id": 10,
      "name": "John Dealer",
      "email": "john.dealer@example.com",
      "role": "dealer",
      "is_dealer": true,
      "is_approved_dealer": false,
      "can_access_dealer_pricing": false,
      "dealer_registration": {
        "status": "pending",
        "is_approved": false,
        "is_pending": true
      }
    }
  }
}
```

**Key Fields:**
- `is_dealer`: `true` if user registered with dealer role
- `is_approved_dealer`: `true` only after admin approves
- `can_access_dealer_pricing`: `true` only after approval (can see dealer prices)
- `dealer_registration.status`: `"not_submitted"`, `"pending"`, `"approved"`, or `"rejected"`

---

## Complete Testing Workflow

### Test Scenario: Register a New Dealer

1. **Register User as Dealer:**
   ```bash
   POST /api/v1/register
   Body: { "name": "...", "email": "...", "password": "...", "password_confirmation": "...", "role": "dealer", "phone": "..." }
   ```
   - ✅ Save the `token` from response

2. **Login (Optional - if you already have account):**
   ```bash
   POST /api/v1/login
   Body: { "email": "...", "password": "..." }
   ```
   - ✅ Save the `token` from response

3. **Check User Status:**
   ```bash
   GET /api/v1/user
   Headers: Authorization: Bearer {token}
   ```
   - ✅ Verify `is_dealer: true`
   - ✅ Verify `dealer_registration.status: "not_submitted"`

4. **Submit Dealer Registration:**
   ```bash
   POST /api/v1/dealer/register
   Headers: Authorization: Bearer {token}
   Body: { "name": "...", "email": "...", "business_name": "...", ... }
   ```
   - ✅ Should return `status: "pending"`

5. **Check Registration Status:**
   ```bash
   GET /api/v1/dealer/status
   Headers: Authorization: Bearer {token}
   ```
   - ✅ Should return `status: "pending"`

6. **After Admin Approval - Check Status Again:**
   ```bash
   GET /api/v1/user
   Headers: Authorization: Bearer {token}
   ```
   - ✅ Should return `is_approved_dealer: true`
   - ✅ Should return `can_access_dealer_pricing: true`
   - ✅ `dealer_registration.status: "approved"`

---

## Common Issues & Troubleshooting

### Issue 1: "User account is not registered as dealer"
**Solution:** Make sure you registered with `"role": "dealer"` in Step 1, not `"role": "customer"`.

### Issue 2: "Dealer registration already submitted"
**Solution:** You've already submitted the form. Check status using `/api/v1/dealer/status` endpoint.

### Issue 3: "Invalid credentials" when logging in
**Solution:** Check email and password. Make sure account exists and password is correct.

### Issue 4: "The gst number has already been taken"
**Solution:** GST number must be unique. Use a different GST number or check if you've already registered.

### Issue 5: 401 Unauthorized
**Solution:** 
- Make sure you included the `Authorization: Bearer {token}` header
- Token might be expired - login again to get a new token
- Check that token has no extra spaces

### Issue 6: 422 Validation Error
**Solution:** 
- Check all required fields are included
- Verify email format is valid
- Ensure `terms_accepted` is `true` (not string "true", but boolean or accepted)
- Check field lengths are within limits

### Issue 7: Can't see dealer prices even after approval
**Solution:**
- Verify `can_access_dealer_pricing: true` in `/api/v1/user` response
- Make sure you're sending the `Authorization` header when fetching products
- Products API automatically returns dealer prices if user is approved dealer

---

## Postman Collection Setup

### Step 1: Create Environment Variables

Create a Postman environment and add these variables:
- `base_url`: `https://nexus.heuristictechpark.com` (or your local URL)
- `token`: (will be set automatically after login)
- `user_email`: `john.dealer@example.com`
- `user_password`: `password123`

### Step 2: Create Requests

1. **Register Dealer User**
   - URL: `{{base_url}}/api/v1/register`
   - Method: POST
   - Body: JSON with dealer registration details
   - Tests: Extract token and save to environment

2. **Login**
   - URL: `{{base_url}}/api/v1/login`
   - Method: POST
   - Body: JSON with email and password
   - Tests: Extract token and save to `{{token}}`

3. **Get User Info**
   - URL: `{{base_url}}/api/v1/user`
   - Method: GET
   - Headers: `Authorization: Bearer {{token}}`

4. **Submit Dealer Registration**
   - URL: `{{base_url}}/api/v1/dealer/register`
   - Method: POST
   - Headers: `Authorization: Bearer {{token}}`
   - Body: form-data or JSON

5. **Check Dealer Status**
   - URL: `{{base_url}}/api/v1/dealer/status`
   - Method: GET
   - Headers: `Authorization: Bearer {{token}}`

### Step 3: Add Tests to Auto-Save Token

In the **Login** request, add this test script:

```javascript
if (pm.response.code === 200) {
    var jsonData = pm.response.json();
    if (jsonData.success && jsonData.data.token) {
        pm.environment.set("token", jsonData.data.token);
        console.log("Token saved:", jsonData.data.token);
    }
}
```

---

## API Summary

| Endpoint | Method | Auth | Purpose |
|----------|--------|------|---------|
| `/api/v1/register` | POST | No | Register user with dealer role |
| `/api/v1/login` | POST | No | Login and get token |
| `/api/v1/user` | GET | Yes | Get user info with dealer status |
| `/api/v1/dealer/register` | POST | Yes | Submit dealer registration form |
| `/api/v1/dealer/status` | GET | Yes | Check registration status |

---

## Required Fields Summary

### User Registration:
- `name`, `email`, `password`, `password_confirmation`, `role` (must be "dealer"), `phone`

### Dealer Registration:
- `name`, `email`, `business_name`, `gst_number`, `business_address`, `phone`, `company_website` (optional), `business_description`, `terms_accepted`

---

## Next Steps After Approval

Once dealer registration is approved:
1. User can see dealer prices in products API
2. `can_access_dealer_pricing` becomes `true`
3. Product prices automatically show dealer pricing
4. User can place orders with dealer pricing

---

**Last Updated:** October 30, 2025

**Need Help?** Check the main API documentation or contact the development team.

