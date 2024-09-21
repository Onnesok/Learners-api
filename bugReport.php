<?php
// Include the database connection file
include 'dbconnection.php';

// Get the connection by calling the dbconnection function
$conn = dbconnection();

// Ensure that the connection is established
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Set the response header
header('Content-Type: application/json');

// Initialize an empty response array
$response = array();

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the posted data
    $uemail = isset($_POST['uemail']) ? $_POST['uemail'] : '';
    $issue_description = isset($_POST['issue_description']) ? $_POST['issue_description'] : '';

    // Validate input
    if (empty($uemail) || empty($issue_description)) {
        $response['status'] = 'error';
        $response['message'] = 'Email and issue description are required.';
    } else {
        // Prepare the SQL statement
        $stmt = $conn->prepare("INSERT INTO bug_report (uemail, issue_description) VALUES (?, ?)");
        $stmt->bind_param("ss", $uemail, $issue_description);

        // Execute the statement
        if ($stmt->execute()) {
            $response['status'] = 'success';
            $response['message'] = 'Bug report submitted successfully.';
        } else {
            $response['status'] = 'error';
            $response['message'] = 'Failed to submit bug report: ' . $stmt->error;
        }

        // Close the statement
        $stmt->close();
    }
} else {
    $response['status'] = 'error';
    $response['message'] = 'Invalid request method.';
}

// Close the database connection
$conn->close();

// Return the response as JSON
echo json_encode($response);
?>
