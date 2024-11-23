<?php
// Include the database $connextion file
include 'db_connect.php';

// Check if the $connextion is established
if ($connextion->connect_error) {
    die(json_encode([
        'status' => 'error',
        'message' => 'Database connection failed: ' . $connextion->connect_error
    ]));
}

try {
    // SQL query to fetch all rows from the 'floorplans' table
    $query = "SELECT `unique_id`, `name`, `image_path`, `created_at` FROM `floorplans` WHERE 1";
    $result = $connextion->query($query);

    // Check if the query was successful
    if ($result) {
        $floorplans = [];

        // Fetch all rows and store them in an array
        while ($row = $result->fetch_assoc()) {
            $floorplans[] = $row;
        }

        // Output the JSON-encoded data
        echo json_encode([
            'status' => 'success',
            'data' => $floorplans
        ]);
    } else {
        // Handle query failure
        echo json_encode([
            'status' => 'error',
            'message' => 'Query failed: ' . $connextion->error
        ]);
    }
} catch (Exception $e) {
    // Handle any other errors
    echo json_encode([
        'status' => 'error',
        'message' => 'An unexpected error occurred: ' . $e->getMessage()
    ]);
}

// Close the database $connextion
$connextion->close();
?>
