<?php
session_start();
require_once '../../../models/paymentModel.php';
require_once '../../../models/courseModel.php';

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

$userId = $_SESSION['user_id'];
$payments = getPaymentsByUser($userId);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Invoices | Student Dashboard</title>
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
                <li>
                    <a href="../dashboard.php#courses">📚 Courses</a>
                </li>
                <li>
                    <a href="../dashboard.php#enrollments">📦 Enrollments</a>
                </li>
                <li class="active">
                    <a href="#">📄 Invoices</a>
                </li>
                <li>
                    <a href="../../controllers/logout.php">🚪 Logout</a>
                </li>
            </ul>
        </aside>

        <main class="main">
            <!-- TOPBAR -->
            <header class="topbar">
                <h1>Invoices</h1>
                <div class="student-info">
                    <img src="<?= getAvatarPath($_SESSION['avatar'] ?? null); ?>" 
                         alt="<?= htmlspecialchars($_SESSION['full_name'] ?? 'User'); ?> Avatar" 
                         class="user-avatar"
                         onerror="this.onerror=null; this.src='<?= getAvatarPath('default.png'); ?>';">
                    <span><?= htmlspecialchars($_SESSION['full_name'] ?? 'student'); ?></span>
                </div>
            </header>

            <div class="invoices-container">
                <div class="invoices-header">
                    <h1>My Payment Invoices</h1>
                    <p>View and download your payment receipts</p>
                </div>

                <div class="invoices-list">
                    <?php if (!empty($payments)): ?>
                        <?php foreach ($payments as $payment): ?>
                            <div class="invoice-card">
                                <div class="invoice-info">
                                    <h3><?= htmlspecialchars($payment['course_title']); ?></h3>
                                    <p>Payment ID: #<?= $payment['id']; ?></p>
                                    <p>Date: <?= date('M j, Y', strtotime($payment['paid_at'])); ?></p>
                                    <p class="invoice-status status-success">Paid: $<?= number_format($payment['amount'], 2); ?></p>
                                </div>
                                <div class="invoice-actions">
                                    <a href="viewInvoice.php?payment_id=<?= $payment['id']; ?>" class="invoice-btn view-btn">View Invoice</a>
                                    <a href="downloadInvoice.php?payment_id=<?= $payment['id']; ?>" class="invoice-btn download-btn">Download</a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="no-invoices">
                            <h3>No invoices yet</h3>
                            <p>You haven't made any payments yet. Once you enroll in a paid course, your invoices will appear here.</p>
                        </div>
                    <?php endif; ?>
                </div>
                
                <a href="../dashboard.php" class="back-to-dashboard">← Back to Dashboard</a>
            </div>
        </main>
    </div>

    <script src="../../../assets/js/student.js"></script>
</body>
</html>