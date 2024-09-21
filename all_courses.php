<?php

header('Content-Type: application/json');

// Database connection details
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "learners";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Query to fetch courses with their details
$sql = "
SELECT 
    c.course_id,
    c.title AS title,
    c.instructor_name,
    c.duration,
    c.price,
    c.release_date,
    c.video_content,          -- Updated: Renamed from content to video_content
    c.description,           -- New: Added description field
    c.video_title,           -- New: Added video_title field
    c.prerequisite,
    c.rating_count,
    c.certificate,
    c.intro_video,           -- Updated: Intro video now stores video ID
    c.image AS image,
    c.stars,
    c.discount,
    cat.category_id,
    cat.title AS category_title,
    cat.image AS category_image,
    cat.description AS category_description
FROM 
    courses c
INNER JOIN 
    categories cat
ON 
    c.category_id = cat.category_id;
";

// Execute query
$result = $conn->query($sql);

$popular_courses = array();

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $popular_courses[] = $row;
    }
}

// Output the data as JSON
echo json_encode($popular_courses);

// Close connection
$conn->close();

?>
