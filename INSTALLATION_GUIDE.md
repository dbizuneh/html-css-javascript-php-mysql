# Medical Records System - Installation Guide

## 🚀 Quick Setup for XAMPP Users

### Step 1: Download and Extract
1. Download the project files
2. Extract to your XAMPP `htdocs` folder:
   ```
   C:\xampp\htdocs\medical-records\
   ```

### Step 2: Test File Structure
1. Open your browser and go to:
   ```
   http://localhost/medical-records/test_paths.php
   ```
2. This will show you if all files are in the right place
3. If you see any ❌ red X marks, check that all folders exist

### Step 3: Setup Database (Option A - Automatic)
1. **Edit database credentials** in `setup_database.php` (lines 7-8):
   ```php
   $username = 'root';        // Your MySQL username (usually 'root')
   $password = '';            // Your MySQL password (usually empty for XAMPP)
   ```

2. **Run the setup script** in your browser:
   ```
   http://localhost/medical-records/setup_database.php
   ```

3. **Follow the on-screen instructions** - it will:
   - Create the database
   - Set up all tables
   - Insert sample data
   - Show you login credentials

### Step 3: Setup Database (Option B - Manual)
If the automatic setup doesn't work:

1. **Open phpMyAdmin**: `http://localhost/phpmyadmin`

2. **Create database**:
   - Click "New" on the left sidebar
   - Database name: `medical_records_db`
   - Click "Create"

3. **Import schema**:
   - Select `medical_records_db` database
   - Click "Import" tab
   - Choose file: `database/schema.sql`
   - Click "Go"

4. **Import sample data**:
   - Still in the Import tab
   - Choose file: `database/sample_data.sql`
   - Click "Go"

5. **Update database config**:
   - Edit `config/database.php`
   - Make sure the credentials match your setup:
   ```php
   private $host = 'localhost';
   private $db_name = 'medical_records_db';
   private $username = 'root';
   private $password = '';  // Usually empty for XAMPP
   ```

### Step 4: Test the System
1. **Visit the homepage**:
   ```
   http://localhost/medical-records/
   ```

2. **Login with sample accounts** (password: `password123`):
   
   **Patients:**
   - `john.doe` (John Doe)
   - `jane.smith` (Jane Smith)
   - `mike.johnson` (Mike Johnson)
   
   **Doctors:**
   - `dr.wilson` (Dr. Sarah Wilson)
   - `dr.brown` (Dr. David Brown)
   
   **Surgeons:**
   - `dr.garcia` (Dr. Maria Garcia)

## 🔧 Troubleshooting

### Error: "Failed to open stream: No such file or directory"
**Solution:**
1. Make sure you extracted ALL files to the correct folder
2. Check that these folders exist:
   - `config/`
   - `includes/`
   - `classes/`
   - `database/`
3. Run `test_paths.php` to verify file structure

### Error: "Connection error" or database issues
**Solution:**
1. Make sure XAMPP MySQL is running (start in XAMPP Control Panel)
2. Check database credentials in `config/database.php`
3. Try the manual database setup steps above

### Error: "Class 'Database' not found"
**Solution:**
1. Make sure `config/database.php` exists and has the correct content
2. Check file permissions - make sure PHP can read the files

### Pages show blank or PHP errors
**Solution:**
1. Make sure Apache and MySQL are running in XAMPP
2. Check XAMPP error logs for detailed error messages
3. Ensure PHP version is 7.4 or higher

## 📁 Expected File Structure
```
C:\xampp\htdocs\medical-records\
│
├── index.php
├── login.php
├── register.php
├── dashboard.php
├── patient-search.php
├── logout.php
├── setup_database.php
├── test_paths.php
├── README.md
├── INSTALLATION_GUIDE.md
│
├── config/
│   └── database.php
│
├── includes/
│   └── auth.php
│
├── classes/
│   └── MedicalRecord.php
│
└── database/
    ├── schema.sql
    └── sample_data.sql
```

## 🎯 Quick Test Checklist
- [ ] XAMPP Apache is running
- [ ] XAMPP MySQL is running
- [ ] All files extracted to `htdocs/medical-records/`
- [ ] `test_paths.php` shows all files exist
- [ ] Database setup completed (automatic or manual)
- [ ] Can access login page: `http://localhost/medical-records/`
- [ ] Can login with sample credentials

## 🔑 Sample Login Credentials
**Password for ALL accounts:** `password123`

| Type | Username | Full Name |
|------|----------|-----------|
| Patient | john.doe | John Doe |
| Patient | jane.smith | Jane Smith |
| Patient | mike.johnson | Mike Johnson |
| Doctor | dr.wilson | Dr. Sarah Wilson |
| Doctor | dr.brown | Dr. David Brown |
| Surgeon | dr.garcia | Dr. Maria Garcia |

## 🆘 Still Having Issues?
1. **Check XAMPP logs**: Look in `C:\xampp\apache\logs\error.log`
2. **Verify PHP version**: Go to `http://localhost/dashboard.php` (should be 7.4+)
3. **Test basic PHP**: Create a simple `<?php phpinfo(); ?>` file
4. **Check permissions**: Make sure XAMPP can read/write to the project folder

---

**Note:** This guide assumes you're using XAMPP on Windows. For other setups, adjust the paths accordingly:
- **MAMP (Mac):** `/Applications/MAMP/htdocs/medical-records/`
- **LAMP (Linux):** `/var/www/html/medical-records/`