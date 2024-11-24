<?php
// Include the database connection file
include 'db_connect.php'; // Ensure this file properly sets $connextion

// Check if `unique_id` is provided via POST
if (isset($_POST['unique_id'])) {
    $unique_id = $_POST['unique_id'];

    // Prepare the SQL statement to increment the `active` field
    $sql = "UPDATE your_table_name SET active = active + 1 WHERE unique_id = ?";
    $stmt = $connextion->prepare($sql);

    if ($stmt) {
        // Bind the `unique_id` parameter to the prepared statement
        $stmt->bind_param('s', $unique_id);

        // Execute the statement
        if ($stmt->execute()) {
            echo json_encode(["success" => true, "message" => "Active field updated successfully"]);
        } else {
            echo json_encode(["success" => false, "message" => "Failed to update the active field"]);
        }

        // Close the statement
        $stmt->close();
    } else {
        echo json_encode(["success" => false, "message" => "Failed to prepare the SQL statement"]);
    }
} else {
    echo json_encode(["success" => false, "message" => "unique_id not provided"]);
}

// Close the database connection
$connextion->close();
?>
