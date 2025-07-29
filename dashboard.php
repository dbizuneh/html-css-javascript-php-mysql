<?php
require_once 'includes/auth.php';
require_once 'classes/MedicalRecord.php';

$auth = new Auth();
$auth->requireLogin();

$medicalRecord = new MedicalRecord();
$userType = $auth->getUserType();
$userName = $_SESSION['first_name'] . ' ' . $_SESSION['last_name'];

// Get dashboard data based on user type
$dashboardData = [];
if ($userType === 'Patient') {
    $patientId = $_SESSION['patient_id'];
    $dashboardData = $medicalRecord->getPatientHistory($patientId);
} else {
    // For medical staff, show recent patients or activity
    $dashboardData['recent_patients'] = $medicalRecord->searchPatients('');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medical Records System - Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .navbar-brand {
            font-weight: bold;
            color: #007bff !important;
        }
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }
        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        .stat-card .card-body {
            text-align: center;
            padding: 2rem;
        }
        .stat-number {
            font-size: 2.5rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }
        .sidebar {
            background: linear-gradient(180deg, #667eea 0%, #764ba2 100%);
            min-height: calc(100vh - 56px);
            border-radius: 0 15px 15px 0;
        }
        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.8);
            border-radius: 10px;
            margin: 5px 0;
            transition: all 0.3s ease;
        }
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            transform: translateX(5px);
        }
        .content-area {
            padding: 20px;
        }
        .patient-card {
            border-left: 4px solid #007bff;
        }
        .allergy-badge {
            background: #dc3545;
            color: white;
            border-radius: 20px;
            padding: 5px 15px;
            margin: 3px;
            display: inline-block;
            font-size: 0.85rem;
        }
        .condition-badge {
            background: #ffc107;
            color: #212529;
            border-radius: 20px;
            padding: 5px 15px;
            margin: 3px;
            display: inline-block;
            font-size: 0.85rem;
        }
        .timeline-item {
            border-left: 3px solid #007bff;
            padding-left: 20px;
            margin-bottom: 20px;
            position: relative;
        }
        .timeline-item::before {
            content: '';
            position: absolute;
            left: -7px;
            top: 5px;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: #007bff;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="dashboard.php">
                <i class="fas fa-heartbeat me-2"></i>Medical Records System
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user-circle me-1"></i><?php echo htmlspecialchars($userName); ?>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="profile.php"><i class="fas fa-user me-2"></i>Profile</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-lg-2 col-md-3 p-0">
                <div class="sidebar p-3">
                    <nav class="nav flex-column">
                        <a class="nav-link active" href="dashboard.php">
                            <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                        </a>
                        
                        <?php if ($userType === 'Patient'): ?>
                            <a class="nav-link" href="my-records.php">
                                <i class="fas fa-file-medical me-2"></i>My Records
                            </a>
                            <a class="nav-link" href="my-allergies.php">
                                <i class="fas fa-exclamation-triangle me-2"></i>Allergies
                            </a>
                            <a class="nav-link" href="my-surgeries.php">
                                <i class="fas fa-cut me-2"></i>Surgeries
                            </a>
                        <?php else: ?>
                            <a class="nav-link" href="patient-search.php">
                                <i class="fas fa-search me-2"></i>Find Patient
                            </a>
                            <a class="nav-link" href="add-record.php">
                                <i class="fas fa-plus-circle me-2"></i>Add Record
                            </a>
                            <?php if ($userType === 'Surgeon'): ?>
                                <a class="nav-link" href="add-surgery.php">
                                    <i class="fas fa-cut me-2"></i>Add Surgery
                                </a>
                            <?php endif; ?>
                        <?php endif; ?>
                    </nav>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-lg-10 col-md-9">
                <div class="content-area">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h1 class="h3 mb-0">Welcome, <?php echo htmlspecialchars($userName); ?></h1>
                            <p class="text-muted">User Type: <?php echo htmlspecialchars($userType); ?></p>
                        </div>
                        <div class="text-end">
                            <small class="text-muted">Last Login: <?php echo date('M d, Y h:i A'); ?></small>
                        </div>
                    </div>

                    <?php if ($userType === 'Patient'): ?>
                        <!-- Patient Dashboard -->
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <div class="card stat-card">
                                    <div class="card-body">
                                        <div class="stat-number"><?php echo count($dashboardData['medical_records'] ?? []); ?></div>
                                        <div>Medical Records</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card stat-card">
                                    <div class="card-body">
                                        <div class="stat-number"><?php echo count($dashboardData['allergies'] ?? []); ?></div>
                                        <div>Known Allergies</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card stat-card">
                                    <div class="card-body">
                                        <div class="stat-number"><?php echo count($dashboardData['conditions'] ?? []); ?></div>
                                        <div>Current Conditions</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card stat-card">
                                    <div class="card-body">
                                        <div class="stat-number"><?php echo count($dashboardData['surgeries'] ?? []); ?></div>
                                        <div>Surgeries</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Patient Info Card -->
                        <?php if (isset($dashboardData['patient_info'])): ?>
                            <div class="row mb-4">
                                <div class="col-12">
                                    <div class="card patient-card">
                                        <div class="card-header bg-primary text-white">
                                            <h5 class="mb-0"><i class="fas fa-user me-2"></i>Patient Information</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <p><strong>Patient ID:</strong> <?php echo htmlspecialchars($dashboardData['patient_info']['patient_id']); ?></p>
                                                    <p><strong>Blood Type:</strong> 
                                                        <span class="badge bg-danger"><?php echo htmlspecialchars($dashboardData['patient_info']['blood_type'] ?? 'Not specified'); ?></span>
                                                    </p>
                                                    <p><strong>Date of Birth:</strong> <?php echo htmlspecialchars($dashboardData['patient_info']['date_of_birth'] ?? 'Not specified'); ?></p>
                                                </div>
                                                <div class="col-md-6">
                                                    <p><strong>Emergency Contact:</strong> <?php echo htmlspecialchars($dashboardData['patient_info']['emergency_contact_name'] ?? 'Not specified'); ?></p>
                                                    <p><strong>Emergency Phone:</strong> <?php echo htmlspecialchars($dashboardData['patient_info']['emergency_contact_phone'] ?? 'Not specified'); ?></p>
                                                    <p><strong>Gender:</strong> <?php echo htmlspecialchars($dashboardData['patient_info']['gender'] ?? 'Not specified'); ?></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Allergies and Conditions -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header bg-danger text-white">
                                        <h5 class="mb-0"><i class="fas fa-exclamation-triangle me-2"></i>Allergies</h5>
                                    </div>
                                    <div class="card-body">
                                        <?php if (!empty($dashboardData['allergies'])): ?>
                                            <?php foreach ($dashboardData['allergies'] as $allergy): ?>
                                                <span class="allergy-badge">
                                                    <?php echo htmlspecialchars($allergy['allergen_name']); ?>
                                                    (<?php echo htmlspecialchars($allergy['severity']); ?>)
                                                </span>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <p class="text-muted">No known allergies recorded.</p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header bg-warning text-dark">
                                        <h5 class="mb-0"><i class="fas fa-stethoscope me-2"></i>Current Conditions</h5>
                                    </div>
                                    <div class="card-body">
                                        <?php if (!empty($dashboardData['conditions'])): ?>
                                            <?php foreach ($dashboardData['conditions'] as $condition): ?>
                                                <span class="condition-badge">
                                                    <?php echo htmlspecialchars($condition['condition_name']); ?>
                                                </span>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <p class="text-muted">No current conditions recorded.</p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Recent Medical Records -->
                        <div class="card">
                            <div class="card-header bg-info text-white">
                                <h5 class="mb-0"><i class="fas fa-file-medical me-2"></i>Recent Medical Records</h5>
                            </div>
                            <div class="card-body">
                                <?php if (!empty($dashboardData['medical_records'])): ?>
                                    <div class="timeline">
                                        <?php foreach (array_slice($dashboardData['medical_records'], 0, 5) as $record): ?>
                                            <div class="timeline-item">
                                                <h6 class="fw-bold"><?php echo htmlspecialchars($record['diagnosis']); ?></h6>
                                                <p class="text-muted mb-1">
                                                    <i class="fas fa-calendar me-1"></i><?php echo date('M d, Y', strtotime($record['visit_date'])); ?>
                                                    <i class="fas fa-user-md ms-3 me-1"></i><?php echo htmlspecialchars($record['doctor_name']); ?>
                                                </p>
                                                <?php if (!empty($record['symptoms'])): ?>
                                                    <p class="mb-1"><strong>Symptoms:</strong> <?php echo htmlspecialchars($record['symptoms']); ?></p>
                                                <?php endif; ?>
                                                <?php if (!empty($record['prescriptions'])): ?>
                                                    <p class="mb-0"><strong>Medications:</strong>
                                                        <?php foreach ($record['prescriptions'] as $prescription): ?>
                                                            <span class="badge bg-primary me-1"><?php echo htmlspecialchars($prescription['medication_name']); ?></span>
                                                        <?php endforeach; ?>
                                                    </p>
                                                <?php endif; ?>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                    <a href="my-records.php" class="btn btn-outline-primary">View All Records</a>
                                <?php else: ?>
                                    <p class="text-muted">No medical records found.</p>
                                <?php endif; ?>
                            </div>
                        </div>

                    <?php else: ?>
                        <!-- Medical Staff Dashboard -->
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <div class="card text-center">
                                    <div class="card-body">
                                        <i class="fas fa-search fa-3x text-primary mb-3"></i>
                                        <h5>Find Patient</h5>
                                        <p class="text-muted">Search for patients by name or ID to view their medical history.</p>
                                        <a href="patient-search.php" class="btn btn-primary">Search Patients</a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card text-center">
                                    <div class="card-body">
                                        <i class="fas fa-plus-circle fa-3x text-success mb-3"></i>
                                        <h5>Add Medical Record</h5>
                                        <p class="text-muted">Create new medical records, prescriptions, and diagnoses.</p>
                                        <a href="add-record.php" class="btn btn-success">Add Record</a>
                                    </div>
                                </div>
                            </div>
                            <?php if ($userType === 'Surgeon'): ?>
                                <div class="col-md-4">
                                    <div class="card text-center">
                                        <div class="card-body">
                                            <i class="fas fa-cut fa-3x text-warning mb-3"></i>
                                            <h5>Surgery Records</h5>
                                            <p class="text-muted">Record surgical procedures and complications.</p>
                                            <a href="add-surgery.php" class="btn btn-warning">Add Surgery</a>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Quick Patient Search -->
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fas fa-users me-2"></i>Quick Patient Access</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="input-group">
                                            <input type="text" class="form-control" id="quickSearch" placeholder="Search patient by name or ID...">
                                            <button class="btn btn-outline-primary" type="button" onclick="searchPatient()">
                                                <i class="fas fa-search"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div id="searchResults" class="mt-3"></div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function searchPatient() {
            const searchTerm = document.getElementById('quickSearch').value;
            if (searchTerm.length < 2) {
                document.getElementById('searchResults').innerHTML = '<p class="text-muted">Enter at least 2 characters to search.</p>';
                return;
            }
            
            // In a real implementation, this would make an AJAX call to search patients
            document.getElementById('searchResults').innerHTML = '<p class="text-info">Searching...</p>';
            
            // Simulate search - in real implementation, use fetch() to call search API
            setTimeout(() => {
                document.getElementById('searchResults').innerHTML = 
                    '<p class="text-muted">Search functionality would return patient results here. <a href="patient-search.php">Go to advanced search</a></p>';
            }, 1000);
        }
        
        // Allow Enter key to trigger search
        document.getElementById('quickSearch').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                searchPatient();
            }
        });
    </script>
</body>
</html>