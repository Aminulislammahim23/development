<?php
session_start();
require_once '../models/courseModel.php';
require_once '../models/paymentModel.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: ../views/auth/login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_SESSION['user_id'];
    $courseId = (int)$_POST['course_id'] ?? 0;
    $amount = (float)$_POST['amount'] ?? 0;
    $paymentType = $_POST['payment_type'] ?? 'full';
    $paymentMethod = $_POST['payment_method'] ?? 'card';
    $cardNumber = $_POST['card_number'] ?? '';
    $cardHolder = $_POST['card_holder'] ?? '';
    $expiry = $_POST['expiry'] ?? '';
    $cvv = $_POST['cvv'] ?? '';
    $couponCode = $_POST['coupon_code'] ?? '';

    if ($courseId <= 0 || $amount < 0) {
        header("Location: ../views/student/dashboard.php?error=invalid_payment_details");
        exit();
    }

    $course = getCourseById($courseId);
    if (!$course) {
        header("Location: ../views/student/dashboard.php?error=course_not_found");
        exit();
    }

    // In a real application, you would process the payment through a payment gateway
    // For this demo, we'll simulate a successful payment
    
    // Handle coupon if provided
    if (!empty($couponCode)) {
        // In a real app, this would validate the coupon and apply discounts
        // For demo purposes, we'll just acknowledge the coupon
        $couponApplied = true;
    }
    
    // Determine payment method
    $paymentMethodText = ($paymentMethod === 'paypal') ? 'PayPal' : 'Credit Card';
    
    // Create payment record
    $paymentData = [
        'user_id' => $userId,
        'course_id' => $courseId,
        'amount' => $amount,
        'payment_method' => $paymentMethodText,
        'payment_status' => 'success'
    ];
    
    $paymentResult = addPayment($paymentData);
    
    if ($paymentResult) {
        // Enroll the user in the course
        $enrollmentResult = enrollInCourse($userId, $courseId);
        
        if ($enrollmentResult) {
            // Send receipt email (simulated)
            // In a real application, you would use PHPMailer or similar to send actual emails
            $email = $_SESSION['email'];
            $subject = "Enrollment Confirmation - " . $course['title'];
            $message = "Dear " . $_SESSION['full_name'] . ",\n\n";
            $message .= "Thank you for enrolling in " . $course['title'] . ".\n";
            $message .= "Your payment of $" . number_format($amount, 2) . " has been processed successfully.\n";
            $message .= "You now have access to the course materials.\n\n";
            $message .= "Best regards,\nThe CodeCraft Team";
            
            // For demo purposes, we'll just store the receipt info in session
            $_SESSION['receipt'] = [
                'course' => $course['title'],
                'amount' => $amount,
                'date' => date('Y-m-d H:i:s')
            ];
            
            header("Location: ../views/student/enrollmentConfirmation.php");
            exit();
        } else {
            header("Location: ../views/student/dashboard.php?error=enrollment_failed");
            exit();
        }
    } else {
        header("Location: ../views/student/dashboard.php?error=payment_failed");
        exit();
    }
} else {
    header("Location: ../views/student/dashboard.php?error=invalid_request");
    exit();
}
?>