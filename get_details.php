<?php
require_once 'config.php';
requireLogin();

$type = $_GET['type'] ?? '';
$response = ['success' => false, 'data' => [], 'message' => ''];

try {
    switch ($type) {
        case 'placement':
            $table = 'placement_details';
            break;
        case 'internship':
            $table = 'internship_details';
            break;
        case 'beHonors':
            $table = 'be_honors_details';
            break;
        case 'additionalCourse':
            $table = 'additional_courses';
            break;
        default:
            throw new Exception('Invalid type specified');
    }
    
    $sql = "SELECT * FROM $table";
    if (hasRole('student')) {
        $sql .= " WHERE student_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $_SESSION['user_id']);
    } else {
        $stmt = $conn->prepare($sql);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    $data = [];
    
    while ($row = $result->fetch_assoc()) {
        // Remove sensitive information
        unset($row['student_id']);
        $data[] = $row;
    }
    
    $response['success'] = true;
    $response['data'] = $data;
    
} catch (Exception $e) {
    $response['message'] = 'Error: ' . $e->getMessage();
}

header('Content-Type: application/json');
echo json_encode($response);
?>