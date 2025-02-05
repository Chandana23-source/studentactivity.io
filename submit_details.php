<?php
require_once 'config.php';
requireLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $type = $_POST['type'];
    $response = ['success' => false, 'message' => ''];
    
    try {
        $uploadDir = 'uploads/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        switch ($type) {
            case 'placement':
                $fileName = uploadFile('offer_letter', $uploadDir);
                $stmt = $conn->prepare("INSERT INTO placement_details (student_id, name, usn, phone, company, ctc, year, semester, section, offer_letter_path) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("isssssiiis", 
                    $_SESSION['user_id'],
                    $_POST['name'],
                    $_POST['usn'],
                    $_POST['phone'],
                    $_POST['company'],
                    $_POST['ctc'],
                    $_POST['year'],
                    $_POST['semester'],
                    $_POST['section'],
                    $fileName
                );
                break;
                
            case 'internship':
                $fileName = uploadFile('offer_letter', $uploadDir);
                $stmt = $conn->prepare("INSERT INTO internship_details (student_id, name, usn, phone, company, semester, section, offer_letter_path) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("issssiis", 
                    $_SESSION['user_id'],
                    $_POST['name'],
                    $_POST['usn'],
                    $_POST['phone'],
                    $_POST['company'],
                    $_POST['semester'],
                    $_POST['section'],
                    $fileName
                );
                break;
                
            case 'beHonors':
                $fileName = uploadFile('certificate', $uploadDir);
                $stmt = $conn->prepare("INSERT INTO be_honors_details (student_id, name, usn, marks_sem1, marks_sem2, marks_sem3, marks_sem4, course, semester, section, certificate_path) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("issddddsiss", 
                    $_SESSION['user_id'],
                    $_POST['name'],
                    $_POST['usn'],
                    $_POST['marks1'],
                    $_POST['marks2'],
                    $_POST['marks3'],
                    $_POST['marks4'],
                    $_POST['course'],
                    $_POST['semester'],
                    $_POST['section'],
                    $fileName
                );
                break;
                
            case 'additionalCourse':
                $fileName = uploadFile('certificate', $uploadDir);
                $stmt = $conn->prepare("INSERT INTO additional_courses (student_id, name, usn, courses, year, duration, semester, section, certificate_path) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("isssisiss", 
                    $_SESSION['user_id'],
                    $_POST['name'],
                    $_POST['usn'],
                    $_POST['courses'],
                    $_POST['year'],
                    $_POST['duration'],
                    $_POST['semester'],
                    $_POST['section'],
                    $fileName
                );
                break;
        }
        
        if ($stmt->execute()) {
            $response['success'] = true;
            $response['message'] = 'Details submitted successfully!';
        } else {
            throw new Exception($stmt->error);
        }
        
    } catch (Exception $e) {
        $response['message'] = 'Error: ' . $e->getMessage();
    }
    
    header('Content-Type: application/json');
    echo json_encode($response);
    exit();
}

function uploadFile($inputName, $uploadDir) {
    if (!isset($_FILES[$inputName])) {
        throw new Exception('No file uploaded');
    }
    
    $file = $_FILES[$inputName];
    $fileName = uniqid() . '_' . basename($file['name']);
    $targetPath = $uploadDir . $fileName;
    
    if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
        throw new Exception('Failed to upload file');
    }
    
    return $fileName;
}
?>