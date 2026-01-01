<?php
session_start();
require_once '../../models/courseModel.php';
require_once '../../models/userModel.php';

/* ---------- HELPER FUNCTIONS ---------- */
function getAvatarPath($avatarFilename)
{
    $avatar = $avatarFilename ?? 'default.png';
    return "../../assets/uploads/users/avatars/" . htmlspecialchars($avatar);
}

/* ---------- SECURITY CHECK ---------- */
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'instructor') {
    header("Location: ../auth/login.php");
    exit();
}




/* ---------- DASHBOARD STATS ---------- */
$totalCourses = countCourses();
// $totalEnrollments = countEnrollments();
// $totalStudents = countStudents();
// $totalRevenue = countRevenue();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instructor Dashboard</title>
    <link rel="stylesheet" href="../../assets/css/instructor.css">
</head>
<body>
    <div class="admin-container">

        <!-- ===== SIDEBAR ===== -->
        <aside class="sidebar">
            <img src="../../assets/img/logo.png" class="brand-logo">
            <h2 class="logo">Instructor</h2>
    
            <ul class="menu">
                <li class="active">
                    <a href="#" onclick="showSection('dashboard')">📊 Dashboard</a>
                </li>
                <li>
                    <a href="#" onclick="showSection('courses')">📚 Courses</a>
                </li>
                <li>
                    <a href="#" onclick="showSection('enrollments')">📦 Enrollments</a>
                </li>
                <li>
                    <a href="#" onclick="showSection('settings')">⚙️ Settings</a>
                </li>
                <li>
                    <a href="../../controllers/logout.php" onclick="showSection('Logout')">🚪 Logout</a>
                </li>
            </ul>
        </aside>

        <main class="main">

            <!-- TOPBAR -->
            <header class="topbar">
                <h1>Dashboard</h1>
                <div class="admin-info">
                    <img src="<?= getAvatarPath($_SESSION['avatar'] ?? null); ?>" 
                         alt="<?= htmlspecialchars($_SESSION['full_name'] ?? 'User'); ?> Avatar" 
                         class="user-avatar"
                         onerror="this.onerror=null; this.src='<?= getAvatarPath('default.png'); ?>';">
                    <span><?= htmlspecialchars($_SESSION['full_name'] ?? 'Instructor'); ?></span>
                </div>
            </header>


            <!-- ===== DASHBOARD SECTION ===== -->
            <div class="cards section" id="dashboardSection">
                <div class="card">
                    <h3>My Total Courses</h3>
                    <!-- <p><?= $totalCourses; ?></p> -->
                </div>
            <div class="card">
                <h3>My Total Enrollments</h3>
                <!-- <p><?= $totalEnrollments; ?></p> -->
            </div>
            <div class="card">
                <h3>My Total Students</h3>
                <!-- <p><?= $totalStudents; ?></p> -->
            </div>
            <div class="card">
                <h3>My Total Revenue</h3>
                <!-- <p><?= $totalRevenue; ?></p> -->
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

            <!-- ===== ENROLLMENTS SECTION ===== -->
            <div class="table-section section" id="enrollmentsSection">
                <h2>My Enrollments</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Enrollment Date</th>
                            <th>Enrollment Status</th>
                            <th>Enrollment Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Enrollment 1</td>
                            <td>Enrollment 1 Status</td>
                            <td>Enrollment 1 Actions</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- ===== STUDENTS SECTION ===== -->
            <div class="table-section section" id="studentsSection">
                <h2>My Students</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Student Name</th>
                            <th>Student Email</th>
                            <th>Student Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Student 1</td>
                            <td>Student 1 Email</td>
                            <td>Student 1 Actions</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- ===== ADD COURSES SECTION ===== -->
            <div class="table-section section" id="addcoursesSection">
                <h2>Add Course</h2>
                <form action="../../controllers/courseController/addCourse.php" method="POST">
                    <label for="title">Title</label>
                    <input type="text" class="txtStyle" id="title" name="title" required>

                    <label for="difficulty">Difficulty</label>
                    <input type="text" class="txtStyle" id="difficulty" name="difficulty" required>

                    <label for="duration">Duration</label>
                    <input type="text" class="txtStyle" id="duration" name="duration" required>

                    <label for="price">Price</label>
                    <input type="number" step="0.01" class="txtStyle" id="price" name="price" required>

                    <label for="rating">Rating</label>
                    <input type="number" step="0.1" min="0" max="5" class="txtStyle" id="rating" name="rating" required>

                    <button type="submit" class="button">Add Course</button>
                </form>
            </div>

            <!-- ===== LOGOUT SECTION ===== -->
            <div class="table-section section" id="logoutSection">
                <h2>Logout</h2>
                <button class="button" onclick="logoutInstructor()">Logout</button>
            </div>
        </main>
    </div>
    <script src="../../assets/js/instructor.js"></script>
</body>
</html>