-- Medical Record Database Schema
-- Database: medical_records_db

CREATE DATABASE IF NOT EXISTS medical_records_db;
USE medical_records_db;

-- User Types Table
CREATE TABLE user_types (
    type_id INT PRIMARY KEY AUTO_INCREMENT,
    type_name VARCHAR(50) NOT NULL UNIQUE,
    description TEXT
);

-- Users Table (for both patients and medical staff)
CREATE TABLE users (
    user_id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(100) NOT NULL UNIQUE,
    email VARCHAR(150) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    date_of_birth DATE,
    gender ENUM('Male', 'Female', 'Other'),
    address TEXT,
    user_type_id INT NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_type_id) REFERENCES user_types(type_id)
);

-- Medical Staff Table (doctors, surgeons, nurses)
CREATE TABLE medical_staff (
    staff_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    license_number VARCHAR(100) NOT NULL UNIQUE,
    specialization VARCHAR(200),
    hospital_name VARCHAR(200),
    years_of_experience INT,
    certification TEXT,
    is_verified BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

-- Patients Table
CREATE TABLE patients (
    patient_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    emergency_contact_name VARCHAR(100),
    emergency_contact_phone VARCHAR(20),
    blood_type VARCHAR(10),
    height_cm INT,
    weight_kg DECIMAL(5,2),
    insurance_number VARCHAR(100),
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

-- Medical Conditions/Diseases Table
CREATE TABLE medical_conditions (
    condition_id INT PRIMARY KEY AUTO_INCREMENT,
    condition_name VARCHAR(200) NOT NULL,
    icd_code VARCHAR(20),
    description TEXT,
    severity ENUM('Mild', 'Moderate', 'Severe', 'Critical')
);

-- Medications Table
CREATE TABLE medications (
    medication_id INT PRIMARY KEY AUTO_INCREMENT,
    medication_name VARCHAR(200) NOT NULL,
    generic_name VARCHAR(200),
    dosage_form VARCHAR(100),
    strength VARCHAR(100),
    manufacturer VARCHAR(200)
);

-- Allergies Table
CREATE TABLE allergies (
    allergy_id INT PRIMARY KEY AUTO_INCREMENT,
    patient_id INT NOT NULL,
    allergen_name VARCHAR(200) NOT NULL,
    reaction_type VARCHAR(200),
    severity ENUM('Mild', 'Moderate', 'Severe', 'Life-threatening'),
    notes TEXT,
    recorded_by INT NOT NULL,
    recorded_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (patient_id) REFERENCES patients(patient_id) ON DELETE CASCADE,
    FOREIGN KEY (recorded_by) REFERENCES medical_staff(staff_id)
);

-- Medical Records Table
CREATE TABLE medical_records (
    record_id INT PRIMARY KEY AUTO_INCREMENT,
    patient_id INT NOT NULL,
    doctor_id INT NOT NULL,
    visit_date DATE NOT NULL,
    diagnosis TEXT NOT NULL,
    symptoms TEXT,
    treatment_plan TEXT,
    notes TEXT,
    follow_up_date DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (patient_id) REFERENCES patients(patient_id) ON DELETE CASCADE,
    FOREIGN KEY (doctor_id) REFERENCES medical_staff(staff_id)
);

-- Patient Conditions (Many-to-Many relationship)
CREATE TABLE patient_conditions (
    patient_condition_id INT PRIMARY KEY AUTO_INCREMENT,
    patient_id INT NOT NULL,
    condition_id INT NOT NULL,
    diagnosed_date DATE,
    status ENUM('Active', 'Resolved', 'Chronic', 'In_Treatment'),
    diagnosed_by INT NOT NULL,
    notes TEXT,
    FOREIGN KEY (patient_id) REFERENCES patients(patient_id) ON DELETE CASCADE,
    FOREIGN KEY (condition_id) REFERENCES medical_conditions(condition_id),
    FOREIGN KEY (diagnosed_by) REFERENCES medical_staff(staff_id)
);

-- Prescriptions Table
CREATE TABLE prescriptions (
    prescription_id INT PRIMARY KEY AUTO_INCREMENT,
    record_id INT NOT NULL,
    medication_id INT NOT NULL,
    dosage VARCHAR(100) NOT NULL,
    frequency VARCHAR(100) NOT NULL,
    duration VARCHAR(100),
    instructions TEXT,
    prescribed_date DATE DEFAULT (CURRENT_DATE),
    FOREIGN KEY (record_id) REFERENCES medical_records(record_id) ON DELETE CASCADE,
    FOREIGN KEY (medication_id) REFERENCES medications(medication_id)
);

-- Surgeries Table
CREATE TABLE surgeries (
    surgery_id INT PRIMARY KEY AUTO_INCREMENT,
    patient_id INT NOT NULL,
    surgeon_id INT NOT NULL,
    surgery_type VARCHAR(200) NOT NULL,
    surgery_date DATE NOT NULL,
    duration_minutes INT,
    anesthesia_type VARCHAR(100),
    pre_operative_notes TEXT,
    operative_notes TEXT,
    post_operative_notes TEXT,
    complications TEXT,
    outcome ENUM('Successful', 'Complications', 'Failed'),
    hospital_name VARCHAR(200),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (patient_id) REFERENCES patients(patient_id) ON DELETE CASCADE,
    FOREIGN KEY (surgeon_id) REFERENCES medical_staff(staff_id)
);

-- Lab Results Table
CREATE TABLE lab_results (
    lab_result_id INT PRIMARY KEY AUTO_INCREMENT,
    patient_id INT NOT NULL,
    test_name VARCHAR(200) NOT NULL,
    test_date DATE NOT NULL,
    result_value VARCHAR(500),
    normal_range VARCHAR(200),
    unit VARCHAR(50),
    status ENUM('Normal', 'Abnormal', 'Critical'),
    ordered_by INT NOT NULL,
    lab_name VARCHAR(200),
    notes TEXT,
    FOREIGN KEY (patient_id) REFERENCES patients(patient_id) ON DELETE CASCADE,
    FOREIGN KEY (ordered_by) REFERENCES medical_staff(staff_id)
);

-- Access Logs Table (for security and audit)
CREATE TABLE access_logs (
    log_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    patient_id INT,
    action VARCHAR(100) NOT NULL,
    ip_address VARCHAR(45),
    user_agent TEXT,
    access_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id),
    FOREIGN KEY (patient_id) REFERENCES patients(patient_id)
);

-- Insert default user types
INSERT INTO user_types (type_name, description) VALUES
('Patient', 'Patients who can view their own medical records'),
('Doctor', 'Licensed doctors who can create and update medical records'),
('Surgeon', 'Licensed surgeons who can perform surgeries and update records'),
('Admin', 'System administrators with full access');

-- Insert sample medical conditions
INSERT INTO medical_conditions (condition_name, icd_code, description, severity) VALUES
('Hypertension', 'I10', 'High blood pressure', 'Moderate'),
('Type 2 Diabetes', 'E11', 'Non-insulin dependent diabetes mellitus', 'Moderate'),
('Asthma', 'J45', 'Chronic respiratory condition', 'Mild'),
('Pneumonia', 'J18', 'Infection of the lungs', 'Severe'),
('Migraine', 'G43', 'Severe headache disorder', 'Mild');

-- Insert sample medications
INSERT INTO medications (medication_name, generic_name, dosage_form, strength, manufacturer) VALUES
('Lisinopril', 'Lisinopril', 'Tablet', '10mg', 'Generic Pharma'),
('Metformin', 'Metformin HCl', 'Tablet', '500mg', 'Generic Pharma'),
('Albuterol', 'Albuterol Sulfate', 'Inhaler', '90mcg', 'Respiratory Solutions'),
('Amoxicillin', 'Amoxicillin', 'Capsule', '500mg', 'Antibiotic Corp'),
('Ibuprofen', 'Ibuprofen', 'Tablet', '200mg', 'Pain Relief Inc');