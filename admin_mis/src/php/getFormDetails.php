<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type");

// Include database connection
include 'db_connect.php';

// Validate and fetch parameters
$donID = isset($_GET['donID']) ? intval($_GET['donID']) : null;
$formType = isset($_GET['formType']) ? $_GET['formType'] : null;

if (!$donID || !$formType) {
    echo json_encode(['success' => false, 'error' => 'Missing required parameters']);
    exit();
}

try {
    // Build query based on form type
    if ($formType === 'Donation') {
        $query = "SELECT d.*, dn.first_name, dn.last_name, dn.age, dn.sex, dn.email, dn.phone, 
                         dn.organization, dn.street, dn.barangay, dn.city, dn.province,
                         a.artifact_title, a.artifact_description, a.acquisition, 
                         a.additional_info, a.narrative, a.images, a.documentation, a.related
                  FROM Donation AS d
                  JOIN Donator AS dn ON d.donatorID = dn.donatorID
                  JOIN Artifact AS a ON d.artifact_nameID = a.artifact_nameID
                  WHERE d.donatorID = ?";
    } elseif ($formType === 'Lending') {
        $query = "SELECT l.*, dn.first_name, dn.last_name, dn.age, dn.sex, dn.email, dn.phone, 
                         dn.organization, dn.street, dn.barangay, dn.city, dn.province,
                         a.artifact_title, a.artifact_description, a.acquisition, 
                         a.additional_info, a.narrative, a.images, a.documentation, a.related
                  FROM Lending AS l
                  JOIN Donator AS dn ON l.donatorID = dn.donatorID
                  JOIN Artifact AS a ON l.artifact_nameID = a.artifact_nameID
                  WHERE l.donatorID = ?";
    } else {
        echo json_encode(['success' => false, 'error' => 'Invalid form type']);
        exit();
    }

    // Prepare and execute statement
    $stmt = $connextion->prepare($query);
    $stmt->bind_param("i", $donID);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo json_encode(['success' => false, 'error' => 'No record found']);
    } else {
        $formDetails = $result->fetch_assoc();
        echo json_encode(['success' => true, 'formDetails' => $formDetails]);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
