<?php
/**
 * Simple MVC Router
 * Handles basic routing for the application
 */

// Start session for authentication
session_start();

// Get the requested path
$request = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$request = trim($request, '/');

// Default route
if (empty($request)) {
    $request = 'home';
}

// Split the request into parts
$parts = explode('/', $request);

// Determine controller and action
$controllerName = isset($parts[0]) ? $parts[0] : 'home';
$action = isset($parts[1]) ? $parts[1] : 'index';

// Map controller names to file names
$controllerMap = [
    'admin' => 'admin_controller',
    'instructor' => 'instructor_controller',
    'student' => 'student_controller'
];

// Get the actual controller file name
$controllerFile = isset($controllerMap[$controllerName]) 
    ? $controllerMap[$controllerName] 
    : $controllerName;

// Include the controller file
$controllerPath = "controllers/{$controllerFile}.php";

if (file_exists($controllerPath)) {
    require_once $controllerPath;
    
    // Create controller class name
    $className = ucfirst($controllerName) . 'Controller';
    
    if (class_exists($className)) {
        $controller = new $className();
        
        // Call the action method if it exists
        if (method_exists($controller, $action)) {
            $controller->$action();
        } else {
            // Action not found
            http_response_code(404);
            echo "Action not found: {$action}";
        }
    } else {
        // Controller class not found
        http_response_code(404);
        echo "Controller class not found: {$className}";
    }
} else {
    // Controller file not found
    http_response_code(404);
    echo "Controller file not found: {$controllerPath}";
}