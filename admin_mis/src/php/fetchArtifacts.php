
<?php


header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, DELETE");
header("Access-Control-Allow-Headers: Content-Type, x-requested-with");


if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0); 
}


$svn = "localhost"; 
$username = "u376871621_bomb_squad";       
$password = "Fujiwara000!";            
$dbname = "u376871621_mb_mis";   

$conn = new mysqli($svn, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

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
$result = mysqli_query($conn, $sql);

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
        "details" => mysqli_error($conn)
    ]);
}

// Close the database connection
mysqli_close($conn);
?>
