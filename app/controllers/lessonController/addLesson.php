<?php
session_start();
require_once '../../../models/courseModel.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if user is logged in and is an instructor
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'instructor') {
        echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
        exit();
    }

    // Get form data
    $course_id = $_POST['course_id'] ?? '';
    $title = $_POST['title'] ?? '';
    $video_url = $_POST['video_url'] ?? '';
    $content = $_POST['content'] ?? '';
    $lesson_order = $_POST['lesson_order'] ?? 0;

    // Validate required fields
    if (empty($course_id) || empty($title)) {
        echo json_encode(['success' => false, 'message' => 'Course ID and Title are required']);
        exit();
    }

    // Check if the course belongs to the instructor
    $instructorId = $_SESSION['user_id'];
    $courses = getCoursesByInstructor($instructorId);
    $validCourse = false;
    foreach ($courses as $course) {
        if ($course['id'] == $course_id) {
            $validCourse = true;
            break;
        }
    }

    if (!$validCourse) {
        echo json_encode(['success' => false, 'message' => 'Invalid course ID for this instructor']);
        exit();
    }

    // Prepare lesson data
    $lessonData = [
        'course_id' => $course_id,
        'title' => $title,
        'video_url' => $video_url,
        'content' => $content,
        'lesson_order' => $lesson_order
    ];

    // Add lesson to database
    $result = addLesson($lessonData);

    if ($result) {
        echo json_encode(['success' => true, 'message' => 'Lesson added successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to add lesson']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>