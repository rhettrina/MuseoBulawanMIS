<?php
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); 
header("Access-Control-Allow-Headers: Content-Type, x-requested-with");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0); 
}

include 'db_connect.php';

// Get sort parameter from the query string (default is 'newest')
$sort = $_GET['sort'] ?? 'newest'; 
$order = ($sort === 'oldest') ? 'ASC' : 'DESC';

// Query to fetch sorted donations with the necessary joins
$query = "
    SELECT 
        l.lendingID AS formID, 
        l.submission_date AS submission_date,
        CONCAT(dn.first_name, ' ', dn.last_name) AS donor_name, 
        a.artifact_nameID AS artifact_title, 
        a.artifact_typeID AS artifact_type, 
        'To Review' AS status, 
        'Pending' AS transfer_status, 
        'Lending' AS form_type,
        a.updated_date AS updated_date

        
    FROM 
        Lending AS l
    JOIN 
        Donator AS dn ON l.donatorID = dn.donatorID
    JOIN 
        Artifact AS a ON l.artifact_nameID = a.artifact_nameID

    UNION ALL

    SELECT 
        d.donationID AS formID, 
        d.submission_date AS submission_date,
        CONCAT(dn.first_name, ' ', dn.last_name) AS donor_name, 
        a.artifact_nameID AS artifact_title, 
        a.artifact_typeID AS artifact_type, 
        'To Review' AS status, 
        'Pending' AS transfer_status, 
        'Donation' AS form_type,
        a.updated_date AS updated_date
        
    FROM 
        Donation AS d
    JOIN 
        Donator AS dn ON d.donatorID = dn.donatorID
    JOIN 
        Artifact AS a ON d.artifact_nameID = a.artifact_nameID

    ORDER BY submission_date $order

";

$result = mysqli_query($connextion, $query);

if (!$result) {
    echo json_encode(['error' => 'Database query failed']);
    exit;
}

$Donation = [];
while ($row = mysqli_fetch_assoc($result)) {
    $Donation[] = $row;
}

// Return JSON response
echo json_encode($Donation);

?>
