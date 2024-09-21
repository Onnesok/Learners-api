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

// Query to fetch up to 8 popular courses per category based on rating
$sql = "
    SELECT 
        co.category_id,
        cat.title AS category_title,
        cat.image AS category_image,
        co.course_id,
        co.title AS title,
        co.instructor_name,
        co.duration,
        co.price,
        co.release_date,
        co.video_content,         
        co.description,     
        co.video_title, 
        co.prerequisite,
        co.rating_count,
        co.certificate,
        co.intro_video,   
        co.image AS image,
        co.stars,
        co.discount
    FROM 
        courses co
    JOIN 
        categories cat ON co.category_id = cat.category_id
    JOIN (
        SELECT 
            category_id,
            course_id,
            stars,
            ROW_NUMBER() OVER (PARTITION BY category_id ORDER BY stars DESC) AS rank
        FROM 
            courses
    ) ranked_courses ON co.course_id = ranked_courses.course_id
    WHERE 
        ranked_courses.rank <= 3
    ORDER BY 
        co.category_id, co.stars DESC
";

// Execute query
$result = $conn->query($sql);

if (!$result) {
    echo json_encode(['error' => 'Error executing query: ' . $conn->error]);
    $conn->close();
    exit();
}

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
