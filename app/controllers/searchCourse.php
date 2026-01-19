<?php
session_start();
require_once '../models/courseModel.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if user is logged in and is an instructor
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'instructor') {
        echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
        exit();
    }

    $query = $_POST['query'] ?? '';
    $instructorId = $_POST['instructor_id'] ?? $_SESSION['user_id'];

    if (empty($query)) {
        echo json_encode(['success' => false, 'message' => 'Query is required']);
        exit();
    }

    // Get all courses for this instructor
    $courses = getCoursesByInstructor($instructorId);

    // Find the course that matches the query (by ID or title)
    $foundCourse = null;
    foreach ($courses as $course) {
        if ($course['id'] == $query || stripos($course['title'], $query) !== false) {
            $foundCourse = $course;
            
            // Get category name for the course
            $categories = getCategories();
            foreach ($categories as $category) {
                if ($category['id'] == $course['category_id']) {
                    $foundCourse['category_name'] = $category['name'];
                    break;
                }
            }
            
            break;
        }
    }

    if ($foundCourse) {
        echo json_encode([
            'success' => true,
            'course' => $foundCourse
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Course not found'
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method'
    ]);
}
?>