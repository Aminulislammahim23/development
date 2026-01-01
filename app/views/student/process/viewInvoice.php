<?php
session_start();
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

$paymentId = $_GET['payment_id'] ?? 0;

if ($paymentId <= 0) {
    header("Location: ../invoices.php?error=invalid_invoice");
    exit();
}

$invoice = generateInvoice($paymentId);

if (!$invoice || $invoice['user_id'] != $_SESSION['user_id']) {
    header("Location: ../invoices.php?error=invoice_not_found");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #<?= $invoice['id']; ?> | Student Dashboard</title>
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
                    <a href="../invoices.php">📄 Invoices</a>
                </li>
                <li>
                    <a href="../../controllers/logout.php">🚪 Logout</a>
                </li>
            </ul>
        </aside>

        <main class="main">
            <!-- TOPBAR -->
            <header class="topbar">
                <h1>Invoice</h1>
                <div class="student-info">
                    <img src="<?= getAvatarPath($_SESSION['avatar'] ?? null); ?>" 
                         alt="<?= htmlspecialchars($_SESSION['full_name'] ?? 'User'); ?> Avatar" 
                         class="user-avatar"
                         onerror="this.onerror=null; this.src='<?= getAvatarPath('default.png'); ?>';">
                    <span><?= htmlspecialchars($_SESSION['full_name'] ?? 'student'); ?></span>
                </div>
            </header>

            <div class="invoice-container">
                <div class="invoice-header">
                    <h1>INVOICE</h1>
                    <p>Invoice #: #<?= $invoice['id']; ?></p>
                    <div class="invoice-status status-paid">PAID</div>
                </div>

                <div class="invoice-details">
                    <div class="invoice-from">
                        <h3>From:</h3>
                        <p>CodeCraft Academy</p>
                        <p>123 Education Street</p>
                        <p>Learning City, LC 12345</p>
                        <p>Email: info@codecraft.com</p>
                    </div>
                    <div class="invoice-to">
                        <h3>To:</h3>
                        <p><?= htmlspecialchars($invoice['user_name']); ?></p>
                        <p><?= htmlspecialchars($invoice['user_email']); ?></p>
                    </div>
                </div>

                <div class="invoice-info">
                    <div style="display: flex; justify-content: space-between;">
                        <div>
                            <p><strong>Invoice Date:</strong> <?= date('F j, Y', strtotime($invoice['paid_at'])); ?></p>
                            <p><strong>Payment Method:</strong> <?= htmlspecialchars($invoice['payment_method']); ?></p>
                        </div>
                        <div style="text-align: right;">
                            <p><strong>Due Date:</strong> <?= date('F j, Y', strtotime($invoice['paid_at'])); ?></p>
                            <p><strong>Status:</strong> <span class="invoice-status status-paid">PAID</span></p>
                        </div>
                    </div>
                </div>

                <table class="invoice-table">
                    <thead>
                        <tr>
                            <th>Description</th>
                            <th>Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><?= htmlspecialchars($invoice['course_title']); ?></td>
                            <td>$<?= number_format($invoice['amount'], 2); ?></td>
                        </tr>
                    </tbody>
                </table>

                <div class="invoice-summary">
                    <div class="summary-row">
                        <div class="summary-label">Subtotal:</div>
                        <div class="summary-value">$<?= number_format($invoice['amount'], 2); ?></div>
                    </div>
                    <div class="summary-row">
                        <div class="summary-label">Tax (0%):</div>
                        <div class="summary-value">$0.00</div>
                    </div>
                    <div class="summary-row total-row">
                        <div class="summary-label">Total:</div>
                        <div class="summary-value">$<?= number_format($invoice['amount'], 2); ?></div>
                    </div>
                </div>

                <div class="invoice-actions">
                    <a href="downloadInvoice.php?payment_id=<?= $invoice['id']; ?>" class="invoice-btn download-btn">Download PDF</a>
                    <a href="../invoices.php" class="invoice-btn back-btn">Back to Invoices</a>
                </div>
            </div>
        </main>
    </div>

    <script src="../../../assets/js/student.js"></script>
</body>
</html>