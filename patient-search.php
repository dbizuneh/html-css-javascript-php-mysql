<?php
require_once 'includes/auth.php';
require_once 'classes/MedicalRecord.php';

$auth = new Auth();
$auth->requireMedicalStaff();

$medicalRecord = new MedicalRecord();
$searchResults = [];
$searchTerm = '';

if (isset($_GET['search']) && !empty($_GET['search'])) {
    $searchTerm = $_GET['search'];
    $searchResults = $medicalRecord->searchPatients($searchTerm);
    $auth->logPatientAccess(null, 'Patient Search: ' . $searchTerm);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Search - Medical Records System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .search-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
        }
        .patient-card {
            border-left: 4px solid #007bff;
            transition: transform 0.2s ease;
        }
        .patient-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
        }
        .btn-view {
            background: linear-gradient(45deg, #007bff, #0056b3);
            border: none;
            color: white;
        }
        .btn-view:hover {
            background: linear-gradient(45deg, #0056b3, #004085);
            color: white;
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
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="dashboard.php">
                    <i class="fas fa-arrow-left me-1"></i>Back to Dashboard
                </a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row">
            <div class="col-12">
                <div class="search-card card">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0"><i class="fas fa-search me-2"></i>Patient Search</h4>
                    </div>
                    <div class="card-body">
                        <form method="GET" class="mb-4">
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="input-group input-group-lg">
                                        <span class="input-group-text">
                                            <i class="fas fa-search"></i>
                                        </span>
                                        <input type="text" class="form-control" name="search" 
                                               placeholder="Search by patient name, ID, phone, or blood type..."
                                               value="<?php echo htmlspecialchars($searchTerm); ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <button type="submit" class="btn btn-primary btn-lg w-100">
                                        <i class="fas fa-search me-2"></i>Search Patients
                                    </button>
                                </div>
                            </div>
                        </form>
                        
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Search Tips:</strong> You can search by patient name, patient ID, phone number, or blood type. 
                            Enter at least 2 characters for name searches or exact patient ID for specific results.
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php if (!empty($searchTerm)): ?>
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="fas fa-users me-2"></i>Search Results for "<?php echo htmlspecialchars($searchTerm); ?>"
                            </h5>
                            <span class="badge bg-primary"><?php echo count($searchResults); ?> patient(s) found</span>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($searchResults)): ?>
                                <div class="row">
                                    <?php foreach ($searchResults as $patient): ?>
                                        <div class="col-md-6 col-lg-4 mb-3">
                                            <div class="card patient-card h-100">
                                                <div class="card-body">
                                                    <h6 class="card-title text-primary">
                                                        <i class="fas fa-user me-2"></i>
                                                        <?php echo htmlspecialchars($patient['patient_name']); ?>
                                                    </h6>
                                                    
                                                    <div class="mb-2">
                                                        <small class="text-muted">Patient ID:</small>
                                                        <span class="fw-bold"><?php echo htmlspecialchars($patient['patient_id']); ?></span>
                                                    </div>
                                                    
                                                    <?php if (!empty($patient['date_of_birth'])): ?>
                                                        <div class="mb-2">
                                                            <small class="text-muted">Date of Birth:</small><br>
                                                            <span><?php echo date('M d, Y', strtotime($patient['date_of_birth'])); ?></span>
                                                        </div>
                                                    <?php endif; ?>
                                                    
                                                    <?php if (!empty($patient['blood_type'])): ?>
                                                        <div class="mb-2">
                                                            <small class="text-muted">Blood Type:</small>
                                                            <span class="badge bg-danger ms-1"><?php echo htmlspecialchars($patient['blood_type']); ?></span>
                                                        </div>
                                                    <?php endif; ?>
                                                    
                                                    <?php if (!empty($patient['phone'])): ?>
                                                        <div class="mb-3">
                                                            <small class="text-muted">Phone:</small><br>
                                                            <span><?php echo htmlspecialchars($patient['phone']); ?></span>
                                                        </div>
                                                    <?php endif; ?>
                                                    
                                                    <div class="d-grid">
                                                        <a href="patient-details.php?id=<?php echo $patient['patient_id']; ?>" 
                                                           class="btn btn-view">
                                                            <i class="fas fa-eye me-2"></i>View Medical History
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <div class="text-center py-5">
                                    <i class="fas fa-user-slash fa-3x text-muted mb-3"></i>
                                    <h5 class="text-muted">No patients found</h5>
                                    <p class="text-muted">
                                        No patients match your search criteria. Please try:
                                    </p>
                                    <ul class="list-unstyled text-muted">
                                        <li>• Checking the spelling of the patient's name</li>
                                        <li>• Searching with partial names</li>
                                        <li>• Using the exact patient ID</li>
                                        <li>• Searching by phone number or blood type</li>
                                    </ul>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <?php if (empty($searchTerm)): ?>
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body text-center py-5">
                            <i class="fas fa-search fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Search for Patients</h5>
                            <p class="text-muted">
                                Use the search form above to find patients by name, ID, phone number, or blood type.
                                This will help you quickly access their medical records and history.
                            </p>
                            <div class="row mt-4">
                                <div class="col-md-3">
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <i class="fas fa-user fa-2x text-primary mb-2"></i>
                                            <h6>By Name</h6>
                                            <small class="text-muted">Search using first name, last name, or full name</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <i class="fas fa-id-card fa-2x text-success mb-2"></i>
                                            <h6>By Patient ID</h6>
                                            <small class="text-muted">Enter exact patient ID for specific results</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <i class="fas fa-phone fa-2x text-info mb-2"></i>
                                            <h6>By Phone</h6>
                                            <small class="text-muted">Search using patient's phone number</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <i class="fas fa-tint fa-2x text-danger mb-2"></i>
                                            <h6>By Blood Type</h6>
                                            <small class="text-muted">Find patients with specific blood types</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto-focus on search input when page loads
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.querySelector('input[name="search"]');
            if (searchInput && !searchInput.value) {
                searchInput.focus();
            }
        });
        
        // Add loading state to search button
        document.querySelector('form').addEventListener('submit', function() {
            const submitBtn = this.querySelector('button[type="submit"]');
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Searching...';
            submitBtn.disabled = true;
        });
    </script>
</body>
</html>