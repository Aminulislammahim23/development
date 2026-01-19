<?php
/**
 * Basic Admin Controller
 * Simple controller for admin operations
 */
class AdminController {
    
    public function dashboard() {
        // Load admin dashboard view
        require_once '../views/admin/dashboard.php';
    }
    
    public function searchUsers() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require_once '../models/userModel.php';
            $searchTerm = trim($_POST['search_term'] ?? '');
            
            if (!empty($searchTerm)) {
                $user = searchUser($searchTerm);
                if ($user) {
                    echo json_encode([
                        'success' => true,
                        'user' => $user
                    ]);
                } else {
                    echo json_encode([
                        'success' => false,
                        'message' => 'User not found'
                    ]);
                }
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Search term is required'
                ]);
            }
        } else {
            header("Location: ../views/admin/dashboard.php?error=invalid_request");
        }
    }
    
    public function updateUser() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require_once '../models/userModel.php';
            $userId = (int)($_POST['user_id'] ?? 0);
            $fullName = trim($_POST['full_name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $role = trim($_POST['role'] ?? '');
            
            if ($userId && $fullName && $email && $role) {
                $userData = [
                    'id' => $userId,
                    'full_name' => $fullName,
                    'email' => $email,
                    'role' => $role
                ];
                
                $result = updateUser($userData);
                
                if ($result && $result !== "NOT_FOUND") {
                    header("Location: ../views/admin/dashboard.php?success=user_updated");
                } else {
                    header("Location: ../views/admin/dashboard.php?error=user_update_failed");
                }
            } else {
                header("Location: ../views/admin/dashboard.php?error=missing_required_fields");
            }
        } else {
            header("Location: ../views/admin/dashboard.php?error=invalid_request");
        }
    }
    
    public function terminateUser() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require_once '../models/userModel.php';
            $userId = (int)($_POST['user_id'] ?? 0);
            
            if ($userId) {
                $result = deleteUser($userId);
                
                if ($result && $result !== "NOT_FOUND") {
                    header("Location: ../views/admin/dashboard.php?success=user_terminated");
                } else {
                    header("Location: ../views/admin/dashboard.php?error=user_termination_failed");
                }
            } else {
                header("Location: ../views/admin/dashboard.php?error=missing_user_id");
            }
        } else {
            header("Location: ../views/admin/dashboard.php?error=invalid_request");
        }
    }
}