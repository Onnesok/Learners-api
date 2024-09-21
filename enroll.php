<?php
include 'dbconnection.php';

// Establish database connection
$con = dbconnection();

// Get the JSON input
$data = json_decode(file_get_contents('php://input'), true);

// Check if the required fields are present
if (!isset($data['uemail']) || empty($data['uemail']) || !isset($data['course_id']) || empty($data['course_id'])) {
    http_response_code(400); // Bad Request
    echo json_encode(['error' => 'Required fields are missing']);
    exit();
}

$uemail = filter_var($data['uemail'], FILTER_SANITIZE_EMAIL);
$course_id = intval($data['course_id']);

// Validate the email format
if (!filter_var($uemail, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400); // Bad Request
    echo json_encode(['error' => 'Invalid email format']);
    exit();
}

// Check if the user is already enrolled in the course
$check_sql = "SELECT * FROM enrollment WHERE uemail = ? AND course_id = ?";
$check_stmt = $con->prepare($check_sql);
$check_stmt->bind_param("si", $uemail, $course_id);
$check_stmt->execute();
$check_result = $check_stmt->get_result();

if ($check_result->num_rows > 0) {
    // User is already enrolled in this course
    http_response_code(409); // Conflict
    echo json_encode(['error' => 'You are already enrolled in this course']);
    $check_stmt->close();
    $con->close();
    exit();
}

$check_stmt->close();

// Prepare the query to insert new enrollment data
$sql = "INSERT INTO enrollment (uemail, course_id) VALUES (?, ?)";
$stmt = $con->prepare($sql);
$stmt->bind_param("si", $uemail, $course_id);

if ($stmt->execute()) {
    // Success
    http_response_code(201); // Created
    echo json_encode(['message' => 'Enrollment added successfully']);
} else {
    // Error
    http_response_code(500); // Internal Server Error
    echo json_encode(['error' => 'Failed to add enrollment']);
}

// Close the connection
$stmt->close();
$con->close();
?>
