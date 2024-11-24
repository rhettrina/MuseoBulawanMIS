<?php
// Include your database connection
include 'db_connect.php';

// Prepare the SQL query
$sql = "
SELECT *,

    Artifact.artifact_nameID AS artname,
    Donation.donationID,
    Donation.donatorID AS DonationDonatorID,
    Donation.submission_date AS DonationSubmissionDate,
    Artifact.artifactID,
    Artifact.artifact_nameID,
    Artifact.submission_date AS ArtifactSubmissionDate,
    Donator.first_name,
    Donator.last_name,
    Lending.lendingID,
    Lending.artifact_nameID AS LendingArtName,
    Lending.lending_durationID,
    Lending.submission_date AS LendingSubmissionDate
FROM Donator
LEFT JOIN Artifact ON Donator.donatorID = Artifact.donatorID
LEFT JOIN Donation ON Donator.donatorID = Donation.donatorID
LEFT JOIN Lending ON Lending.donatorID = Donator.donatorID;

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
