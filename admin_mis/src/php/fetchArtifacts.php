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
    Donation.artifact_description,
    Donation.submission_date AS DonationSubmissionDate,

    -- Columns from Artifact table
    Artifact.artifactID,
    Artifact.artifact_typeID AS type,
    Artifact.submission_date AS ArtifactSubmissionDate,
    Artifact.donatorID,
    Artifact.artifact_description,
    Artifact.artifact_nameID,
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
    Donator.submission_date AS DonatorSubmissionDate,

    -- Columns from Lending table
    Lending.lendingID,
    Lending.donatorID AS LendingDonatorID,
    Lending.artifact_nameID AS LendingArtifactNameID,
    Lending.lending_durationID,
    Lending.display_conditions,
    Lending.liability_concerns,
    Lending.lending_reason,
    Lending.submission_date AS LendingSubmissionDate,
    Lending.starting_date,
    Lending.ending_date

FROM Donation
JOIN Artifact ON Donation.artifact_nameID = Artifact.artifact_nameID
JOIN Donator ON Donation.donatorID = Donator.donatorID
JOIN Lending ON Lending.artifact_nameID = Donation.artifact_nameID;


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
