# 🚀 Hosting Deployment - Image Fix Commands

**Run these commands on your hosting server** (via SSH/Terminal)

## Step 1: Create Storage Link (MOST IMPORTANT)
```bash
php artisan storage:link
```
**Expected Output:** `The [public/storage] link has been connected.`

---

## Step 2: Clear All Application Caches
```bash
php artisan optimize:clear
```

This runs:
- `php artisan cache:clear` - Clear app cache
- `php artisan config:clear` - Clear config cache  
- `php artisan route:clear` - Clear route cache
- `php artisan view:clear` - Clear view cache

---

## Step 3: Set Correct File Permissions (Linux/Unix)
```bash
chmod -R 775 storage/app/public
chmod -R 755 public/storage
```

For Windows (if running on Windows hosting), use File Manager to set:
- `storage/app/public` → Full Control (or 777)
- `public/storage` → Read/Execute

---

## Step 4: Verify Storage Directory Exists
```bash
mkdir -p storage/app/public/products/primary
mkdir -p storage/app/public/products/gallery
chmod -R 775 storage/app/public/products
```

---

## Testing After Deployment

1. **Upload a test image** via admin panel
2. **Hard refresh browser:** `Ctrl+Shift+R` (Chrome/Firefox) or `Cmd+Shift+R` (Mac)
3. **Check browser DevTools:**
   - F12 → Network tab
   - Load product page
   - Look for image request - should return **200 OK**
   - Image URL should look like: `https://yourdomain.com/storage/products/primary/filename.jpg?t=1731234567`

---

## Complete Deployment Script (Copy & Paste)

```bash
#!/bin/bash
# Run all fixes at once

echo "1. Creating storage link..."
php artisan storage:link

echo "2. Clearing caches..."
php artisan optimize:clear

echo "3. Setting permissions..."
chmod -R 775 storage/app/public
chmod -R 755 public/storage

echo "4. Creating directories..."
mkdir -p storage/app/public/products/primary
mkdir -p storage/app/public/products/gallery
chmod -R 775 storage/app/public/products

echo "✅ All done! Images should now display correctly."
echo "🔄 Remember to hard refresh your browser (Ctrl+Shift+R)"
```

Save as `deploy-images.sh`, then run:
```bash
chmod +x deploy-images.sh
./deploy-images.sh
```

---

## If Using cPanel/Hosting Control Panel

1. Go to **File Manager**
2. Navigate to your app root
3. **Create folders** if they don't exist:
   - `storage/app/public/products/primary`
   - `storage/app/public/products/gallery`
4. Set permissions to **777** (full access)
5. Use **Terminal** or **SSH** to run the commands above

---

## Troubleshooting

| Issue | Solution |
|-------|----------|
| "storage:link" fails | Hosting may not support symlinks. Contact support or use alternative storage |
| Images still 404 | Check if `/public/storage` symlink exists; verify file permissions are 775+ |
| Old images still showing | Hard refresh: `Ctrl+Shift+R`, not just `F5` |
| Permission denied errors | Use `chmod 777` temporarily to diagnose, then set to 775 |

---

## Contact Your Hosting Support With This

If you need hosting support, provide them this template:

```
Issue: Product images don't display after updates
Framework: Laravel 11
Problem: Need to enable symbolic links for /public/storage

Requested:
1. Ensure /storage/app/public is writable (775 permissions)
2. Create symbolic link: public/storage → storage/app/public
3. Or alternative: Allow us to upload directly to public/uploads folder

These commands are required:
- php artisan storage:link
- chmod -R 775 storage/app/public
- chmod -R 755 public/storage
```

---

## ✅ After Fix - What You'll See

✅ Image URLs include timestamp: `...filename.jpg?t=1731234567`  
✅ New images appear immediately after upload  
✅ Refreshing page shows updated images  
✅ No 404 errors in browser console  
✅ Admin panel shows current image in edit form  








