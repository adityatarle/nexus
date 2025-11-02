# Dealer Registration API - Quick Reference Card

## Base URL
```
https://nexus.heuristictechpark.com/api/v1
```

---

## Quick Test Flow (Copy & Paste)

### 1. Register as Dealer
```bash
curl -X POST "https://nexus.heuristictechpark.com/api/v1/register" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Test Dealer",
    "email": "testdealer@example.com",
    "password": "password123",
    "password_confirmation": "password123",
    "role": "dealer",
    "phone": "+919876543210"
  }'
```

### 2. Login (Save Token)
```bash
curl -X POST "https://nexus.heuristictechpark.com/api/v1/login" \
  -H "Content-Type: application/json" \
  -d '{
    "email": "testdealer@example.com",
    "password": "password123"
  }'
```

**⚠️ Copy the `token` from response!**

### 3. Submit Dealer Registration
```bash
curl -X POST "https://nexus.heuristictechpark.com/api/v1/dealer/register" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Test Dealer",
    "email": "contact@testdealer.com",
    "business_name": "Test Agricultural Supplies",
    "gst_number": "27TEST1234F1Z5",
    "business_address": "123 Test Street, Mumbai",
    "phone": "+919876543210",
    "company_website": "https://testdealer.com",
    "business_description": "Test business description",
    "terms_accepted": true
  }'
```

### 4. Check Status
```bash
curl -X GET "https://nexus.heuristictechpark.com/api/v1/dealer/status" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

---

## Required Fields Checklist

### User Registration:
- [ ] name
- [ ] email (unique)
- [ ] password
- [ ] password_confirmation
- [ ] role: "dealer"
- [ ] phone

### Dealer Registration:
- [ ] name (contact person)
- [ ] email (contact email)
- [ ] business_name
- [ ] gst_number (unique)
- [ ] business_address
- [ ] phone
- [ ] company_website (optional)
- [ ] business_description
- [ ] terms_accepted: true

---

## Common Errors

| Error | Solution |
|-------|----------|
| "User account is not registered as dealer" | Register with `"role": "dealer"` first |
| "Dealer registration already submitted" | Check status - already submitted |
| "The gst number has already been taken" | Use different GST number |
| 401 Unauthorized | Include `Authorization: Bearer {token}` header |
| 422 Validation Error | Check all required fields are present |

---

## Endpoints Summary

```
POST   /api/v1/register           → Register user (role: dealer)
POST   /api/v1/login              → Login & get token
GET    /api/v1/user               → Get user info
POST   /api/v1/dealer/register    → Submit dealer form (Auth required)
GET    /api/v1/dealer/status      → Check status (Auth required)
```

---

## Postman Quick Setup

1. **Authorization Tab:** Select "Bearer Token"
2. **Token:** Paste token from login response
3. **Headers:** Don't add Authorization header manually (Postman handles it)

---

**Full Guide:** See `DEALER_REGISTRATION_API_TESTING_GUIDE.md` for complete details.

