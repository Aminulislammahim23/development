<?php
session_start();
require_once '../../models/courseModel.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;

    if ($id === null) {
        header("Location: ../../views/instructor/dashboard.php?error=empty_course_id");
        exit;
    }

    $result = deleteCourse($id);

    if ($result) {
        header("Location: ../../views/instructor/dashboard.php?success=course_deleted");
        exit;
    } else {
        header("Location: ../../views/instructor/dashboard.php?error=course_delete_failed");
        exit;
    }
} else {
    header("Location: ../../views/instructor/dashboard.php?error=invalid_request");
    exit;
}
?>