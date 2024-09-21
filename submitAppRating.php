<?php
include 'dbconnection.php'; 

// Establish database connection
$conn = dbconnection();

// Check connection
if ($conn->connect_error) {
    die(json_encode(['status' => 'error', 'message' => 'Database connection failed.']));
}

// Get data from POST request
$uemail = isset($_POST['uemail']) ? trim($_POST['uemail']) : '';
$app_rating = isset($_POST['app_rating']) ? (int)$_POST['app_rating'] : 0;

// Debugging output
error_log("Received uemail: $uemail, app_rating: $app_rating");

$response = [];

// Validate input
if (!empty($uemail) && filter_var($uemail, FILTER_VALIDATE_EMAIL) && $app_rating >= 1 && $app_rating <= 5) {
    // Check if the user has already rated
    $stmt = $conn->prepare("SELECT * FROM app_rating_db WHERE uemail = ?");
    $stmt->bind_param("s", $uemail);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $response['status'] = 'error';
        $response['message'] = 'You have already rated the app.';
    } else {
        // Insert the new rating
        $stmt = $conn->prepare("INSERT INTO app_rating_db (uemail, app_rating) VALUES (?, ?)");
        $stmt->bind_param("si", $uemail, $app_rating);

        if ($stmt->execute()) {
            $response['status'] = 'success';
            $response['message'] = 'Rating submitted successfully.';
        } else {
            $response['status'] = 'error';
            $response['message'] = 'Error submitting rating. Please try again later.';
        }
    }

    $stmt->close();
} else {
    $response['status'] = 'error';
    $response['message'] = 'Invalid input. Please provide a valid rating.';
}

$conn->close();
echo json_encode($response);
?>
