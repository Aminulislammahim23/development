<?php
/**
 * Basic Instructor Controller
 * Simple controller for instructor operations
 */
class InstructorController {
    
    public function dashboard() {
        require_once '../models/courseModel.php';
        // Get instructor-specific data
        session_start();
        $instructorId = $_SESSION['user_id'];
        
        // Get instructor-specific statistics
        $totalCourses = countCoursesByInstructor($instructorId);
        $totalEnrollments = $this->countEnrollmentsByInstructor($instructorId);
        $totalStudents = $this->countStudentsByInstructor($instructorId);
        $totalRevenue = $this->countRevenueByInstructor($instructorId);
        
        // Load instructor dashboard view with data
        require_once '../views/instructor/dashboard.php';
    }
    
    public function addCourse() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require_once '../models/courseModel.php';
            session_start();
            
            $title = trim($_POST['title'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $category_id = (int)($_POST['category_id'] ?? 1);
            $difficulty = trim($_POST['difficulty'] ?? '');
            $duration = trim($_POST['duration'] ?? '');
            $price = trim($_POST['price'] ?? '0');
            $rating = trim($_POST['rating'] ?? '0');
            $courseImage = $_FILES['course_image'] ?? null;
            
            if ($title === '' || $difficulty === '' || $duration === '') {
                header("Location: ../views/instructor/dashboard.php?error=empty_course_fields");
                return;
            }
            
            // Handle course image upload
            $courseImageName = 'default.png';
            
            if ($courseImage && isset($courseImage['name']) && $courseImage['name'] !== '' && $courseImage['error'] === 0) {
                $ext = strtolower(pathinfo($courseImage['name'], PATHINFO_EXTENSION));
                $allowed = ['jpg', 'jpeg', 'png', 'gif'];
                
                if (in_array($ext, $allowed)) {
                    // Check file size (max 2MB)
                    if ($courseImage['size'] <= 2 * 1024 * 1024) {
                        $courseImageName = 'course_' . uniqid() . '.' . $ext;
                        $uploadPath = "../assets/images/courses/" . $courseImageName;
                        
                        // Create directory if it doesn't exist
                        if (!is_dir("../assets/images/courses/")) {
                            mkdir("../assets/images/courses/", 0777, true);
                        }
                        
                        if (!move_uploaded_file($courseImage['tmp_name'], $uploadPath)) {
                            $courseImageName = 'default.png'; // Fallback to default on upload failure
                            error_log("Failed to upload course image");
                        }
                    }
                }
            }
            
            $course = [
                'title' => $title,
                'description' => $description,
                'category_id' => $category_id,
                'instructor_id' => $_SESSION['user_id'], // Set instructor ID automatically
                'difficulty' => $difficulty,
                'duration' => $duration,
                'price' => $price,
                'rating' => $rating,
                'course_image' => $courseImageName,
            ];
            
            $result = addCourse($course);
            
            if ($result) {
                header("Location: ../views/instructor/dashboard.php?success=course_added");
            } else {
                header("Location: ../views/instructor/dashboard.php?error=course_add_failed");
            }
        } else {
            header("Location: ../views/instructor/dashboard.php?error=invalid_request");
        }
    }
    
    public function updateCourse() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require_once '../models/courseModel.php';
            session_start();
            
            $id = (int)($_POST['id'] ?? 0);
            $title = trim($_POST['title'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $category_id = (int)($_POST['category_id'] ?? 1);
            $difficulty = trim($_POST['difficulty'] ?? '');
            $duration = trim($_POST['duration'] ?? '');
            $price = trim($_POST['price'] ?? '0');
            $rating = trim($_POST['rating'] ?? '0');
            
            if ($id === 0 || $title === '' || $difficulty === '' || $duration === '') {
                header("Location: ../views/instructor/dashboard.php?error=missing_required_fields");
                return;
            }
            
            // Check if instructor owns this course
            $course = getCourseById($id);
            if (!$course || $course['instructor_id'] != $_SESSION['user_id']) {
                header("Location: ../views/instructor/dashboard.php?error=unauthorized_access");
                return;
            }
            
            $courseData = [
                'id' => $id,
                'title' => $title,
                'description' => $description,
                'category_id' => $category_id,
                'difficulty' => $difficulty,
                'duration' => $duration,
                'price' => $price,
                'rating' => $rating
            ];
            
            $result = updateCourse($courseData);
            
            if ($result) {
                header("Location: ../views/instructor/dashboard.php?success=course_updated");
            } else {
                header("Location: ../views/instructor/dashboard.php?error=course_update_failed");
            }
        } else {
            header("Location: ../views/instructor/dashboard.php?error=invalid_request");
        }
    }
    
    public function deleteCourse() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require_once '../models/courseModel.php';
            session_start();
            
            $id = (int)($_POST['id'] ?? 0);
            
            if ($id === 0) {
                header("Location: ../views/instructor/dashboard.php?error=empty_course_id");
                return;
            }
            
            // Check if instructor owns this course
            $course = getCourseById($id);
            if (!$course || $course['instructor_id'] != $_SESSION['user_id']) {
                header("Location: ../views/instructor/dashboard.php?error=unauthorized_access");
                return;
            }
            
            $result = deleteCourse($id);
            
            if ($result) {
                header("Location: ../views/instructor/dashboard.php?success=course_deleted");
            } else {
                header("Location: ../views/instructor/dashboard.php?error=course_delete_failed");
            }
        } else {
            header("Location: ../views/instructor/dashboard.php?error=invalid_request");
        }
    }
    
    public function searchCourse() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require_once '../models/courseModel.php';
            session_start();
            
            $query = trim($_POST['query'] ?? '');
            $instructorId = $_SESSION['user_id'];
            
            if (!empty($query) && $instructorId > 0) {
                // First try to find by ID
                if (is_numeric($query)) {
                    $course = getCourseById((int)$query);
                    if ($course && $course['instructor_id'] == $instructorId) {
                        // Get category name
                        $categoryName = $this->getCategoryName($course['category_id']);
                        $course['category_name'] = $categoryName;
                        echo json_encode(['success' => true, 'course' => $course]);
                        return;
                    }
                }
                
                // If not found by ID, search by title
                $con = getConnection();
                $safeQuery = mysqli_real_escape_string($con, $query);
                $sql = "SELECT * FROM courses WHERE title LIKE '%$safeQuery%' AND instructor_id = $instructorId LIMIT 1";
                $result = mysqli_query($con, $sql);
                
                if ($result && mysqli_num_rows($result) > 0) {
                    $course = mysqli_fetch_assoc($result);
                    $categoryName = $this->getCategoryName($course['category_id']);
                    $course['category_name'] = $categoryName;
                    echo json_encode(['success' => true, 'course' => $course]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Course not found']);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Invalid search parameters']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
        }
    }
    
    /**
     * Helper method to get category name
     */
    private function getCategoryName($categoryId) {
        $con = getConnection();
        $categoryId = (int)$categoryId;
        $sql = "SELECT name FROM categories WHERE id = $categoryId LIMIT 1";
        $result = mysqli_query($con, $sql);
        
        if ($result && mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            return $row['name'];
        }
        return 'Unknown Category';
    }
    
    /**
     * Helper method to count enrollments for instructor's courses
     */
    private function countEnrollmentsByInstructor($instructorId) {
        $con = getConnection();
        $instructorId = (int)$instructorId;
        $sql = "SELECT COUNT(*) as total FROM enrollments e 
                JOIN courses c ON e.course_id = c.id 
                WHERE c.instructor_id = $instructorId";
        $result = mysqli_query($con, $sql);
        $data = mysqli_fetch_assoc($result);
        return $data['total'] ?? 0;
    }
    
    /**
     * Helper method to count unique students for instructor's courses
     */
    private function countStudentsByInstructor($instructorId) {
        $con = getConnection();
        $instructorId = (int)$instructorId;
        $sql = "SELECT COUNT(DISTINCT e.user_id) as total FROM enrollments e 
                JOIN courses c ON e.course_id = c.id 
                WHERE c.instructor_id = $instructorId";
        $result = mysqli_query($con, $sql);
        $data = mysqli_fetch_assoc($result);
        return $data['total'] ?? 0;
    }
    
    /**
     * Helper method to calculate total revenue for instructor's courses
     */
    private function countRevenueByInstructor($instructorId) {
        $con = getConnection();
        $instructorId = (int)$instructorId;
        $sql = "SELECT SUM(c.price) as total FROM courses c 
                JOIN enrollments e ON c.id = e.course_id 
                WHERE c.instructor_id = $instructorId AND e.payment_status = 'completed'";
        $result = mysqli_query($con, $sql);
        $data = mysqli_fetch_assoc($result);
        return $data['total'] ?? 0;
    }
}