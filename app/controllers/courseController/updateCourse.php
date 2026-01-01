<?php
session_start();
require_once '../../models/courseModel.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    $title = trim($_POST['title'] ?? '');
    $difficulty = trim($_POST['difficulty'] ?? '');
    $duration = trim($_POST['duration'] ?? '');
    $price = trim($_POST['price'] ?? '');
    $rating = trim($_POST['rating'] ?? '');

    if ($id === null || $title === '' || $difficulty === '' || $duration === '' || $price === '' || $rating === '') {
        header("Location: ../../views/instructor/dashboard.php?error=empty_course_fields");
        exit;
    }

    $course = [
        'id' => $id,
        'title' => $title,
        'difficulty' => $difficulty,
        'duration' => $duration,
        'price' => $price,
        'rating' => $rating
    ];

    $result = updateCourse($course);

    if ($result) {
        header("Location: ../../views/instructor/dashboard.php?success=course_updated");
        exit;
    } else {
        header("Location: ../../views/instructor/dashboard.php?error=course_update_failed");
        exit;
    }
} else {
    header("Location: ../../views/instructor/dashboard.php?error=invalid_request");
    exit;
}
?>
