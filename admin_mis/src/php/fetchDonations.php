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
    SELECT  dn.donatorID AS donID,
        l.lendingID AS formID, 
        l.submission_date AS submission_date,
        CONCAT(dn.first_name, ' ', dn.last_name) AS donor_name, 
        a.artifact_nameID AS artifact_title, 
        'Lending' AS form_type, -- Explicitly set form type as 'Lending'
        'To Review' AS status, 
        'Pending' AS transfer_status, 
        a.updated_date AS updated_date
       
    FROM 
        Lending AS l
    JOIN 
        Donator AS dn ON l.donatorID = dn.donatorID
    JOIN 
        Artifact AS a ON l.artifact_nameID = a.artifact_nameID

    UNION ALL

    SELECT dn.donatorID AS donID,
        d.donationID AS formID, 
        d.submission_date AS submission_date,
        CONCAT(dn.first_name, ' ', dn.last_name) AS donor_name, 
        a.artifact_nameID AS artifact_title, 
        'Donation' AS form_type, -- Explicitly set form type as 'Donation'
        'To Review' AS status, 
        'Pending' AS transfer_status, 
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

$donations = [];
while ($row = mysqli_fetch_assoc($result)) {
    $donations[] = $row;
}

// Return JSON response
echo json_encode($donations);

?>
