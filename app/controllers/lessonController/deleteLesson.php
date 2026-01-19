<?php
session_start();
require_once '../../../models/courseModel.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if user is logged in and is an instructor
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'instructor') {
        echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
        exit();
    }

    // Get lesson ID
    $lesson_id = $_POST['id'] ?? '';

    if (empty($lesson_id)) {
        echo json_encode(['success' => false, 'message' => 'Lesson ID is required']);
        exit();
    }

    // Check if the lesson belongs to a course owned by the instructor
    $instructorId = $_SESSION['user_id'];
    $courses = getCoursesByInstructor($instructorId);
    $validLesson = false;
    
    foreach ($courses as $course) {
        $courseLessons = getLessonsByCourseId($course['id']);
        foreach ($courseLessons as $lesson) {
            if ($lesson['id'] == $lesson_id) {
                $validLesson = true;
                break 2; // Break out of both loops
            }
        }
    }

    if (!$validLesson) {
        echo json_encode(['success' => false, 'message' => 'Invalid lesson ID for this instructor']);
        exit();
    }

    // Delete lesson from database
    $result = deleteLesson($lesson_id);

    if ($result) {
        echo json_encode(['success' => true, 'message' => 'Lesson deleted successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to delete lesson']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>