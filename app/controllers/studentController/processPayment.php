<?php
session_start();
require_once '../../models/courseModel.php';
require_once '../../models/paymentModel.php';

/* ---------- HELPER FUNCTIONS ---------- */
function getAvatarPath($avatarFilename)
{
    $avatar = $avatarFilename ?? 'default.png';
    return "../../assets/uploads/users/avatars/" . htmlspecialchars($avatar);
}

/* ---------- SECURITY CHECK ---------- */
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
    header("Location: ../auth/login.php");
    exit();
}

$courseId = $_POST['course_id'] ?? 0;
$paymentType = $_POST['payment_type'] ?? 'full';

if ($courseId <= 0) {
    header("Location: ../dashboard.php?error=invalid_course");
    exit();
}

$course = getCourseById($courseId);

if (!$course) {
    header("Location: ../dashboard.php?error=course_not_found");
    exit();
}

// For demo purposes, we'll simulate a payment gateway
// In a real application, this would connect to Stripe, PayPal, etc.

$paymentAmount = $course['price'];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Gateway | Student Dashboard</title>
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
                <h1>Payment Gateway</h1>
                <div class="student-info">
                    <img src="<?= getAvatarPath($_SESSION['avatar'] ?? null); ?>" 
                         alt="<?= htmlspecialchars($_SESSION['full_name'] ?? 'User'); ?> Avatar" 
                         class="user-avatar"
                         onerror="this.onerror=null; this.src='<?= getAvatarPath('default.png'); ?>';">
                    <span><?= htmlspecialchars($_SESSION['full_name'] ?? 'student'); ?></span>
                </div>
            </header>

            <div class="payment-container">
                <div class="payment-header">
                    <h1>Secure Payment</h1>
                    <p>Complete your enrollment in <?= htmlspecialchars($course['title']); ?></p>
                </div>

                <div class="payment-summary">
                    <h3>Payment Summary</h3>
                    <p><strong>Course:</strong> <?= htmlspecialchars($course['title']); ?></p>
                    <p><strong>Amount:</strong> $<?= number_format($paymentAmount, 2); ?></p>
                    <p><strong>Payment Type:</strong> <?= ucfirst($paymentType); ?></p>
                </div>

                <form id="payment-form" class="payment-form" method="POST" action="../../controllers/processPayment.php">
                    <div class="payment-methods">
                        <h3>Select Payment Method</h3>
                        <div class="payment-options">
                            <label class="payment-option">
                                <input type="radio" name="payment_method" value="card" checked>
                                <span class="payment-label">💳 Credit/Debit Card</span>
                            </label>
                            <label class="payment-option">
                                <input type="radio" name="payment_method" value="paypal">
                                <span class="payment-label">📘 PayPal</span>
                            </label>
                        </div>
                    </div>
                    
                    <div id="card-details">
                        <div class="form-group">
                            <label for="card-number">Card Number</label>
                            <input type="text" id="card-number" name="card_number" placeholder="1234 5678 9012 3456" required>
                        </div>

                        <div class="form-group">
                            <label for="card-holder">Card Holder Name</label>
                            <input type="text" id="card-holder" name="card_holder" placeholder="John Doe" required>
                        </div>

                        <div class="form-group">
                            <label for="expiry">Expiry Date</label>
                            <input type="text" id="expiry" name="expiry" placeholder="MM/YY" required>
                        </div>

                        <div class="form-group">
                            <label for="cvv">CVV</label>
                            <input type="text" id="cvv" name="cvv" placeholder="123" required>
                        </div>
                    </div>
                    
                    <div id="paypal-details" style="display:none;">
                        <div class="paypal-info">
                            <p>You will be redirected to PayPal to complete your payment securely.</p>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="coupon">Coupon Code (Optional)</label>
                        <input type="text" id="coupon" name="coupon_code" placeholder="Enter coupon code">
                        <button type="button" id="apply-coupon" class="enroll-btn" style="margin-top: 10px; width: auto;">Apply Coupon</button>
                    </div>

                    <input type="hidden" name="course_id" value="<?= $course['id']; ?>">
                    <input type="hidden" name="amount" value="<?= $paymentAmount; ?>">
                    <input type="hidden" name="payment_type" value="<?= $paymentType; ?>">

                    <button type="submit" class="enroll-btn">Complete Payment - $<?= number_format($paymentAmount, 2); ?></button>
                </form>
                
                <a href="../dashboard.php" class="back-to-dashboard">← Back to Dashboard</a>
            </div>

            <div id="payment-confirmation" class="payment-confirmation">
                <div class="success-message">✓</div>
                <h2>Payment Successful!</h2>
                <p>Your enrollment in <?= htmlspecialchars($course['title']); ?> is now complete.</p>
                <p>A receipt has been sent to your email address.</p>
                <a href="../dashboard.php" class="enroll-btn" style="display: inline-block; margin-top: 20px;">Go to Dashboard</a>
            </div>
        </main>
    </div>

    <script>
        // Payment method selection
        document.querySelectorAll('input[name="payment_method"]').forEach(radio => {
            radio.addEventListener('change', function() {
                document.querySelectorAll('.payment-option').forEach(option => {
                    option.classList.remove('active');
                });
                
                if (this.checked) {
                    this.closest('.payment-option').classList.add('active');
                }
                
                if (this.value === 'card') {
                    document.getElementById('card-details').style.display = 'block';
                    document.getElementById('paypal-details').style.display = 'none';
                } else if (this.value === 'paypal') {
                    document.getElementById('card-details').style.display = 'none';
                    document.getElementById('paypal-details').style.display = 'block';
                }
            });
        });
        
        // Apply coupon button
        document.getElementById('apply-coupon').addEventListener('click', function() {
            const couponCode = document.getElementById('coupon').value;
            if (couponCode.trim() !== '') {
                alert('Coupon code applied: ' + couponCode);
                // In a real app, this would validate the coupon and update the price
            }
        });
        
        // Form submission with 3D Secure simulation
        document.getElementById('payment-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const paymentMethod = document.querySelector('input[name="payment_method"]:checked').value;
            
            if (paymentMethod === 'card') {
                // Simulate 3D Secure authentication
                alert('Initiating 3D Secure authentication...');
                
                // In a real app, this would redirect to 3D Secure authentication
                // For demo purposes, we'll simulate the process
                setTimeout(() => {
                    document.querySelector('.payment-container').style.display = 'none';
                    document.getElementById('payment-confirmation').style.display = 'block';
                }, 1500);
            } else {
                // For PayPal, redirect to PayPal
                alert('Redirecting to PayPal for payment...');
                document.querySelector('.payment-container').style.display = 'none';
                document.getElementById('payment-confirmation').style.display = 'block';
            }
        });
    </script>
</body>
</html>