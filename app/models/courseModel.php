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
