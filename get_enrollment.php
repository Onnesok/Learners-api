<?php
include 'dbconnection.php'; 

// Establish database connection
$con = dbconnection();

// Check if email parameter is set
if (!isset($_GET['email']) || empty($_GET['email'])) {
    http_response_code(400); // Bad Request
    echo json_encode(['error' => 'Email parameter is required']);
    exit();
}

$email = $_GET['email'];

// Prepare the query to fetch course_ids based on user email
$sql = "SELECT course_id FROM enrollment WHERE uemail = ?";
$stmt = $con->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    http_response_code(404); // Not Found
    echo json_encode(['error' => 'No enrollment found for the provided email']);
    exit();
}

// Fetch all course_ids
$course_ids = [];
while ($row = $result->fetch_assoc()) {
    $course_ids[] = $row['course_id'];
}

// Prepare the query to fetch course details based on course_ids
$placeholders = implode(',', array_fill(0, count($course_ids), '?'));
$sql = "SELECT * FROM courses WHERE course_id IN ($placeholders)";
$stmt = $con->prepare($sql);
$stmt->bind_param(str_repeat('i', count($course_ids)), ...$course_ids);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    http_response_code(404); // Not Found
    echo json_encode(['error' => 'No courses found']);
    exit();
}

// Fetch all course details
$courses = [];
while ($row = $result->fetch_assoc()) {
    $courses[] = $row;
}

// Return course details as JSON
header('Content-Type: application/json');
echo json_encode($courses);

// Close the connection
$stmt->close();
$con->close();
?>
