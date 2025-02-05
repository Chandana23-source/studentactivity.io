<?php
require_once 'config.php';
requireLogin();

if (!hasRole('teacher') && !hasRole('admin')) {
    header('HTTP/1.1 403 Forbidden');
    exit('Access denied');
}

$type = $_GET['type'] ?? '';
$format = $_GET['format'] ?? 'csv';

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
    
    $stmt = $conn->prepare("SELECT * FROM $table");
    $stmt->execute();
    $result = $stmt->get_result();
    
    $filename = $type . '_details_' . date('Y-m-d') . '.' . $format;
    
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    
    $output = fopen('php://output', 'w');
    
    // Output headers
    $first = true;
    while ($row = $result->fetch_assoc()) {
        if ($first) {
            fputcsv($output, array_keys($row));
            $first = false;
        }
        fputcsv($output, $row);
    }
    
    fclose($output);
    
} catch (Exception $e) {
    header('HTTP/1.1 500 Internal Server Error');
    echo 'Error: ' . $e->getMessage();
}
?>