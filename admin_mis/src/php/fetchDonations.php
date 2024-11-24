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

// Validate and set sort parameter
$sort = isset($_GET['sort']) && $_GET['sort'] === 'oldest' ? 'ASC' : 'DESC';

// Query to fetch sorted donations with necessary joins
$query = "
    SELECT
        l.lendingID AS formID, 
        l.submission_date AS submission_date,
        CONCAT(dn.first_name, ' ', dn.last_name) AS donor_name, 
        a.artifact_nameID AS artifact_title, 
        'Lending' AS form_type, 
        'To Review' AS status, 
        transfer_status, 
        a.updated_date AS updated_date, 
        dn.donatorID AS donID
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
        'Donation' AS form_type, 
        'To Review' AS status, 
        transfer_status, 
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
$result = mysqli_query($conn, $query);

if (!$result) {
    // Improved error message
    echo json_encode(['error' => 'Database query failed: ' . mysqli_error($conn)]);
    exit;
}

$donations = [];
while ($row = mysqli_fetch_assoc($result)) {
    $donations[] = $row;
}

// Return JSON response
echo json_encode($donations);
?>
