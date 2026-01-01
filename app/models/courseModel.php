<?php
    require_once 'db.php';

function getAllCourses() {
    $con = getConnection();
    $sql = "SELECT * FROM courses";
    $result = mysqli_query($con, $sql);
    $courses = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $courses[] = $row;
    }
    return $courses;
}

function countCourses() {
    $con = getConnection();
    $sql = "SELECT COUNT(*) AS total FROM courses";
    $result = mysqli_query($con, $sql);
    $data = mysqli_fetch_assoc($result);
    return $data['total'];
}

function getCourseByTitle($title) {
    $con = getConnection();
    $title = mysqli_real_escape_string($con, $title);
    $sql = "SELECT * FROM courses WHERE title='{$title}' LIMIT 1";
    $result = mysqli_query($con, $sql);
    if ($result && mysqli_num_rows($result) == 1) {
        return mysqli_fetch_assoc($result);
    } else {
        return false;
    }
}

function addCourse($course) {
    $con = getConnection();
    $title = mysqli_real_escape_string($con, $course['title']);
    $difficulty = mysqli_real_escape_string($con, $course['difficulty']);
    $duration = mysqli_real_escape_string($con, $course['duration']);
    $price = mysqli_real_escape_string($con, $course['price']);
    $rating = mysqli_real_escape_string($con, $course['rating']);
    $sql = "INSERT INTO courses (title, difficulty, duration, price, rating) VALUES ('$title', '$difficulty', '$duration', '$price', '$rating')";
    $result = mysqli_query($con, $sql);
    return $result;
}

function updateCourse($course) {
    $con = getConnection();
    $id = (int)$course['id'];
    $title = mysqli_real_escape_string($con, $course['title']);
    $difficulty = mysqli_real_escape_string($con, $course['difficulty']);
    $duration = mysqli_real_escape_string($con, $course['duration']);
    $price = mysqli_real_escape_string($con, $course['price']);
    $rating = mysqli_real_escape_string($con, $course['rating']);
    $sql = "UPDATE courses 
            SET title='$title', difficulty='$difficulty', duration='$duration', price='$price', rating='$rating' 
            WHERE id=$id";
    $result = mysqli_query($con, $sql);
    return $result;
}

function deleteCourse($id) {
    $con = getConnection();
    $id = (int)$id;
    $sql = "DELETE FROM courses WHERE id=$id";
    return mysqli_query($con, $sql);
}

function getCourseById($id) {
    $con = getConnection();
    $id = (int)$id;
    $sql = "SELECT * FROM courses WHERE id = $id LIMIT 1";
    $result = mysqli_query($con, $sql);
    
    if ($result && mysqli_num_rows($result) == 1) {
        return mysqli_fetch_assoc($result);
    } else {
        return false;
    }
}

function getEnrolledCourses($userId) {
    $con = getConnection();
    $userId = (int)$userId;
    $sql = "SELECT c.* FROM courses c 
            JOIN enrollments e ON c.id = e.course_id 
            WHERE e.user_id = $userId";
    $result = mysqli_query($con, $sql);
    $courses = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $courses[] = $row;
    }
    return $courses;
}

function enrollInCourse($userId, $courseId) {
    $con = getConnection();
    $userId = (int)$userId;
    $courseId = (int)$courseId;
    
    // Check if user is already enrolled
    $checkSql = "SELECT id FROM enrollments WHERE user_id = $userId AND course_id = $courseId";
    $checkResult = mysqli_query($con, $checkSql);
    
    if (mysqli_num_rows($checkResult) > 0) {
        return false; // Already enrolled
    }
    
    $sql = "INSERT INTO enrollments (user_id, course_id, payment_status) VALUES ($userId, $courseId, 'free')";
    return mysqli_query($con, $sql);
}

function getCategories() {
    $con = getConnection();
    $sql = "SELECT * FROM categories";
    $result = mysqli_query($con, $sql);
    $categories = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $categories[] = $row;
    }
    return $categories;
}
