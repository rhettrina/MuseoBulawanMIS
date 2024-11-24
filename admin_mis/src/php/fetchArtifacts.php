<?php
// Include your database connection
include 'db_connect.php';

// Prepare the SQL query
$sql = "
SELECT 
    *
FROM 
    Lending
JOIN 
    Donator 
ON 
    Lending.donatorID = Donator.donatorID
JOIN 
    Donation 
ON 
    Donator.donatorID = Donation.donatorID
JOIN 
    Artifact 
ON 
    Donation.artifact_nameID = Artifact.artifact_nameID;
";

// Execute the query
$result = mysqli_query($connextion, $sql);

// Check if query was successful
if ($result) {
    // Fetch all rows into an array
    $data = mysqli_fetch_all($result, MYSQLI_ASSOC);

    // Encode data into JSON format
    echo json_encode($data, JSON_PRETTY_PRINT);

} else {
    // Output an error message
    echo json_encode([
        "error" => "Query failed",
        "details" => mysqli_error($connextion)
    ]);
}

// Close the database connection
mysqli_close($connextion);
?>
