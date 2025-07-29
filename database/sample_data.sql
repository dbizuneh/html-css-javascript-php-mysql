-- Sample Data for Medical Records System
-- Run this after creating the database schema

-- Sample patients
INSERT INTO users (username, email, password_hash, first_name, last_name, phone, date_of_birth, gender, address, user_type_id) VALUES
('john.doe', 'john.doe@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'John', 'Doe', '555-0101', '1985-03-15', 'Male', '123 Main St, City, State 12345', 1),
('jane.smith', 'jane.smith@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Jane', 'Smith', '555-0102', '1990-07-22', 'Female', '456 Oak Ave, City, State 12345', 1),
('mike.johnson', 'mike.johnson@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mike', 'Johnson', '555-0103', '1978-12-08', 'Male', '789 Pine Rd, City, State 12345', 1);

-- Sample doctors
INSERT INTO users (username, email, password_hash, first_name, last_name, phone, date_of_birth, gender, address, user_type_id) VALUES
('dr.wilson', 'dr.wilson@hospital.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Sarah', 'Wilson', '555-0201', '1975-05-10', 'Female', '100 Medical Center Dr, City, State 12345', 2),
('dr.brown', 'dr.brown@hospital.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'David', 'Brown', '555-0202', '1980-09-18', 'Male', '100 Medical Center Dr, City, State 12345', 2);

-- Sample surgeon
INSERT INTO users (username, email, password_hash, first_name, last_name, phone, date_of_birth, gender, address, user_type_id) VALUES
('dr.garcia', 'dr.garcia@hospital.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Maria', 'Garcia', '555-0203', '1972-11-25', 'Female', '100 Medical Center Dr, City, State 12345', 3);

-- Insert patient records
INSERT INTO patients (user_id, emergency_contact_name, emergency_contact_phone, blood_type, height_cm, weight_kg) VALUES
(1, 'Mary Doe', '555-0111', 'O+', 175, 70.5),
(2, 'Robert Smith', '555-0112', 'A-', 165, 58.2),
(3, 'Linda Johnson', '555-0113', 'B+', 180, 85.0);

-- Insert medical staff records
INSERT INTO medical_staff (user_id, license_number, specialization, hospital_name, years_of_experience, is_verified) VALUES
(4, 'MD001234', 'Internal Medicine', 'City General Hospital', 15, TRUE),
(5, 'MD001235', 'Cardiology', 'City General Hospital', 12, TRUE),
(6, 'MD001236', 'General Surgery', 'City General Hospital', 18, TRUE);

-- Sample medical records
INSERT INTO medical_records (patient_id, doctor_id, visit_date, diagnosis, symptoms, treatment_plan, notes, follow_up_date) VALUES
(1, 1, '2024-01-15', 'Hypertension', 'High blood pressure, headaches', 'Prescribed ACE inhibitor, lifestyle changes', 'Patient advised to monitor BP daily', '2024-02-15'),
(1, 1, '2024-02-15', 'Hypertension follow-up', 'BP improved, occasional headaches', 'Continue medication, increase exercise', 'Good progress, patient compliant', '2024-05-15'),
(2, 2, '2024-01-20', 'Chest pain evaluation', 'Chest pain during exercise', 'EKG and stress test ordered', 'Probable angina, requires further testing', '2024-02-01'),
(3, 1, '2024-01-25', 'Type 2 Diabetes', 'Increased thirst, frequent urination', 'Metformin prescribed, dietary counseling', 'HbA1c elevated at 8.2%', '2024-04-25');

-- Sample prescriptions
INSERT INTO prescriptions (record_id, medication_id, dosage, frequency, duration, instructions) VALUES
(1, 1, '10mg', 'Once daily', '30 days', 'Take in the morning with food'),
(2, 1, '10mg', 'Once daily', '90 days', 'Continue as prescribed, monitor BP'),
(4, 2, '500mg', 'Twice daily', '30 days', 'Take with meals to reduce stomach upset');

-- Sample allergies
INSERT INTO allergies (patient_id, allergen_name, reaction_type, severity, notes, recorded_by) VALUES
(1, 'Penicillin', 'Skin rash', 'Moderate', 'Developed rash within 2 hours of taking penicillin', 1),
(2, 'Shellfish', 'Anaphylaxis', 'Life-threatening', 'Severe allergic reaction to shrimp, carries EpiPen', 2),
(3, 'Latex', 'Contact dermatitis', 'Mild', 'Skin irritation when exposed to latex gloves', 1);

-- Sample patient conditions
INSERT INTO patient_conditions (patient_id, condition_id, diagnosed_date, status, diagnosed_by, notes) VALUES
(1, 1, '2024-01-15', 'Active', 1, 'Well controlled with medication'),
(2, 1, '2024-01-20', 'In_Treatment', 2, 'Under evaluation for cardiac catheterization'),
(3, 2, '2024-01-25', 'Active', 1, 'Newly diagnosed, starting treatment');

-- Sample surgery (for demonstration)
INSERT INTO surgeries (patient_id, surgeon_id, surgery_type, surgery_date, duration_minutes, anesthesia_type, pre_operative_notes, operative_notes, post_operative_notes, complications, outcome, hospital_name) VALUES
(2, 3, 'Cardiac Catheterization', '2024-02-05', 90, 'Local anesthesia with sedation', 'Patient fasting, pre-operative EKG normal', 'Single vessel disease found in LAD, stent placed successfully', 'Patient stable, no complications noted', 'None', 'Successful', 'City General Hospital');

-- Sample lab results
INSERT INTO lab_results (patient_id, test_name, test_date, result_value, normal_range, unit, status, ordered_by, lab_name, notes) VALUES
(1, 'Blood Pressure', '2024-01-15', '160/95', '120/80', 'mmHg', 'Abnormal', 1, 'City Lab', 'Elevated, requires treatment'),
(1, 'Blood Pressure', '2024-02-15', '135/85', '120/80', 'mmHg', 'Abnormal', 1, 'City Lab', 'Improved but still elevated'),
(3, 'HbA1c', '2024-01-25', '8.2', '4.0-5.6', '%', 'Critical', 1, 'City Lab', 'Significantly elevated, diabetes confirmed'),
(3, 'Fasting Glucose', '2024-01-25', '185', '70-100', 'mg/dL', 'Critical', 1, 'City Lab', 'Elevated, consistent with diabetes');

-- Note: The password for all sample users is 'password123'
-- Hash generated with: password_hash('password123', PASSWORD_DEFAULT)

-- Display sample login credentials
SELECT 'Sample Login Credentials:' as Info;
SELECT 'Patients:' as Type, username, 'password123' as Password FROM users WHERE user_type_id = 1;
SELECT 'Doctors:' as Type, username, 'password123' as Password FROM users WHERE user_type_id = 2;
SELECT 'Surgeons:' as Type, username, 'password123' as Password FROM users WHERE user_type_id = 3;