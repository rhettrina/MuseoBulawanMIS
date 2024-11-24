<?php
// Include your database connection
include 'db_connect.php';

// Prepare the SQL query
$sql = "
SELECT 
    -- Columns from Donation table
    Donation.donationID,
    Donation.donatorID AS DonationDonatorID,
    Donation.artifact_nameID,
    Donation.artifact_description AS DonationArtifactDescription,
    Donation.submission_date AS DonationSubmissionDate,

    -- Columns from Artifact table
    Artifact.artifactID,
    Artifact.artifact_typeID,
    Artifact.submission_date AS ArtifactSubmissionDate,
    Artifact.donatorID AS ArtifactDonatorID,
    Artifact.artifact_description AS ArtifactDescription,
    Artifact.artifact_nameID AS ArtifactNameID,
    Artifact.acquisition,
    Artifact.additional_info,
    Artifact.narrative,
    Artifact.artifact_img,
    Artifact.documentation,
    Artifact.related_img,
    Artifact.status,
    Artifact.transfer_status,
    Artifact.updated_date,
    Artifact.display_status,

    -- Columns from Donator table
    Donator.donatorID AS DonatorDonatorID,
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
    Donator.submission_date AS DonatorSubmissionDate
FROM Donation
JOIN Artifact ON Donation.artifact_nameID = Artifact.artifact_nameID
JOIN Donator ON Donation.donatorID = Donator.donatorID;

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
