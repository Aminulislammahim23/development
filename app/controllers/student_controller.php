<?php
/**
 * Basic Student Controller
 * Simple controller for student operations
 */
class StudentController {
    
    public function dashboard() {
        require_once '../models/courseModel.php';
        session_start();
        
        $userId = $_SESSION['user_id'];
        
        // Get enrolled courses
        $enrolledCourses = getEnrolledCourses($userId);
        
        // Load student dashboard view with data
        require_once '../views/student/dashboard.php';
    }
    
    public function viewCourse() {
        require_once '../models/courseModel.php';
        session_start();
        
        $courseId = (int)($_GET['course_id'] ?? 0);
        
        if ($courseId === 0) {
            header("Location: ../views/student/dashboard.php?error=invalid_course");
            return;
        }
        
        $course = getCourseById($courseId);
        if (!$course) {
            header("Location: ../views/student/dashboard.php?error=course_not_found");
            return;
        }
        
        // Check if user is enrolled in this course
        $isEnrolled = $this->isUserEnrolled($_SESSION['user_id'], $courseId);
        
        // Load course view with data
        require_once '../views/student/courseView.php';
    }
    
    public function enrollCourse() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require_once '../models/courseModel.php';
            session_start();
            
            $userId = $_SESSION['user_id'];
            $courseId = (int)($_POST['course_id'] ?? 0);
            
            if ($courseId === 0) {
                header("Location: ../views/student/dashboard.php?error=invalid_course");
                return;
            }
            
            // Check if already enrolled
            if ($this->isUserEnrolled($userId, $courseId)) {
                header("Location: ../views/student/dashboard.php?error=already_enrolled");
                return;
            }
            
            $result = enrollInCourse($userId, $courseId);
            
            if ($result) {
                header("Location: ../views/student/dashboard.php?success=enrollment_success");
            } else {
                header("Location: ../views/student/dashboard.php?error=enrollment_failed");
            }
        } else {
            header("Location: ../views/student/dashboard.php?error=invalid_request");
        }
    }
    
    public function processPayment() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require_once '../models/paymentModel.php';
            session_start();
            
            $userId = $_SESSION['user_id'];
            $courseId = (int)($_POST['course_id'] ?? 0);
            $amount = (float)($_POST['amount'] ?? 0);
            $paymentMethod = trim($_POST['payment_method'] ?? '');
            
            if ($courseId === 0 || $amount <= 0 || empty($paymentMethod)) {
                header("Location: ../views/student/dashboard.php?error=invalid_payment_data");
                return;
            }
            
            // Process payment
            $paymentData = [
                'user_id' => $userId,
                'course_id' => $courseId,
                'amount' => $amount,
                'payment_method' => $paymentMethod,
                'payment_status' => 'pending'
            ];
            
            $result = addPayment($paymentData);
            
            if ($result) {
                // For demo purposes, assume payment is successful
                
                // Enroll user in course
                require_once '../models/courseModel.php';
                $enrollmentResult = enrollInCourse($userId, $courseId);
                
                if ($enrollmentResult) {
                    header("Location: ../views/student/dashboard.php?success=payment_success");
                } else {
                    header("Location: ../views/student/dashboard.php?error=enrollment_failed_after_payment");
                }
            } else {
                header("Location: ../views/student/dashboard.php?error=payment_processing_failed");
            }
        } else {
            header("Location: ../views/student/dashboard.php?error=invalid_request");
        }
    }
    
    public function viewInvoices() {
        require_once '../models/paymentModel.php';
        session_start();
        
        $userId = $_SESSION['user_id'];
        
        $invoices = getPaymentsByUser($userId);
        
        // Load invoices view with data
        require_once '../views/student/invoices.php';
    }
    
    public function takeQuiz() {
        session_start();
        
        $courseId = (int)($_GET['course_id'] ?? 0);
        
        if ($courseId === 0) {
            header("Location: ../views/student/dashboard.php?error=invalid_course");
            return;
        }
        
        // Check if user is enrolled and has access to quizzes
        if (!$this->isUserEnrolled($_SESSION['user_id'], $courseId)) {
            header("Location: ../views/student/dashboard.php?error=not_enrolled");
            return;
        }
        
        // Get quiz questions for this course (placeholder)
        $quizQuestions = $this->getQuizQuestions($courseId);
        
        // Load quiz view with data
        require_once '../views/student/takeQuiz.php';
    }
    
    public function submitQuiz() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            session_start();
            
            $userId = $_SESSION['user_id'];
            $courseId = (int)($_POST['course_id'] ?? 0);
            
            if ($courseId === 0) {
                header("Location: ../views/student/dashboard.php?error=invalid_course");
                return;
            }
            
            // Process quiz submission (placeholder)
            $answers = $_POST['answers'] ?? [];
            $score = $this->calculateQuizScore($courseId, $answers);
            
            header("Location: ../views/student/dashboard.php?success=quiz_submitted&score=" . $score);
        } else {
            header("Location: ../views/student/dashboard.php?error=invalid_request");
        }
    }
    
    public function getCertificate() {
        require_once '../models/courseModel.php';
        session_start();
        
        $userId = $_SESSION['user_id'];
        $courseId = (int)($_GET['course_id'] ?? 0);
        
        if ($courseId === 0) {
            header("Location: ../views/student/dashboard.php?error=invalid_course");
            return;
        }
        
        // Check if user has completed the course
        if (!$this->isCourseCompleted($userId, $courseId)) {
            header("Location: ../views/student/dashboard.php?error=course_not_completed");
            return;
        }
        
        // Generate certificate data
        $certificateData = [
            'user' => $_SESSION,
            'course' => getCourseById($courseId),
            'completion_date' => date('Y-m-d'),
            'certificate_id' => uniqid('CERT_')
        ];
        
        // Load certificate view with data
        require_once '../views/student/getCertificate.php';
    }
    
    /**
     * Helper method to check if user is enrolled in a course
     */
    private function isUserEnrolled($userId, $courseId) {
        $con = getConnection();
        $userId = (int)$userId;
        $courseId = (int)$courseId;
        $sql = "SELECT id FROM enrollments WHERE user_id = $userId AND course_id = $courseId LIMIT 1";
        $result = mysqli_query($con, $sql);
        return $result && mysqli_num_rows($result) > 0;
    }
    
    /**
     * Helper method to get quiz questions for a course
     */
    private function getQuizQuestions($courseId) {
        // This would typically fetch from a quizzes/questions table
        // For now, returning sample data
        return [
            [
                'id' => 1,
                'question' => 'What is the main topic of this course?',
                'options' => ['Option A', 'Option B', 'Option C', 'Option D'],
                'correct_answer' => 0
            ]
        ];
    }
    
    /**
     * Helper method to calculate quiz score
     */
    private function calculateQuizScore($courseId, $answers) {
        // Implementation would depend on your quiz system
        // This is a simplified example
        return count($answers) > 0 ? 80 : 0; // Return 80% if any answers given
    }
    
    /**
     * Helper method to check if course is completed
     */
    private function isCourseCompleted($userId, $courseId) {
        $con = getConnection();
        $userId = (int)$userId;
        $courseId = (int)$courseId;
        
        // Check if user has completed required percentage (e.g., 80%)
        $sql = "SELECT completed_percentage FROM progress 
                WHERE user_id = $userId AND course_id = $courseId LIMIT 1";
        $result = mysqli_query($con, $sql);
        
        if ($result && mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            return $row['completed_percentage'] >= 80;
        }
        
        return false;
    }
}