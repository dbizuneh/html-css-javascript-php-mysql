<?php
session_start();
require_once __DIR__ . '/../config/database.php';

class Auth {
    private $conn;
    
    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }
    
    public function login($username, $password) {
        $query = "SELECT u.user_id, u.username, u.password_hash, u.first_name, u.last_name, 
                         ut.type_name, ms.staff_id, p.patient_id 
                  FROM users u 
                  JOIN user_types ut ON u.user_type_id = ut.type_id 
                  LEFT JOIN medical_staff ms ON u.user_id = ms.user_id 
                  LEFT JOIN patients p ON u.user_id = p.user_id 
                  WHERE u.username = :username AND u.is_active = 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (password_verify($password, $user['password_hash'])) {
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['first_name'] = $user['first_name'];
                $_SESSION['last_name'] = $user['last_name'];
                $_SESSION['user_type'] = $user['type_name'];
                $_SESSION['staff_id'] = $user['staff_id'];
                $_SESSION['patient_id'] = $user['patient_id'];
                
                $this->logAccess($user['user_id'], null, 'Login');
                return true;
            }
        }
        return false;
    }
    
    public function register($userData) {
        $this->conn->beginTransaction();
        
        try {
            // Insert into users table
            $query = "INSERT INTO users (username, email, password_hash, first_name, last_name, 
                                       phone, date_of_birth, gender, address, user_type_id) 
                      VALUES (:username, :email, :password_hash, :first_name, :last_name, 
                              :phone, :date_of_birth, :gender, :address, :user_type_id)";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':username', $userData['username']);
            $stmt->bindParam(':email', $userData['email']);
            $stmt->bindParam(':password_hash', password_hash($userData['password'], PASSWORD_DEFAULT));
            $stmt->bindParam(':first_name', $userData['first_name']);
            $stmt->bindParam(':last_name', $userData['last_name']);
            $stmt->bindParam(':phone', $userData['phone']);
            $stmt->bindParam(':date_of_birth', $userData['date_of_birth']);
            $stmt->bindParam(':gender', $userData['gender']);
            $stmt->bindParam(':address', $userData['address']);
            $stmt->bindParam(':user_type_id', $userData['user_type_id']);
            
            $stmt->execute();
            $user_id = $this->conn->lastInsertId();
            
            // If registering as patient, add to patients table
            if ($userData['user_type_id'] == 1) { // Patient type
                $patientQuery = "INSERT INTO patients (user_id, emergency_contact_name, 
                                                     emergency_contact_phone, blood_type) 
                                VALUES (:user_id, :emergency_contact_name, :emergency_contact_phone, :blood_type)";
                
                $patientStmt = $this->conn->prepare($patientQuery);
                $patientStmt->bindParam(':user_id', $user_id);
                $patientStmt->bindParam(':emergency_contact_name', $userData['emergency_contact_name']);
                $patientStmt->bindParam(':emergency_contact_phone', $userData['emergency_contact_phone']);
                $patientStmt->bindParam(':blood_type', $userData['blood_type']);
                $patientStmt->execute();
            }
            
            // If registering as medical staff, add to medical_staff table
            if ($userData['user_type_id'] == 2 || $userData['user_type_id'] == 3) { // Doctor or Surgeon
                $staffQuery = "INSERT INTO medical_staff (user_id, license_number, specialization, 
                                                        hospital_name, years_of_experience) 
                              VALUES (:user_id, :license_number, :specialization, :hospital_name, :years_of_experience)";
                
                $staffStmt = $this->conn->prepare($staffQuery);
                $staffStmt->bindParam(':user_id', $user_id);
                $staffStmt->bindParam(':license_number', $userData['license_number']);
                $staffStmt->bindParam(':specialization', $userData['specialization']);
                $staffStmt->bindParam(':hospital_name', $userData['hospital_name']);
                $staffStmt->bindParam(':years_of_experience', $userData['years_of_experience']);
                $staffStmt->execute();
            }
            
            $this->conn->commit();
            return true;
            
        } catch (Exception $e) {
            $this->conn->rollback();
            return false;
        }
    }
    
    public function logout() {
        if (isset($_SESSION['user_id'])) {
            $this->logAccess($_SESSION['user_id'], null, 'Logout');
        }
        session_destroy();
    }
    
    public function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }
    
    public function getUserType() {
        return isset($_SESSION['user_type']) ? $_SESSION['user_type'] : null;
    }
    
    public function isMedicalStaff() {
        $userType = $this->getUserType();
        return in_array($userType, ['Doctor', 'Surgeon', 'Admin']);
    }
    
    public function requireLogin() {
        if (!$this->isLoggedIn()) {
            header('Location: login.php');
            exit();
        }
    }
    
    public function requireMedicalStaff() {
        $this->requireLogin();
        if (!$this->isMedicalStaff()) {
            header('Location: dashboard.php');
            exit();
        }
    }
    
    private function logAccess($user_id, $patient_id, $action) {
        $query = "INSERT INTO access_logs (user_id, patient_id, action, ip_address, user_agent) 
                  VALUES (:user_id, :patient_id, :action, :ip_address, :user_agent)";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':patient_id', $patient_id);
        $stmt->bindParam(':action', $action);
        $stmt->bindParam(':ip_address', $_SERVER['REMOTE_ADDR']);
        $stmt->bindParam(':user_agent', $_SERVER['HTTP_USER_AGENT']);
        $stmt->execute();
    }
    
    public function logPatientAccess($patient_id, $action) {
        if (isset($_SESSION['user_id'])) {
            $this->logAccess($_SESSION['user_id'], $patient_id, $action);
        }
    }
}
?>