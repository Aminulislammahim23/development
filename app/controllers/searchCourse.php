<?php
require_once('../models/courseModel.php');
// Prevent PHP notices / warnings from corrupting JSON response
ini_set('display_errors', 0);
error_reporting(0);
ob_start();
header('Content-Type: application/json');

$query = trim($_POST['query'] ?? '');

if ($query === '') {
    ob_clean();
    echo json_encode(["status" => "not_found"]);
    exit;
}

$course = searchCourse($query);

if ($course) {
    ob_clean();
    echo json_encode([
        "status" => "found",
        "course" => $course
    ]);
} else {
    ob_clean();
    echo json_encode([
        "status" => "not_found"
    ]);
}
exit;
