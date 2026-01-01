<?php
session_start();
require_once '../../models/courseModel.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $difficulty = trim($_POST['difficulty'] ?? '');
    $duration = trim($_POST['duration'] ?? '');
    $price = trim($_POST['price'] ?? '');
    $rating = trim($_POST['rating'] ?? '');

    if ($title === '' || $difficulty === '' || $duration === '' || $price === '' || $rating === '') {
        header("Location: ../../views/instructor/dashboard.php?error=empty_course_fields");
        exit;
    }

    $course = [
        'title' => $title,
        'difficulty' => $difficulty,
        'duration' => $duration,
        'price' => $price,
        'rating' => $rating,
    ];

    $result = addCourse($course);

    if ($result) {
        header("Location: ../../views/instructor/dashboard.php?success=course_added");
        exit;
    } else {
        header("Location: ../../views/instructor/dashboard.php?error=course_add_failed");
        exit;
    }
} else {
    header("Location: ../../views/instructor/dashboard.php?error=invalid_request");
    exit;
}
?>