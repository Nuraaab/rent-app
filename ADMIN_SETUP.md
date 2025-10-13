# Admin Dashboard - Quick Setup Guide

## ⚡ Quick Start (3 Steps)

### **Step 1: Run Migration**
```bash
cd house_and_job
php artisan migrate
```

### **Step 2: Create Your First Admin**
```bash
php artisan tinker
```

Then in Tinker:
```php
// Option A: Make existing user an admin
$user = User::where('email', 'ahmednuru215@gmail.com')->first();
$user->is_admin = true;
$user->save();

// Option B: Create new admin user
User::create([
    'first_name' => 'Admin',
    'last_name' => 'User',
    'email' => 'admin@spacegig.com',
    'password' => Hash::make('admin123'), // Change this password!
    'is_admin' => true,
]);

exit
```

### **Step 3: Access Dashboard**
```
URL: http://localhost:8000/admin/login
     OR
     https://rentapp.thomasasfaw.com/admin/login

Email: Your admin email
Password: Your password
```

---

## 🎯 What You Can Do

### **Dashboard Home (`/admin`)**
- View total counts (users, properties, jobs, applications)
- See user growth chart
- See property types distribution
- View recent users and properties

### **Users Management (`/admin/users`)**
- Search users by name/email/phone
- Filter by type (Admin/Regular)
- View user details and activities
- Edit user information
- Make users admin or remove admin
- Delete users

### **Properties Management (`/admin/properties`)**
- Search properties
- Filter by category, type, status, price
- View property details and gallery
- Change property status (active/pending/inactive)
- Edit or delete properties

### **Job Openings (`/admin/jobs`)**
- Search jobs
- Filter by job type, employment type, modality
- View job details
- Edit or delete jobs

### **Applications (`/admin/applications`)**
- View all applications
- Filter by type (property/job) and status
- Update application status
- Delete applications

### **Favorites (`/admin/favorites`)**
- See what users are favoriting
- Remove favorites
- Track trends

---

## 🎨 Features Highlights

✅ **Beautiful Dashboard** - Professional Bootstrap 5 design
✅ **Real-time Stats** - Live user count, property count, etc.
✅ **Interactive Charts** - Chart.js visualizations
✅ **Search Everything** - Fast search on all pages
✅ **Advanced Filters** - Multiple filter options
✅ **Secure** - Admin-only access
✅ **Responsive** - Works on desktop, tablet, mobile
✅ **Easy to Use** - Intuitive interface

---

## 🔐 Important Security Notes

### **Change Default Password:**
If you created a test admin with `admin123`, change it immediately!

### **Protect Admin Routes:**
Make sure your `.env` has:
```
APP_ENV=production
APP_DEBUG=false
```

### **Use Strong Passwords:**
Admin accounts should have strong, unique passwords.

---

## 📝 Admin User Properties

| Field | Description |
|-------|-------------|
| `is_admin` | Boolean (true for admin access) |
| `last_login_at` | Timestamp of last login |
| All other user fields | First name, last name, email, etc. |

---

## 🚨 Troubleshooting

### **Can't Login:**
- Make sure you ran the migration
- Make sure user has `is_admin = 1`
- Check email and password are correct

### **Routes Not Working:**
- Check `routes/web.php` includes admin routes
- Run `php artisan route:list` to see all routes
- Clear route cache: `php artisan route:clear`

### **Middleware Error:**
- Make sure `AdminMiddleware` is in `app/Http/Kernel.php`
- Check middleware alias is registered

### **Views Not Found:**
- Make sure all files are in `resources/views/admin/`
- Clear view cache: `php artisan view:clear`

---

## 🎉 You're All Set!

After these 3 steps, you'll have a fully functional admin dashboard to manage your entire SpaceGig platform!

**Login URL:** `/admin/login`

**Documentation:** See `ADMIN_DASHBOARD_COMPLETE.md` for full details

---

## Date: 2025-10-13

