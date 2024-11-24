<?php
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); 
header("Access-Control-Allow-Headers: Content-Type, x-requested-with");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0); 
}

include 'db_connect.php';

// Validate and set sort parameter
$sort = isset($_GET['sort']) && $_GET['sort'] === 'oldest' ? 'ASC' : 'DESC';

// Query to fetch sorted donations with necessary joins
$query = "
    SELECT DISTINCT
        d.donationID AS formID,
        d.submission_date AS submission_date,
        CONCAT(dn.first_name, ' ', dn.last_name) AS donor_name,
        a.artifact_nameID AS artifact_title,
        'Donation' AS form_type,
        d.transfer_status, -- Correctly fetch transfer_status from the database
        a.updated_date AS updated_date,
        dn.donatorID AS donID
    FROM 
        Donation AS d
    JOIN 
        Donator AS dn ON d.donatorID = dn.donatorID
    JOIN 
        Artifact AS a ON d.artifact_nameID = a.artifact_nameID
    ORDER BY submission_date $sort
";

// Execute the query
$result = mysqli_query($connextion, $query);

if (!$result) {
    // Improved error message
    echo json_encode(['error' => 'Database query failed: ' . mysqli_error($connextion)]);
    exit;
}

$donations = [];
while ($row = mysqli_fetch_assoc($result)) {
    $donations[] = $row;
}

// Return JSON response
echo json_encode($donations);
?>
