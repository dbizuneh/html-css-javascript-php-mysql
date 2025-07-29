# Medical Records Database System

A comprehensive, secure healthcare management system that replaces traditional paper-based medical records. This web-based application allows patients, doctors, and surgeons to manage and access medical information efficiently while maintaining strict security and privacy standards.

## 🏥 Features

### Patient Features
- **Personal Dashboard**: View complete medical history at a glance
- **Medical Records**: Access all past consultations, diagnoses, and treatments
- **Allergy Management**: Track known allergies and reactions
- **Surgery History**: View surgical procedures and complications
- **Prescription History**: Track all prescribed medications
- **Secure Access**: Patient-only access to personal medical data

### Doctor/Surgeon Features
- **Patient Search**: Find patients by name, ID, phone, or blood type
- **Medical Record Management**: Create and update patient records
- **Prescription Management**: Prescribe medications with dosage instructions
- **Allergy Recording**: Document patient allergies and reactions
- **Surgery Documentation**: Record surgical procedures and outcomes (Surgeons)
- **Patient History Access**: View complete medical history for informed decisions

### System Features
- **Multi-User System**: Separate interfaces for patients, doctors, and surgeons
- **Secure Authentication**: Role-based access control
- **Audit Logging**: Track all access to patient records
- **Responsive Design**: Works on desktop, tablet, and mobile devices
- **Data Security**: Encrypted passwords and secure session management

## 🛠️ Technology Stack

- **Frontend**: HTML5, CSS3, JavaScript, Bootstrap 5
- **Backend**: PHP 7.4+
- **Database**: MySQL 5.7+
- **Icons**: Font Awesome 6
- **Framework**: Custom MVC-style PHP architecture

## 📋 Requirements

- **Web Server**: Apache or Nginx
- **PHP**: Version 7.4 or higher
- **MySQL**: Version 5.7 or higher
- **PHP Extensions**: PDO, PDO_MySQL

## 🚀 Installation

### 1. Download and Setup

```bash
# Clone or download the project
git clone <repository-url>
cd medical-records-system

# Set appropriate permissions
chmod -R 755 .
chmod -R 777 uploads/ (if file uploads are needed)
```

### 2. Database Setup

1. Create a new MySQL database:
```sql
CREATE DATABASE medical_records_db;
```

2. Import the database schema:
```bash
mysql -u your_username -p medical_records_db < database/schema.sql
```

3. Update database configuration in `config/database.php`:
```php
private $host = 'localhost';
private $db_name = 'medical_records_db';
private $username = 'your_mysql_username';
private $password = 'your_mysql_password';
```

### 3. Web Server Configuration

#### Apache
Create a virtual host or place files in your web directory:
```apache
<VirtualHost *:80>
    DocumentRoot /path/to/medical-records-system
    ServerName medical-records.local
    <Directory /path/to/medical-records-system>
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

#### Nginx
```nginx
server {
    listen 80;
    server_name medical-records.local;
    root /path/to/medical-records-system;
    index index.php index.html;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass 127.0.0.1:9000;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

### 4. Initial Setup

1. Access the application in your web browser
2. Register test accounts:
   - Create a patient account
   - Create a doctor account
   - Create a surgeon account

## 👥 User Accounts

### Default User Types

1. **Patient** (user_type_id: 1)
   - Can view own medical records
   - Cannot modify medical data
   - Can see allergies, conditions, and surgery history

2. **Doctor** (user_type_id: 2)
   - Can search and view all patients
   - Can create medical records
   - Can prescribe medications
   - Can record allergies and conditions

3. **Surgeon** (user_type_id: 3)
   - All doctor permissions
   - Can record surgical procedures
   - Can document surgical complications

4. **Admin** (user_type_id: 4)
   - Full system access (for future implementation)

### Sample User Creation

Register users through the web interface or manually insert into database:

```sql
-- Sample Doctor
INSERT INTO users (username, email, password_hash, first_name, last_name, user_type_id) 
VALUES ('dr.smith', 'dr.smith@hospital.com', '$2y$10$...', 'John', 'Smith', 2);

INSERT INTO medical_staff (user_id, license_number, specialization, hospital_name) 
VALUES (LAST_INSERT_ID(), 'MD12345', 'Internal Medicine', 'City General Hospital');
```

## 📊 Database Schema

### Core Tables

- **users**: Store all user accounts (patients and medical staff)
- **patients**: Patient-specific information (blood type, emergency contacts)
- **medical_staff**: Doctor/surgeon information (license, specialization)
- **medical_records**: Patient consultations and diagnoses
- **prescriptions**: Prescribed medications linked to medical records
- **allergies**: Patient allergies and reactions
- **surgeries**: Surgical procedures and outcomes
- **medical_conditions**: Reference table for diseases/conditions
- **medications**: Reference table for available medications
- **access_logs**: Audit trail for security and compliance

### Key Relationships

- Users can be patients OR medical staff (not both)
- Medical records link patients to doctors
- Prescriptions are tied to specific medical records
- Surgeries are performed by surgeons on patients
- Allergies are recorded by medical staff for patients

## 🔐 Security Features

### Authentication
- Secure password hashing using PHP's `password_hash()`
- Session-based authentication
- Role-based access control
- Automatic session timeout

### Access Control
- Patients can only access their own records
- Medical staff must be verified before accessing patient data
- All patient access is logged with timestamps and IP addresses
- Proper input validation and sanitization

### Privacy Protection
- No patient data exposed in URLs
- Secure database queries using prepared statements
- XSS protection through proper output encoding
- CSRF protection recommended for production

## 📱 User Interface

### Patient Dashboard
- Medical history timeline
- Quick stats (total records, allergies, conditions)
- Emergency information display
- Allergy and condition badges

### Medical Staff Dashboard
- Patient search functionality
- Quick access to add new records
- Recent activity overview
- Streamlined navigation

### Responsive Design
- Mobile-friendly interface
- Bootstrap 5 components
- Modern gradient designs
- Intuitive navigation

## 🏃‍♂️ Usage Examples

### For Patients
1. Login with patient credentials
2. View dashboard with medical summary
3. Navigate to specific sections (records, allergies, surgeries)
4. Review prescription history and follow-up dates

### For Doctors
1. Login with doctor credentials
2. Search for patient by name or ID
3. View patient's complete medical history
4. Add new medical records with diagnoses
5. Prescribe medications with dosage instructions
6. Record new allergies or medical conditions

### For Surgeons
1. All doctor capabilities plus:
2. Access to surgical history
3. Ability to record new surgeries
4. Document surgical complications
5. Record pre/post-operative notes

## 🔧 Customization

### Adding New User Types
1. Add entry to `user_types` table
2. Update authentication class
3. Modify UI navigation based on user type

### Adding New Medical Fields
1. Update database schema
2. Modify MedicalRecord class methods
3. Update forms and display templates

### Styling Customization
- Modify CSS custom properties in style sections
- Update Bootstrap theme variables
- Add custom CSS files for organization-specific branding

## 🚨 Important Security Notes

### For Production Deployment

1. **Database Security**:
   - Use strong database passwords
   - Limit database user permissions
   - Enable SSL connections
   - Regular database backups

2. **Web Server Security**:
   - Enable HTTPS/SSL
   - Configure proper file permissions
   - Disable directory browsing
   - Set up web application firewall

3. **Application Security**:
   - Change default database credentials
   - Implement CSRF tokens
   - Add rate limiting for login attempts
   - Regular security updates

4. **Compliance**:
   - Ensure HIPAA compliance if applicable
   - Implement data retention policies
   - Regular security audits
   - Staff training on data privacy

## 📋 Future Enhancements

- **File Upload**: Support for medical documents and images
- **Appointment Scheduling**: Integration with calendar systems
- **Messaging System**: Secure communication between patients and doctors
- **Reporting**: Generate medical reports and analytics
- **API Development**: RESTful API for mobile app integration
- **Lab Results Integration**: Connect with laboratory systems
- **Pharmacy Integration**: Direct prescription sending to pharmacies

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Test thoroughly
5. Submit a pull request

## 📄 License

This project is developed for educational and healthcare purposes. Please ensure compliance with local healthcare regulations and privacy laws before deployment.

## 📞 Support

For issues, questions, or contributions:
- Review the documentation
- Check existing issues
- Create detailed bug reports
- Follow security reporting procedures for vulnerabilities

---

**⚠️ Disclaimer**: This system is designed for educational purposes. For production use in healthcare environments, ensure compliance with relevant regulations (HIPAA, GDPR, etc.) and conduct proper security assessments.