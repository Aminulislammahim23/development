<?php
session_start();
$successMsg = "";
$errorMsg = "";

if (isset($_GET['success'])) {
    if ($_GET['success'] === 'user_updated') {
        $successMsg = "✅ User information updated successfully.";
    } elseif ($_GET['success'] === 'user_created') {
        $successMsg = "✅ User created successfully.";
    } elseif ($_GET['success'] === 'user_terminated') {
        $successMsg = "✅ User terminated successfully.";
    }
}

if (isset($_GET['error'])) {
    if ($_GET['error'] === 'user_not_found') {
        $errorMsg = "❌ User not found!";
    } elseif ($_GET['error'] === 'update_failed') {
        $errorMsg = "❌ Failed to update user. Please try again.";
    } elseif ($_GET['error'] === 'email_exists') {
        $errorMsg = "❌ Email already exists!";
    } elseif ($_GET['error'] === 'empty_fields') {
        $errorMsg = "❌ All fields are required!";
    } elseif ($_GET['error'] === 'registration_failed') {
        $errorMsg = "❌ Failed to create user. Please try again.";
    } elseif ($_GET['error'] === 'terminate_failed') {
        $errorMsg = "❌ Failed to terminate user. Please try again.";
    } elseif ($_GET['error'] === 'cannot_delete_self') {
        $errorMsg = "❌ You cannot delete your own account!";
    } elseif ($_GET['error'] === 'invalid_user_id') {
        $errorMsg = "❌ Invalid user ID!";
    }
}

require_once('../../models/userModel.php');
require_once('../../models/courseModel.php');
require_once('../../models/paymentModel.php');

/* ---------- HELPER FUNCTIONS ---------- */
function getAvatarPath($avatarFilename) {
    $avatar = $avatarFilename ?? 'default.png';
    return "../../assets/uploads/users/avatars/" . htmlspecialchars($avatar);
}

/* ---------- SECURITY CHECK ---------- */
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../view/login.php");
    exit();
}

/* ---------- DASHBOARD STATS ---------- */

// Total Users
$totalUsers = countUsers() ?? 0;
$totalCourses = countCourses() ?? 0;
$totalEnrollments = countTotalEnrollments() ?? 0;
$monthlyRevenue = getMonthlyRevenue() ?? 0;

// // Monthly Revenue

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>CodeCraft Admin Panel</title>
    <link rel="stylesheet" href="../../assets/css/admin.css">
</head>
<body>

<div class="admin-container">

    <!-- ===== SIDEBAR ===== -->
    <aside class="sidebar">
        <img src="../../assets/img/logo.png" class="brand-logo">
        <h2 class="logo">Admin</h2>

        <ul class="menu">
            <li class="active">
                <a href="#" onclick="showSection('dashboard')">📊 Dashboard</a>
            </li>
            <li>
                <a href="#" onclick="showSection('users')">👨‍🎓 Users</a>
            </li>
            <li>
                    <a href="#" onclick="showSection('courses')">📚 Courses</a>
            </li>
            <li>
                <a href="#" onclick="showSection('profile')">👤 Profile</a>
            </li>
            <li>
                <a href="#" onclick="showSection('settings')">⚙️ Settings</a>
            </li>
            <li>
                <a href="../../controllers/logout.php" onclick="showSection('logout')">🚪 Logout</a>
            </li>
        </ul>
    </aside>

    <!-- ===== MAIN ===== -->
    <main class="main">

        <!-- TOPBAR -->
        <header class="topbar">
            <h1>Dashboard</h1>
            <div class="admin-info">
                <img src="<?= getAvatarPath($_SESSION['avatar'] ?? null); ?>" 
                     alt="<?= htmlspecialchars($_SESSION['full_name'] ?? 'User'); ?> Avatar" 
                     class="user-avatar"
                     onerror="this.onerror=null; this.src='<?= getAvatarPath('default.png'); ?>';">
                <span><?= htmlspecialchars($_SESSION['full_name'] ?? 'Admin'); ?></span>
            </div>
        </header>

        <!-- Success/Error Messages -->
        <?php if ($successMsg): ?>
            <div class="alert alert-success" style="background: #d4edda; color: #155724; padding: 15px; margin: 20px; border-radius: 5px; border: 1px solid #c3e6cb;">
                <?= $successMsg; ?>
            </div>
        <?php endif; ?>
        <?php if ($errorMsg): ?>
            <div class="alert alert-error" style="background: #f8d7da; color: #721c24; padding: 15px; margin: 20px; border-radius: 5px; border: 1px solid #f5c6cb;">
                <?= $errorMsg; ?>
            </div>
        <?php endif; ?>

        <!-- ===== DASHBOARD SECTION ===== -->
        <div class="cards section" id="dashboardSection">
            <div class="card">
                <h3>Total Users</h3>
                <p><?= $totalUsers; ?></p>
            </div>

            <div class="card">
                <h3>Total Courses</h3>
                <p><?= $totalCourses; ?></p>
            </div>

            <div class="card">
                <h3>Total Enrolled Courses</h3>
                <p><?= $totalEnrollments; ?></p>
            </div>

            <div class="card">
                <h3>Monthly Revenue</h3>
                <p>৳ <?= number_format($monthlyRevenue, 2); ?></p>
            </div>
        </div>


        <!-- ===== USERS SECTION ===== -->
        <div class="table-section section" id="usersSection" style="display:none;">
            <h2>All Users</h2>
            <div class="btn-container">
                <button class="button" onclick="showSection('addusers')">👥 Add User</button>
                <button class="button" onclick="showSection('updateUsersSection')">👤 Update User Information</button>
                <button class="button" onclick="showSection('terminateUsersSection')">🚫 Terminate User</button>
            </div><br><br>

            <table>
                <thead>
                    <tr>
                        <th>Avatar</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Joined</th>
                    </tr>
                </thead>
                <!-- <tbody>
                    <tr>
                        <td>
                            <img src="../../uploads/users/avatars/" width="40" alt="Avatar">
                        </td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                </tbody> -->
            </table>
        </div>

        <!-- ===== ADD/UPDATE/TERMINATE USERS SECTION ===== -->

        <div class="table-section section" id="addUsersSection" style="display:none;">
            <h2>Add New User</h2>
            <form action="../../controllers/addCheck.php" method="POST" enctype="multipart/form-data">
                <label for="full_name">Full Name</label>
                <input type="text" class="txtStyle" id="full_name" name="full_name" required><br><br>

                <label for="email">Email</label>
                <input type="email" class="txtStyle" id="email" name="email" required><br><br>

                <label for="password">Password</label>
                <input type="password" class="txtStyle" id="password" name="password" required><br><br>

                <label for="role">Role</label>
                <select id="role" class="txtStyle" name="role" required>
                    <option value="student">Student</option>
                    <option value="instructor">Instructor</option>
                </select><br><br>

                <label for="avatar">Avatar</label>
                <input type="file" class="txtStyle" id="avatar" name="avatar" accept="image/*"><br><br>

                <button type="submit" class="button">Add User</button>
            </form>
        </div>


        <div class="table-section section" id="updateUsersSection" style="display:none;">
            <h2>Update User Information</h2>

           
            <form onsubmit="return false;">
                <label for="searchUser">Search User</label>
                <input type="text" class="txtStyle" id="searchUser" placeholder="Search by email or name"><br>
                <button type="button" class="button" id="searchBtn">Search User</button><br><br>
            </form>

            
            <form action="../../controllers/updateUser.php" method="POST" enctype="multipart/form-data">

                    <input type="hidden" id="user_id" name="user_id" value="">

                    <label for="update_full_name">Full Name</label>
                    <input type="text" id="full_name" name="full_name" class="txtStyle" required><br><br>

                    <label for="update_email">Email</label>
                    <input type="email" id="email" name="email" class="txtStyle" required><br><br>

                    <label for="update_password">Password (leave blank to keep current)</label>
                    <input type="password" id="password" name="password" class="txtStyle"><br><br>

                    <label for="update_role">Role</label>
                    <select id="role" name="role" class="txtStyle">
                        <option value="student">Student</option>
                        <option value="instructor">Instructor</option>
                    </select><br><br>

                    <label for="update_avatar">Avatar</label>
                    <input type="file" id="avatar" class="txtStyle" name="avatar" accept="image/*"><br><br>

                    <button type="submit" class="button">Update User</button>
            </form> 
        </div>


        <div class="table-section section" id="terminateUsersSection" style="display:none;">
            <h2>Terminate User</h2>
            
            <!-- Search User First -->
            <form onsubmit="return false;">
                <label for="searchUserTerminate">Search User</label>
                <input type="text" id="searchUserTerminate" class="txtStyle" placeholder="Search by email, name, or user ID"><br>
                <button type="button" class="button" id="searchTerminateBtn">Search User</button><br><br>
            </form>

            <!-- Terminate Form -->
            <form action="../../controllers/terminateUser.php" method="POST" onsubmit="return confirm('Are you sure you want to terminate this user? This action cannot be undone!');">
                <input type="hidden" id="terminate_user_id" name="user_id" value="">
                
                <label for="terminate_full_name">Full Name</label>
                <input type="text" class="txtStyle" id="terminate_full_name" name="full_name" readonly><br><br>

                <label for="terminate_email">Email</label>
                <input type="email" class="txtStyle" id="terminate_email" name="email" readonly><br><br>

                <label for="terminate_role">Role</label>
                <input type="text" class="txtStyle" id="terminate_role" name="role" readonly><br><br>

                <button type="submit" class="button" style="background: #dc3545;">Terminate User</button>
            </form>
        </div>

        <div class="table-section section" id="profileSection" style="display:none;">
            <h2>My Profile</h2>
            <div class="profile-container">
                <div class="profile-avatar">
                    <img src="<?= getAvatarPath($_SESSION['avatar'] ?? null); ?>" 
                         alt="<?= htmlspecialchars($_SESSION['full_name'] ?? 'User'); ?> Avatar" 
                         class="profile-img"
                         onerror="this.onerror=null; this.src='<?= getAvatarPath('default.png'); ?>';">
                </div>
                <div class="profile-info">
                    <p><strong>Full Name:</strong> <?= htmlspecialchars($_SESSION['full_name'] ?? 'N/A'); ?></p>
                    <p><strong>Email:</strong> <?= htmlspecialchars($_SESSION['email'] ?? 'N/A'); ?></p>
                    <p><strong>Role:</strong> <?= htmlspecialchars(ucfirst($_SESSION['role'] ?? 'N/A')); ?></p>
                    <p><strong>User ID:</strong> <?= htmlspecialchars($_SESSION['user_id'] ?? 'N/A'); ?></p>
                </div>
            </div>
        </div>


        <!-- ===== COURSES SECTION ===== -->
        <div class="table-section section" id="courseSection">
                <h2>My Courses</h2>
                <div class="btn-container">
                    <button class="button" onclick="showSection('addcourses')">👥 Add Course</button>
                    <button class="button" onclick="showSection('updatecoursesSection')">👤 Update Course Information</button>
                    <button class="button" onclick="showSection('deletecoursesSection')">🚫 Delete Course</button>
                </div><br><br>
                <table border="1" cellpadding="10">
                    <tr>
                        <th>ID</th>
                        <th>Category</th>
                        <th>Title</th>
                        <th>Difficulty</th>
                        <th>Duration</th>
                        <th>Price</th>
                        <th>Rating</th>
                    </tr>
                
                <?php
                $courses = getAllCourses();
                if (!empty($courses)):
                    foreach ($courses as $course):
                ?>
                <?php
                $categories = getCategories();
                foreach ($categories as $category):
                    if ($category['id'] == $course['category_id']):
                        $category_name = $category['name'];
                    endif;
                endforeach;
                ?>
                <tr>
                    <td><?= htmlspecialchars($course['id']); ?></td>
                    <td><?= htmlspecialchars($category_name); ?></td>
                    <td><?= htmlspecialchars($course['title']); ?></td>
                    <td><?= htmlspecialchars($course['difficulty']); ?></td>
                    <td><?= htmlspecialchars($course['duration']); ?></td>
                    <td><?= htmlspecialchars($course['price']); ?> $</td>
                    <td><?= htmlspecialchars($course['rating']); ?></td>
                </tr>
                <?php
                    endforeach;
                else:
                ?>
                <tr>
                    <td colspan="6">No courses found.</td>
                </tr>
                <?php endif; ?>
                </table>                
            </div>


            <!-- ===== SETTINGS SECTION ===== -->

        <div id="settingsSection" class="section" style="display:none;">
            <h2>Settings</h2>
            <form action="../controller/updateSettings.php" method="POST">
                <label for="site_name">Site Name</label>
                <input type="text" class="txtStyle" id="site_name" name="site_name" required>

                <button type="submit" class="button" value="save">Save</button>
                <button type="submit" class="button" value="exit">Exit</button>
            </form>
        </div>

    </main>
</div>

<script src="../../assets/js/admin.js"></script>
<script>
    // Auto-hide success/error messages after 4 seconds
    window.addEventListener('DOMContentLoaded', function() {
        const alerts = document.querySelectorAll('.alert');
        if (alerts.length > 0) {
            setTimeout(function() {
                alerts.forEach(function(alert) {
                    alert.style.transition = 'opacity 0.5s ease';
                    alert.style.opacity = '0';
                    setTimeout(function() {
                        alert.style.display = 'none';
                    }, 500);
                });
            }, 4000);
        }
    });
</script>
</body>
</html>
