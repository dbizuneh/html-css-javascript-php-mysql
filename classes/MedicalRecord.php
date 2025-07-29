<?php
require_once __DIR__ . '/../config/database.php';

class MedicalRecord {
    private $conn;
    
    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }
    
    // Get patient's complete medical history
    public function getPatientHistory($patient_id) {
        $history = [];
        
        // Get basic patient info
        $query = "SELECT p.*, u.first_name, u.last_name, u.date_of_birth, u.gender, u.phone, u.address 
                  FROM patients p 
                  JOIN users u ON p.user_id = u.user_id 
                  WHERE p.patient_id = :patient_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':patient_id', $patient_id);
        $stmt->execute();
        $history['patient_info'] = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Get medical records
        $history['medical_records'] = $this->getMedicalRecords($patient_id);
        
        // Get allergies
        $history['allergies'] = $this->getAllergies($patient_id);
        
        // Get current conditions
        $history['conditions'] = $this->getCurrentConditions($patient_id);
        
        // Get surgeries
        $history['surgeries'] = $this->getSurgeries($patient_id);
        
        // Get recent lab results
        $history['lab_results'] = $this->getLabResults($patient_id);
        
        return $history;
    }
    
    public function getMedicalRecords($patient_id, $limit = 10) {
        $query = "SELECT mr.*, 
                         CONCAT(u.first_name, ' ', u.last_name) as doctor_name,
                         ms.specialization
                  FROM medical_records mr 
                  JOIN medical_staff ms ON mr.doctor_id = ms.staff_id 
                  JOIN users u ON ms.user_id = u.user_id 
                  WHERE mr.patient_id = :patient_id 
                  ORDER BY mr.visit_date DESC 
                  LIMIT :limit";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':patient_id', $patient_id);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        $records = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Get prescriptions for each record
        foreach ($records as &$record) {
            $record['prescriptions'] = $this->getPrescriptions($record['record_id']);
        }
        
        return $records;
    }
    
    public function addMedicalRecord($data) {
        $this->conn->beginTransaction();
        
        try {
            // Insert medical record
            $query = "INSERT INTO medical_records (patient_id, doctor_id, visit_date, diagnosis, 
                                                 symptoms, treatment_plan, notes, follow_up_date) 
                      VALUES (:patient_id, :doctor_id, :visit_date, :diagnosis, :symptoms, 
                              :treatment_plan, :notes, :follow_up_date)";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':patient_id', $data['patient_id']);
            $stmt->bindParam(':doctor_id', $data['doctor_id']);
            $stmt->bindParam(':visit_date', $data['visit_date']);
            $stmt->bindParam(':diagnosis', $data['diagnosis']);
            $stmt->bindParam(':symptoms', $data['symptoms']);
            $stmt->bindParam(':treatment_plan', $data['treatment_plan']);
            $stmt->bindParam(':notes', $data['notes']);
            $stmt->bindParam(':follow_up_date', $data['follow_up_date']);
            
            $stmt->execute();
            $record_id = $this->conn->lastInsertId();
            
            // Add prescriptions if any
            if (isset($data['prescriptions']) && !empty($data['prescriptions'])) {
                foreach ($data['prescriptions'] as $prescription) {
                    $this->addPrescription($record_id, $prescription);
                }
            }
            
            $this->conn->commit();
            return $record_id;
            
        } catch (Exception $e) {
            $this->conn->rollback();
            throw $e;
        }
    }
    
    public function getAllergies($patient_id) {
        $query = "SELECT a.*, CONCAT(u.first_name, ' ', u.last_name) as recorded_by_name 
                  FROM allergies a 
                  JOIN medical_staff ms ON a.recorded_by = ms.staff_id 
                  JOIN users u ON ms.user_id = u.user_id 
                  WHERE a.patient_id = :patient_id 
                  ORDER BY a.recorded_date DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':patient_id', $patient_id);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function addAllergy($data) {
        $query = "INSERT INTO allergies (patient_id, allergen_name, reaction_type, severity, notes, recorded_by) 
                  VALUES (:patient_id, :allergen_name, :reaction_type, :severity, :notes, :recorded_by)";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':patient_id', $data['patient_id']);
        $stmt->bindParam(':allergen_name', $data['allergen_name']);
        $stmt->bindParam(':reaction_type', $data['reaction_type']);
        $stmt->bindParam(':severity', $data['severity']);
        $stmt->bindParam(':notes', $data['notes']);
        $stmt->bindParam(':recorded_by', $data['recorded_by']);
        
        return $stmt->execute();
    }
    
    public function getCurrentConditions($patient_id) {
        $query = "SELECT pc.*, mc.condition_name, mc.icd_code, mc.description, mc.severity,
                         CONCAT(u.first_name, ' ', u.last_name) as diagnosed_by_name 
                  FROM patient_conditions pc 
                  JOIN medical_conditions mc ON pc.condition_id = mc.condition_id 
                  JOIN medical_staff ms ON pc.diagnosed_by = ms.staff_id 
                  JOIN users u ON ms.user_id = u.user_id 
                  WHERE pc.patient_id = :patient_id AND pc.status IN ('Active', 'Chronic', 'In_Treatment') 
                  ORDER BY pc.diagnosed_date DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':patient_id', $patient_id);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function addPatientCondition($data) {
        $query = "INSERT INTO patient_conditions (patient_id, condition_id, diagnosed_date, status, diagnosed_by, notes) 
                  VALUES (:patient_id, :condition_id, :diagnosed_date, :status, :diagnosed_by, :notes)";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':patient_id', $data['patient_id']);
        $stmt->bindParam(':condition_id', $data['condition_id']);
        $stmt->bindParam(':diagnosed_date', $data['diagnosed_date']);
        $stmt->bindParam(':status', $data['status']);
        $stmt->bindParam(':diagnosed_by', $data['diagnosed_by']);
        $stmt->bindParam(':notes', $data['notes']);
        
        return $stmt->execute();
    }
    
    public function getSurgeries($patient_id) {
        $query = "SELECT s.*, CONCAT(u.first_name, ' ', u.last_name) as surgeon_name, ms.specialization 
                  FROM surgeries s 
                  JOIN medical_staff ms ON s.surgeon_id = ms.staff_id 
                  JOIN users u ON ms.user_id = u.user_id 
                  WHERE s.patient_id = :patient_id 
                  ORDER BY s.surgery_date DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':patient_id', $patient_id);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function addSurgery($data) {
        $query = "INSERT INTO surgeries (patient_id, surgeon_id, surgery_type, surgery_date, 
                                       duration_minutes, anesthesia_type, pre_operative_notes, 
                                       operative_notes, post_operative_notes, complications, 
                                       outcome, hospital_name) 
                  VALUES (:patient_id, :surgeon_id, :surgery_type, :surgery_date, :duration_minutes, 
                          :anesthesia_type, :pre_operative_notes, :operative_notes, :post_operative_notes, 
                          :complications, :outcome, :hospital_name)";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':patient_id', $data['patient_id']);
        $stmt->bindParam(':surgeon_id', $data['surgeon_id']);
        $stmt->bindParam(':surgery_type', $data['surgery_type']);
        $stmt->bindParam(':surgery_date', $data['surgery_date']);
        $stmt->bindParam(':duration_minutes', $data['duration_minutes']);
        $stmt->bindParam(':anesthesia_type', $data['anesthesia_type']);
        $stmt->bindParam(':pre_operative_notes', $data['pre_operative_notes']);
        $stmt->bindParam(':operative_notes', $data['operative_notes']);
        $stmt->bindParam(':post_operative_notes', $data['post_operative_notes']);
        $stmt->bindParam(':complications', $data['complications']);
        $stmt->bindParam(':outcome', $data['outcome']);
        $stmt->bindParam(':hospital_name', $data['hospital_name']);
        
        return $stmt->execute();
    }
    
    public function getPrescriptions($record_id) {
        $query = "SELECT p.*, m.medication_name, m.generic_name, m.dosage_form, m.strength 
                  FROM prescriptions p 
                  JOIN medications m ON p.medication_id = m.medication_id 
                  WHERE p.record_id = :record_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':record_id', $record_id);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function addPrescription($record_id, $data) {
        $query = "INSERT INTO prescriptions (record_id, medication_id, dosage, frequency, duration, instructions) 
                  VALUES (:record_id, :medication_id, :dosage, :frequency, :duration, :instructions)";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':record_id', $record_id);
        $stmt->bindParam(':medication_id', $data['medication_id']);
        $stmt->bindParam(':dosage', $data['dosage']);
        $stmt->bindParam(':frequency', $data['frequency']);
        $stmt->bindParam(':duration', $data['duration']);
        $stmt->bindParam(':instructions', $data['instructions']);
        
        return $stmt->execute();
    }
    
    public function getLabResults($patient_id, $limit = 10) {
        $query = "SELECT lr.*, CONCAT(u.first_name, ' ', u.last_name) as ordered_by_name 
                  FROM lab_results lr 
                  JOIN medical_staff ms ON lr.ordered_by = ms.staff_id 
                  JOIN users u ON ms.user_id = u.user_id 
                  WHERE lr.patient_id = :patient_id 
                  ORDER BY lr.test_date DESC 
                  LIMIT :limit";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':patient_id', $patient_id);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function searchPatients($search_term) {
        $query = "SELECT p.patient_id, CONCAT(u.first_name, ' ', u.last_name) as patient_name, 
                         u.date_of_birth, p.blood_type, u.phone 
                  FROM patients p 
                  JOIN users u ON p.user_id = u.user_id 
                  WHERE u.first_name LIKE :search OR u.last_name LIKE :search 
                     OR CONCAT(u.first_name, ' ', u.last_name) LIKE :search 
                     OR p.patient_id = :exact_id 
                  ORDER BY u.last_name, u.first_name";
        
        $search_param = '%' . $search_term . '%';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':search', $search_param);
        $stmt->bindParam(':exact_id', $search_term);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getMedications() {
        $query = "SELECT * FROM medications ORDER BY medication_name";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getMedicalConditions() {
        $query = "SELECT * FROM medical_conditions ORDER BY condition_name";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>