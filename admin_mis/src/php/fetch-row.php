<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, x-requested-with");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}

// Include database connection file
include 'db_conn.php';

// Get form type and row ID from URL query parameters
$formType = isset($_GET['type']) ? $_GET['type'] : null;
$rowId = isset($_GET['id']) ? intval($_GET['id']) : null;

if ($formType && $rowId) {
    // Determine the table to query
    $table = ($formType === 'donation') ? 'donation_form' : (($formType === 'lending') ? 'lending_form' : null);

    if ($table) {
        // Prepare and execute the SELECT query
        $query = "SELECT * FROM $table WHERE id = ?";
        $stmt = $connextion->prepare($query);
        $stmt->bind_param("i", $rowId);

        if ($stmt->execute()) {
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                // Fetch the row and return it as JSON
                $row = $result->fetch_assoc();
                echo json_encode(['success' => true, 'row' => $row]);
            } else {
                // Return error if no row found
                echo json_encode(['error' => 'Row not found']);
            }
        } else {
            // Return error if query execution fails
            echo json_encode(['error' => 'Query execution failed']);
        }

        $stmt->close();
    } else {
        // Return error for invalid form type
        echo json_encode(['error' => 'Invalid form type']);
    }
} else {
    // Return error if required parameters are missing
    echo json_encode(['error' => 'Invalid request parameters']);
}

$connextion->close(); // Close connection using $connextion
?>
