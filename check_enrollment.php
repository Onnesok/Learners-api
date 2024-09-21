<?php
include 'dbconnection.php'; 

// Establish database connection
$con = dbconnection();

// Check if email and courseId parameters are set
if (!isset($_GET['email']) || empty($_GET['email']) || !isset($_GET['courseId']) || empty($_GET['courseId'])) {
    http_response_code(400); // Bad Request
    echo json_encode(['error' => 'Email and courseId parameters are required']);
    exit();
}

$email = $_GET['email'];
$courseId = $_GET['courseId'];

// Prepare the query to check enrollment based on user email and courseId
$sql = "SELECT COUNT(*) as count FROM enrollment WHERE uemail = ? AND course_id = ?";
$stmt = $con->prepare($sql);
$stmt->bind_param("si", $email, $courseId); // 's' for email (string), 'i' for course_id (integer)
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

// Check if the user is enrolled in the specified course
if ($row['count'] > 0) {
    echo json_encode(['enrolled' => 'yes']);
} else {
    echo json_encode(['enrolled' => 'no']);
}

// Close the connection
$stmt->close();
$con->close();
?>
