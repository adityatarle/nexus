# GST Number Made Optional - API Changes & Flutter Guide

**Date:** November 25, 2025  
**Change:** GST Number field is now **OPTIONAL** in dealer registration

---

## 📋 Summary of Changes

The `gst_number` field in dealer registration has been changed from **required** to **optional**. Dealers can now register without providing a GST number.

---

## 🔄 API Changes

### Endpoint: `POST /api/v1/dealer/register`

#### Before (Old Validation):
```json
{
  "gst_number": "required|string|unique:dealer_registrations,gst_number"
}
```

#### After (New Validation):
```json
{
  "gst_number": "nullable|string|max:255|unique:dealer_registrations,gst_number"
}
```

**Key Changes:**
- ✅ `gst_number` is now **nullable** (optional)
- ✅ Still validates format if provided
- ✅ Still enforces uniqueness if provided
- ✅ Can be omitted from request entirely

---

## 📱 Flutter Implementation Guide

### 1. Updated Request Model

#### Dart Model Example:
```dart
class DealerRegistrationRequest {
  final String name;
  final String email;
  final String businessName;
  final String? gstNumber;  // ✅ Now nullable
  final String businessAddress;
  final String phone;
  final String? companyWebsite;
  final String businessDescription;
  final bool termsAccepted;
  
  // Optional fields
  final String? panNumber;
  final String? businessCity;
  final String? businessState;
  final String? businessPincode;
  final String? businessCountry;
  final String? alternatePhone;
  final String? businessType;
  final int? yearsInBusiness;
  final String? annualTurnover;
  
  DealerRegistrationRequest({
    required this.name,
    required this.email,
    required this.businessName,
    this.gstNumber,  // ✅ Optional
    required this.businessAddress,
    required this.phone,
    this.companyWebsite,
    required this.businessDescription,
    required this.termsAccepted,
    // ... other optional fields
  });
  
  Map<String, dynamic> toJson() {
    final Map<String, dynamic> data = {
      'name': name,
      'email': email,
      'business_name': businessName,
      'business_address': businessAddress,
      'phone': phone,
      'business_description': businessDescription,
      'terms_accepted': termsAccepted.toString(),
    };
    
    // ✅ Only include gst_number if provided
    if (gstNumber != null && gstNumber!.isNotEmpty) {
      data['gst_number'] = gstNumber;
    }
    
    // Add other optional fields if provided
    if (companyWebsite != null && companyWebsite!.isNotEmpty) {
      data['company_website'] = companyWebsite;
    }
    // ... add other optional fields similarly
    
    return data;
  }
}
```

---

### 2. Updated Form UI

#### Flutter Form Example:
```dart
class DealerRegistrationForm extends StatefulWidget {
  @override
  _DealerRegistrationFormState createState() => _DealerRegistrationFormState();
}

class _DealerRegistrationFormState extends State<DealerRegistrationForm> {
  final _formKey = GlobalKey<FormState>();
  final _gstNumberController = TextEditingController();
  
  @override
  Widget build(BuildContext context) {
    return Form(
      key: _formKey,
      child: Column(
        children: [
          // ... other required fields
          
          // ✅ GST Number - Now Optional
          TextFormField(
            controller: _gstNumberController,
            decoration: InputDecoration(
              labelText: 'GST Number',
              hintText: '22ABCDE1234F1Z5 (Optional)',
              // ✅ No asterisk (*) needed
            ),
            // ✅ No validator required - field is optional
            // Optional: Add format validation if user enters value
            validator: (value) {
              if (value != null && value.isNotEmpty) {
                // Validate GST format if provided
                final gstRegex = RegExp(r'^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[0-9A-Z]{1}$');
                if (!gstRegex.hasMatch(value)) {
                  return 'Invalid GST number format';
                }
              }
              return null; // ✅ Allow empty
            },
          ),
          
          // ... other fields
        ],
      ),
    );
  }
}
```

---

### 3. Updated API Call (Multipart Form Data)

#### Flutter HTTP Request Example:
```dart
Future<Map<String, dynamic>> registerDealer(
  DealerRegistrationRequest request,
) async {
  final uri = Uri.parse('$baseUrl/api/v1/dealer/register');
  final multipartRequest = http.MultipartRequest('POST', uri);
  
  // Add authorization header
  multipartRequest.headers['Authorization'] = 'Bearer $token';
  
  // Required fields
  multipartRequest.fields['name'] = request.name;
  multipartRequest.fields['email'] = request.email;
  multipartRequest.fields['business_name'] = request.businessName;
  multipartRequest.fields['business_address'] = request.businessAddress;
  multipartRequest.fields['phone'] = request.phone;
  multipartRequest.fields['business_description'] = request.businessDescription;
  multipartRequest.fields['terms_accepted'] = request.termsAccepted.toString();
  
  // ✅ GST Number - Only add if provided
  if (request.gstNumber != null && request.gstNumber!.isNotEmpty) {
    multipartRequest.fields['gst_number'] = request.gstNumber!;
  }
  
  // Optional fields - only add if provided
  if (request.companyWebsite != null && request.companyWebsite!.isNotEmpty) {
    multipartRequest.fields['company_website'] = request.companyWebsite!;
  }
  
  // ... add other optional fields
  
  // File uploads (if any)
  // if (gstCertificateFile != null) {
  //   multipartRequest.files.add(
  //     await http.MultipartFile.fromPath('gst_certificate', gstCertificateFile.path),
  //   );
  // }
  
  final response = await multipartRequest.send();
  final responseBody = await response.stream.bytesToString();
  
  if (response.statusCode == 201) {
    return json.decode(responseBody);
  } else {
    throw Exception('Registration failed: ${response.statusCode}');
  }
}
```

---

### 4. Updated JSON Request Example

#### Without GST Number (Now Allowed):
```json
{
  "name": "John Doe",
  "email": "john@example.com",
  "business_name": "ABC Trading Company",
  "business_address": "123 Business Street, Mumbai",
  "phone": "+919876543210",
  "company_website": "https://www.abctrading.com",
  "business_description": "We supply agricultural equipment",
  "terms_accepted": "true"
}
```

#### With GST Number (Still Supported):
```json
{
  "name": "John Doe",
  "email": "john@example.com",
  "business_name": "ABC Trading Company",
  "gst_number": "27ABCDE1234F1Z5",
  "business_address": "123 Business Street, Mumbai",
  "phone": "+919876543210",
  "company_website": "https://www.abctrading.com",
  "business_description": "We supply agricultural equipment",
  "terms_accepted": "true"
}
```

---

## 📊 Response Format

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
      "gst_number": null,  // ✅ Can be null now
      "business_address": "123 Business Street, Mumbai",
      "phone": "+919876543210",
      "company_website": "https://www.abctrading.com",
      "business_description": "We supply agricultural equipment",
      "status": "pending",
      "created_at": "2025-11-25T10:00:00.000000Z"
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
    "email": ["The email must be a valid email address."]
    // ✅ No gst_number error if omitted
  }
}
```

---

## ✅ Validation Rules

| Field | Type | Required | Validation |
|-------|------|----------|------------|
| name | string | ✅ Yes | Max 255 characters |
| email | string | ✅ Yes | Valid email format, max 255 |
| business_name | string | ✅ Yes | Max 255 characters |
| **gst_number** | **string** | **❌ No** | **Max 255, unique if provided** |
| business_address | string | ✅ Yes | No max length restriction |
| phone | string | ✅ Yes | Max 20 characters |
| company_website | string | ❌ No | Valid URL format, max 255 |
| business_description | string | ✅ Yes | No max length restriction |
| terms_accepted | boolean/string | ✅ Yes | Must be true/accepted |

---

## 🔍 Testing Checklist

### Test Cases for Flutter App:

1. ✅ **Registration without GST number**
   - Submit form without `gst_number`
   - Should succeed with status 201
   - Response should have `gst_number: null`

2. ✅ **Registration with GST number**
   - Submit form with valid `gst_number`
   - Should succeed with status 201
   - Response should have the provided GST number

3. ✅ **Registration with invalid GST format**
   - Submit form with invalid GST format
   - Should fail with validation error
   - Error should specify format issue

4. ✅ **Registration with duplicate GST number**
   - Submit form with GST number already in use
   - Should fail with uniqueness error

5. ✅ **Registration with empty GST string**
   - Submit form with `gst_number: ""`
   - Should succeed (treated as null)

---

## 🚀 Migration Steps

### Backend Migration:
The backend migration has been created. Run:
```bash
php artisan migrate
```

### Flutter App Updates:
1. Update your `DealerRegistrationRequest` model to make `gstNumber` nullable
2. Remove `required` validator from GST number field in form
3. Update UI to remove asterisk (*) from GST number label
4. Update API call to conditionally include `gst_number` in request
5. Test both scenarios (with and without GST number)

---

## 📝 Important Notes

1. **Backward Compatibility**: Existing registrations with GST numbers will continue to work
2. **Uniqueness**: If GST number is provided, it must still be unique
3. **Format Validation**: If GST number is provided, it must match the format: `22ABCDE1234F1Z5`
4. **Null Handling**: The API accepts `null`, empty string `""`, or omitting the field entirely
5. **Database**: The database column is now nullable, allowing multiple dealers without GST numbers

---

## 🆘 Troubleshooting

### Issue: "GST number is required" error
**Solution:** Make sure you're not sending `gst_number` as an empty string in a required field. Either omit it or send `null`.

### Issue: "GST number format invalid"
**Solution:** If providing GST number, ensure it matches the format: `22ABCDE1234F1Z5`

### Issue: "GST number already taken"
**Solution:** The GST number you're trying to use is already registered. Use a different one or omit it.

---

## 📞 Support

If you encounter any issues with the API changes, please contact the backend team or refer to the API documentation.

---

**Last Updated:** November 25, 2025

