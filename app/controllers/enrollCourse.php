<?php
session_start();
require_once '../models/courseModel.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: ../views/auth/login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_SESSION['user_id'];
    $courseId = (int)$_POST['course_id'] ?? 0;
    
    if ($courseId <= 0) {
        header("Location: ../views/student/dashboard.php?error=invalid_course");
        exit();
    }
    
    $course = getCourseById($courseId);
    
    if (!$course) {
        header("Location: ../views/student/dashboard.php?error=course_not_found");
        exit();
    }
    
    $result = enrollInCourse($userId, $courseId);
    
    if ($result) {
        // For free courses, set up receipt info for confirmation page
        if ($course['price'] <= 0) {
            $_SESSION['receipt'] = [
                'course' => $course['title'],
                'amount' => 0,
                'date' => date('Y-m-d H:i:s')
            ];
            header("Location: ../views/student/enrollmentConfirmation.php");
        } else {
            header("Location: ../views/student/dashboard.php?success=enrolled");
        }
        exit();
    } else {
        header("Location: ../views/student/dashboard.php?error=enrollment_failed");
        exit();
    }
} else {
    header("Location: ../views/student/dashboard.php?error=invalid_request");
    exit();
}
?>