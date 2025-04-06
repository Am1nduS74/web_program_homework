<?php
header("Content-Type: application/json"); // Set the content type to JSON
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: POST, GET");


require __DIR__ . '/../backend/utils/tools.php'; // Include the tools file for utility functions
require __DIR__ . '/../backend/utils/db.php'; // Include the database connection file
require __DIR__ . '/../backend/api/user.php'; // Include the user API file for user-related functions
require __DIR__ . '/../backend/api/courses.php'; // Include the courses API file for course-related functions


$routes = [ // Define the routes for the API
    'GET /api/user/info' => authentication('handleUserInfo'),    
    'POST /api/user/login' => 'handleLogin', // User login
    'POST /api/user/register' => 'handleRegister', // User registration
    'GET /api/courses/list' => 'listAllCourses', // List all courses
    'POST /api/purchase/add' => authentication('purchaseAdd'), // Add a course to the user's purchase list
    'POST /api/purchase/remove' => authentication('purchaseRemove'), // Remove a course from the user's purchase list
    'POST /api/purchase/validate' => 'purchaseValidate', // Validate if a course is purchased
];


$requestMethod = $_SERVER['REQUEST_METHOD']; 
$requestUri = $_SERVER['REQUEST_URI'];
$routeKey = "$requestMethod $requestUri"; // Create a key for the route based on the request method and URI

if ($requestUri === '/' || $requestUri === '/index.php') { // If the request is for the root or index page, redirect to home.php
    include 'home.php';
    exit;
}

if (isset($routes[$routeKey])) { // Check if the route exists in the defined routes
    $handler = $routes[$routeKey];
    if (is_callable($handler)) {
        call_user_func($handler);
    } else {
        call_user_func_array(explode('@', $handler), []); // Call the handler function
    }
} else {
    http_response_code(404);
    echo json_encode(['error' => 'Not Found']);
}
