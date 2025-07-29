<?php
require_once 'includes/auth.php';

$auth = new Auth();
$success = '';
$error = '';

if ($_POST) {
    $userData = $_POST;
    
    // Basic validation
    if (empty($userData['username']) || empty($userData['password']) || empty($userData['email'])) {
        $error = 'Please fill in all required fields';
    } elseif ($userData['password'] !== $userData['confirm_password']) {
        $error = 'Passwords do not match';
    } elseif (strlen($userData['password']) < 6) {
        $error = 'Password must be at least 6 characters long';
    } else {
        try {
            if ($auth->register($userData)) {
                $success = 'Registration successful! You can now login.';
            } else {
                $error = 'Registration failed. Username or email may already exist.';
            }
        } catch (Exception $e) {
            $error = 'Registration failed: ' . $e->getMessage();
        }
    }
}

if ($auth->isLoggedIn()) {
    header('Location: dashboard.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medical Records System - Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px 0;
        }
        .register-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
        }
        .medical-icon {
            background: linear-gradient(45deg, #28a745, #20c997);
            border-radius: 50%;
            width: 80px;
            height: 80px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
        }
        .btn-success {
            background: linear-gradient(45deg, #28a745, #20c997);
            border: none;
            border-radius: 25px;
            padding: 12px 30px;
            font-weight: 600;
        }
        .btn-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(40, 167, 69, 0.3);
        }
        .form-control, .form-select {
            border-radius: 10px;
            border: 2px solid #e9ecef;
            padding: 10px 15px;
            transition: all 0.3s ease;
        }
        .form-control:focus, .form-select:focus {
            border-color: #28a745;
            box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
        }
        .medical-staff-fields {
            display: none;
        }
        .nav-pills .nav-link.active {
            background: linear-gradient(45deg, #28a745, #20c997);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="register-card p-5">
                    <div class="text-center mb-4">
                        <div class="medical-icon">
                            <i class="fas fa-user-plus text-white fa-2x"></i>
                        </div>
                        <h2 class="fw-bold text-dark">Register Account</h2>
                        <p class="text-muted">Join our secure healthcare management system</p>
                    </div>
                    
                    <?php if ($error): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <?php echo htmlspecialchars($error); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($success): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>
                            <?php echo htmlspecialchars($success); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" id="registerForm">
                        <!-- User Type Selection -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Account Type</label>
                            <ul class="nav nav-pills nav-justified" id="userTypeTabs">
                                <li class="nav-item">
                                    <a class="nav-link active" href="#" data-type="1">
                                        <i class="fas fa-user me-2"></i>Patient
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="#" data-type="2">
                                        <i class="fas fa-user-md me-2"></i>Doctor
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="#" data-type="3">
                                        <i class="fas fa-user-md me-2"></i>Surgeon
                                    </a>
                                </li>
                            </ul>
                            <input type="hidden" name="user_type_id" id="userTypeId" value="1">
                        </div>
                        
                        <!-- Basic Information -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="first_name" class="form-label">First Name *</label>
                                <input type="text" class="form-control" id="first_name" name="first_name" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="last_name" class="form-label">Last Name *</label>
                                <input type="text" class="form-control" id="last_name" name="last_name" required>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="username" class="form-label">Username *</label>
                                <input type="text" class="form-control" id="username" name="username" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email *</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="password" class="form-label">Password *</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="confirm_password" class="form-label">Confirm Password *</label>
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label">Phone</label>
                                <input type="tel" class="form-control" id="phone" name="phone">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="date_of_birth" class="form-label">Date of Birth</label>
                                <input type="date" class="form-control" id="date_of_birth" name="date_of_birth">
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="gender" class="form-label">Gender</label>
                                <select class="form-select" id="gender" name="gender">
                                    <option value="">Select Gender</option>
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="address" class="form-label">Address</label>
                            <textarea class="form-control" id="address" name="address" rows="2"></textarea>
                        </div>
                        
                        <!-- Patient Specific Fields -->
                        <div id="patientFields" class="patient-fields">
                            <h5 class="text-primary mb-3">Patient Information</h5>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="emergency_contact_name" class="form-label">Emergency Contact Name</label>
                                    <input type="text" class="form-control" id="emergency_contact_name" name="emergency_contact_name">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="emergency_contact_phone" class="form-label">Emergency Contact Phone</label>
                                    <input type="tel" class="form-control" id="emergency_contact_phone" name="emergency_contact_phone">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="blood_type" class="form-label">Blood Type</label>
                                    <select class="form-select" id="blood_type" name="blood_type">
                                        <option value="">Select Blood Type</option>
                                        <option value="A+">A+</option>
                                        <option value="A-">A-</option>
                                        <option value="B+">B+</option>
                                        <option value="B-">B-</option>
                                        <option value="AB+">AB+</option>
                                        <option value="AB-">AB-</option>
                                        <option value="O+">O+</option>
                                        <option value="O-">O-</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Medical Staff Specific Fields -->
                        <div id="staffFields" class="medical-staff-fields">
                            <h5 class="text-success mb-3">Medical Staff Information</h5>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="license_number" class="form-label">License Number *</label>
                                    <input type="text" class="form-control" id="license_number" name="license_number">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="specialization" class="form-label">Specialization</label>
                                    <input type="text" class="form-control" id="specialization" name="specialization">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="hospital_name" class="form-label">Hospital/Clinic Name</label>
                                    <input type="text" class="form-control" id="hospital_name" name="hospital_name">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="years_of_experience" class="form-label">Years of Experience</label>
                                    <input type="number" class="form-control" id="years_of_experience" name="years_of_experience" min="0">
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-grid mb-4">
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="fas fa-user-plus me-2"></i>Register Account
                            </button>
                        </div>
                    </form>
                    
                    <div class="text-center">
                        <p class="text-muted mb-0">Already have an account?</p>
                        <a href="login.php" class="fw-semibold text-decoration-none">Login Here</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Handle user type selection
        document.querySelectorAll('#userTypeTabs .nav-link').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                
                // Update active tab
                document.querySelectorAll('#userTypeTabs .nav-link').forEach(l => l.classList.remove('active'));
                this.classList.add('active');
                
                // Update hidden input
                const userType = this.getAttribute('data-type');
                document.getElementById('userTypeId').value = userType;
                
                // Show/hide relevant fields
                const patientFields = document.getElementById('patientFields');
                const staffFields = document.getElementById('staffFields');
                
                if (userType === '1') { // Patient
                    patientFields.style.display = 'block';
                    staffFields.style.display = 'none';
                    // Remove required from staff fields
                    staffFields.querySelectorAll('input[required]').forEach(input => input.removeAttribute('required'));
                } else { // Doctor or Surgeon
                    patientFields.style.display = 'none';
                    staffFields.style.display = 'block';
                    // Add required to license number
                    document.getElementById('license_number').setAttribute('required', 'required');
                }
            });
        });
        
        // Form validation
        document.getElementById('registerForm').addEventListener('submit', function(e) {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            
            if (password !== confirmPassword) {
                e.preventDefault();
                alert('Passwords do not match!');
                return false;
            }
            
            if (password.length < 6) {
                e.preventDefault();
                alert('Password must be at least 6 characters long!');
                return false;
            }
        });
    </script>
</body>
</html>