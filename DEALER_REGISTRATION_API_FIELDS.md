# Dealer Registration API - Field Reference

## Required Fields

The dealer registration form requires **ONLY** the following fields:

### Endpoint
```
POST /api/v1/dealer/register
Headers: Authorization: Bearer {token}
Content-Type: multipart/form-data
```

### Required Fields:

1. **name** (string, max 255)
   - Contact person name
   - Example: "John Doe"

2. **email** (string, email, max 255)
   - Contact email address
   - Example: "john@example.com"

3. **business_name** (string, max 255)
   - Name of the business
   - Example: "ABC Trading Company"

4. **gst_number** (string, unique)
   - GST registration number
   - Must be unique (not used by another dealer)
   - Example: "27ABCDE1234F1Z5"

5. **business_address** (string)
   - Complete business address
   - Example: "123 Business Street, Mumbai"

6. **phone** (string, max 20)
   - Contact phone number
   - Example: "+919876543210"

7. **company_website** (string, URL, optional)
   - Company website URL
   - Can be null
   - Example: "https://www.abctrading.com"

8. **business_description** (string)
   - Description of the business
   - Example: "We are a leading agricultural equipment supplier"

9. **terms_accepted** (boolean/string)
   - Must be accepted/true
   - Example: "true" or true

---

## Example Request (Flutter/Dart)

### JSON Body Example:
```json
{
  "name": "John Doe",
  "email": "john@example.com",
  "business_name": "ABC Trading Company",
  "gst_number": "27ABCDE1234F1Z5",
  "business_address": "123 Business Street, Mumbai",
  "phone": "+919876543210",
  "company_website": "https://www.abctrading.com",
  "business_description": "We supply agricultural equipment and tools"
}
```

### Multipart Form Data (with file uploads):
```dart
var request = http.MultipartRequest(
  'POST',
  Uri.parse('$baseUrl/api/v1/dealer/register'),
);

request.headers['Authorization'] = 'Bearer $token';

// Required fields
request.fields['name'] = 'John Doe';
request.fields['email'] = 'john@example.com';
request.fields['business_name'] = 'ABC Trading Company';
request.fields['gst_number'] = '27ABCDE1234F1Z5';
request.fields['business_address'] = '123 Business Street, Mumbai';
request.fields['phone'] = '+919876543210';
request.fields['company_website'] = 'https://www.abctrading.com';
request.fields['business_description'] = 'We supply agricultural equipment';
request.fields['terms_accepted'] = 'true';

// Optional file uploads (if needed)
// request.files.add(await http.MultipartFile.fromPath('gst_certificate', '/path/to/file.pdf'));
```

---

## Response Format

### Success Response (201):
```json
{
  "success": true,
  "message": "Dealer registration submitted successfully. Your application is under review.",
  "data": {
    "registration": {
      "id": 1,
      "name": "John Doe",
      "email": "john@example.com",
      "business_name": "ABC Trading Company",
      "gst_number": "27ABCDE1234F1Z5",
      "business_address": "123 Business Street, Mumbai",
      "phone": "+919876543210",
      "company_website": "https://www.abctrading.com",
      "business_description": "We supply agricultural equipment",
      "status": "pending",
      "created_at": "2025-10-30T10:00:00.000000Z"
    }
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
    "email": ["The email must be a valid email address."]
  }
}
```

---

## Field Validation Rules

| Field | Type | Required | Validation |
|-------|------|----------|------------|
| name | string | Yes | Max 255 characters |
| email | string | Yes | Valid email format, max 255 |
| business_name | string | Yes | Max 255 characters |
| gst_number | string | Yes | Unique (not duplicate), max 255 |
| business_address | string | Yes | No max length restriction |
| phone | string | Yes | Max 20 characters |
| company_website | string | No | Valid URL format, max 255 |
| business_description | string | Yes | No max length restriction |
| terms_accepted | boolean | Yes | Must be true/accepted |

---

## Optional Fields (Not Required, but Available)

These fields can be included but are not mandatory:

- `pan_number` - PAN number
- `business_city` - City
- `business_state` - State
- `business_pincode` - PIN code
- `business_country` - Country (defaults to "India")
- `alternate_phone` - Alternate phone number
- `business_type` - Type (Individual, Partnership, Private Limited, etc.)
- `years_in_business` - Number of years
- `annual_turnover` - Annual turnover
- `gst_certificate` - GST certificate file (PDF/Image, max 5MB)
- `pan_certificate` - PAN certificate file (PDF/Image, max 5MB)
- `business_license` - Business license file (PDF/Image, max 5MB)

---

## Quick Implementation Checklist

- [ ] User must be registered as dealer (`role: "dealer"` in registration)
- [ ] User must be logged in (Bearer token required)
- [ ] Include all 9 required fields
- [ ] Set `terms_accepted` to true
- [ ] Handle validation errors gracefully
- [ ] Show pending status after submission
- [ ] Check status periodically via `/api/v1/dealer/status`

---

**Last Updated:** October 30, 2025

