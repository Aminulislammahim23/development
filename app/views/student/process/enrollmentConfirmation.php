<?php
session_start();
require_once '../../../models/courseModel.php';
require_once '../../../models/paymentModel.php';

/* ---------- HELPER FUNCTIONS ---------- */
function getAvatarPath($avatarFilename)
{
    $avatar = $avatarFilename ?? 'default.png';
    return "../../../assets/uploads/users/avatars/" . htmlspecialchars($avatar);
}

/* ---------- SECURITY CHECK ---------- */
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
    header("Location: ../auth/login.php");
    exit();
}

// Get receipt information from session
$receipt = $_SESSION['receipt'] ?? null;

if (!$receipt) {
    header("Location: ../dashboard.php");
    exit();
}

// Clear the receipt from session so it doesn't show again
unset($_SESSION['receipt']);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enrollment Confirmation | Student Dashboard</title>
    <link rel="stylesheet" href="../../../assets/css/student.css">
    
</head>
<body>
    <div class="admin-container">

        <!-- ===== SIDEBAR ===== -->
        <aside class="sidebar">
            <img src="../../../assets/img/logo.png" class="brand-logo">
            <h2 class="logo">Welcome to CodeCraft</h2>
    
            <ul class="menu">
                <li>
                    <a href="../dashboard.php">📊 Dashboard</a>
                </li>
                <li class="active">
                    <a href="../dashboard.php#courses">📚 Courses</a>
                </li>
                <li>
                    <a href="../dashboard.php#enrollments">📦 Enrollments</a>
                </li>
                <li>
                    <a href="../dashboard.php">👤 Profile</a>
                </li>
                <li>
                    <a href="../../controllers/logout.php">🚪 Logout</a>
                </li>
            </ul>
        </aside>

        <main class="main">
            <!-- TOPBAR -->
            <header class="topbar">
                <h1>Confirmation</h1>
                <div class="student-info">
                    <img src="<?= getAvatarPath($_SESSION['avatar'] ?? null); ?>" 
                         alt="<?= htmlspecialchars($_SESSION['full_name'] ?? 'User'); ?> Avatar" 
                         class="user-avatar"
                         onerror="this.onerror=null; this.src='<?= getAvatarPath('default.png'); ?>';">
                    <span><?= htmlspecialchars($_SESSION['full_name'] ?? 'student'); ?></span>
                </div>
            </header>

            <div class="confirmation-container">
                <div class="confirmation-header">
                    <div class="success-icon">✓</div>
                    <h1>Enrollment Successful!</h1>
                    <p>Congratulations! You have been successfully enrolled in the course.</p>
                </div>

                <div class="receipt-details">
                    <h3>Receipt Details</h3>
                    <p><strong>Course:</strong> <?= htmlspecialchars($receipt['course']); ?></p>
                    <p><strong>Amount Paid:</strong> $<?= number_format($receipt['amount'], 2); ?></p>
                    <p><strong>Transaction Date:</strong> <?= $receipt['date']; ?></p>
                    <p><strong>Status:</strong> <span style="color: #28a745; font-weight: bold;">Completed</span></p>
                </div>

                <p>A confirmation email has been sent to your registered email address with detailed receipt information.</p>

                <div class="btn-container">
                    <a href="../dashboard.php" class="btn btn-primary">Go to Dashboard</a>
                    <a href="../dashboard.php#enrollments" class="btn btn-secondary">View My Enrollments</a>
                    <a href="invoices.php" class="btn btn-secondary">View Invoice</a>
                </div>
                
                <a href="../dashboard.php" class="back-to-dashboard">← Back to Dashboard</a>
            </div>
        </main>
    </div>

    <script src="../../../assets/js/student.js"></script>
</body>
</html>