<?php
// Include your database connection
include 'db_connect.php';

// Check if `donatorID` is provided in the request
if (!isset($_GET['donatorID'])) {
    echo json_encode([
        "error" => "donatorID parameter is missing."
    ]);
    exit;
}

// Get the donatorID from the request
$donatorID = intval($_GET['donatorID']);

// Prepare the SQL query to fetch artifact details
$sql = "
SELECT 
    Artifact.artifact_nameID AS artifactTitle,
    Artifact.status AS artifactStatus,
    Artifact.transfer_status AS transferStatus
FROM Artifact
WHERE Artifact.donatorID = ?";

// Prepare the SQL statement
$stmt = $connextion->prepare($sql);
if (!$stmt) {
    echo json_encode([
        "error" => "Failed to prepare statement.",
        "details" => $connextion->error
    ]);
    exit;
}

// Bind the `donatorID` parameter
$stmt->bind_param("i", $donatorID);

// Execute the query
$stmt->execute();

// Get the result set
$result = $stmt->get_result();

// Check if query was successful
if ($result) {
    // Fetch all rows into an array
    $data = $result->fetch_all(MYSQLI_ASSOC);

    // Encode data into JSON format and output it
    echo json_encode($data, JSON_PRETTY_PRINT);
} else {
    // Output an error message
    echo json_encode([
        "error" => "Query failed",
        "details" => $stmt->error
    ]);
}

// Close the statement and the database connection
$stmt->close();
$connextion->close();
?>
