<?php
// Include your database connection
include 'db_connect.php';

// Prepare the SQL query
$sql = "
SELECT 
    Lending.lendingID,
    Lending.artifact_nameID,
    Lending.lending_durationID,
    Lending.display_conditions,
    Lending.liability_concerns,
    Lending.lending_reason,
    Lending.submission_date AS lending_submission_date,
    Lending.starting_date,
    Lending.ending_date,
    Donator.donatorID,
    Donator.first_name,
    Donator.last_name,
    Donator.email,
    Donator.phone,
    Donator.province,
    Donator.street,
    Donator.barangay,
    Donator.organization,
    Donator.age,
    Donator.sex,
    Donator.city,
    Donator.submission_date AS donator_submission_date,
    Donation.donationID,
    Donation.artifact_nameID,
    Donation.artifact_description,
    Donation.submission_date AS donation_submission_date
FROM 
    Lending
JOIN 
    Donator 
ON 
    Lending.donatorID = Donator.donatorID
JOIN 
    Donation 
ON 
    Donator.donatorID = Donation.donatorID;
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
