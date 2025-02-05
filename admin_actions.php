<?php
require_once 'config.php';
requireLogin();

if (!hasRole('admin')) {
    header('HTTP/1.1 403 Forbidden');
    exit('Access denied');
}

$response = ['success' => false, 'message' => ''];

try {
    $input = json_decode(file_get_contents('php://input'), true);
    if (!$input) {
        $input = $_POST; // Fallback to POST data if JSON parsing fails
    }
    
    $action = $input['action'] ?? '';
    
    switch ($action) {
        case 'delete':
            $ids = $input['ids'] ?? [];
            $type = $input['type'] ?? '';
            
            if (empty($ids) || empty($type)) {
                throw new Exception('Invalid parameters');
            }
            
            $table = '';
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
            
            $idList = implode(',', array_map('intval', $ids));
            $sql = "DELETE FROM $table WHERE id IN ($idList)";
            
            if ($conn->query($sql)) {
                $response['success'] = true;
                $response['message'] = 'Records deleted successfully';
            } else {
                throw new Exception($conn->error);
            }
            break;
            
        case 'update':
            $id = $input['id'] ?? '';
            $type = $input['type'] ?? '';
            $data = $input['data'] ?? [];
            
            if (empty($id) || empty($type) || empty($data)) {
                throw new Exception('Invalid parameters');
            }
            
            $table = '';
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
            
            $setClauses = [];
            $params = [];
            $types = '';
            
            foreach ($data as $key => $value) {
                if ($key !== 'id') {
                    $setClauses[] = "$key = ?";
                    $params[] = $value;
                    $types .= 's'; // Assuming all fields are strings, adjust if needed
                }
            }
            
            $params[] = $id;
            $types .= 'i';
            
            $sql = "UPDATE $table SET " . implode(', ', $setClauses) . " WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param($types, ...$params);
            
            if ($stmt->execute()) {
                $response['success'] = true;
                $response['message'] = 'Record updated successfully';
            } else {
                throw new Exception($stmt->error);
            }
            break;
            
        default:
            throw new Exception('Invalid action specified');
    }
} catch (Exception $e) {
    $response['message'] = 'Error: ' . $e->getMessage();
}

header('Content-Type: application/json');
echo json_encode($response);
?>