<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type");

// Include database connection
include 'db_connect.php';

// Validate and fetch parameters
$donID = isset($_GET['donID']) ? intval($_GET['donID']) : null;

if (!$donID) {
    echo json_encode(['success' => false, 'error' => 'Missing required parameters']);
    exit();
}

try {
    // Step 1: Determine the form type dynamically using artifact_typeID
    $formTypeQuery = "SELECT a.artifact_typeID
                      FROM Artifact AS a
                      JOIN Donation AS d ON d.artifact_nameID = a.artifact_nameID
                      WHERE d.donationID = ?
                      UNION
                      SELECT a.artifact_typeID
                      FROM Artifact AS a
                      JOIN Lending AS l ON l.artifact_nameID = a.artifact_nameID
                      WHERE l.lendingID = ?";
                      
    $formTypeStmt = $connection->prepare($formTypeQuery);
    $formTypeStmt->bind_param("ii", $donID, $donID);
    $formTypeStmt->execute();
    $formTypeResult = $formTypeStmt->get_result();

    if ($formTypeResult->num_rows === 0) {
        echo json_encode(['success' => false, 'error' => 'No record found for the given donation or lending ID']);
        exit();
    }

    $row = $formTypeResult->fetch_assoc();
    $formType = $row['artifact_typeID'];

    // Step 2: Build and execute the main query based on form type
    if ($formType === 'Donation') {
        $query = "SELECT d.*, 
                         dn.first_name, dn.last_name, dn.age, dn.sex, dn.email, dn.phone, 
                         dn.organization, dn.street, dn.barangay, dn.city, dn.province,
                         a.artifact_nameID, a.artifact_description, a.acquisition, 
                         a.additional_info, a.narrative, a.artifact_img, a.documentation, a.related_img
                  FROM Donation AS d
                  JOIN Donator AS dn ON d.donatorID = dn.donatorID
                  JOIN Artifact AS a ON d.artifact_nameID = a.artifact_nameID
                  WHERE d.donationID = ?";
    } elseif ($formType === 'Lending') {
        $query = "SELECT l.*, 
                         dn.first_name, dn.last_name, dn.age, dn.sex, dn.email, dn.phone, 
                         dn.organization, dn.street, dn.barangay, dn.city, dn.province,
                         a.artifact_nameID, a.artifact_description, a.acquisition, 
                         a.additional_info, a.narrative, a.artifact_img, a.documentation, a.related_img
                  FROM Lending AS l
                  JOIN Donator AS dn ON l.donatorID = dn.donatorID
                  JOIN Artifact AS a ON l.artifact_nameID = a.artifact_nameID
                  WHERE l.lendingID = ? AND a.artifact_typeID = 'Lending'";
    } else {
        echo json_encode(['success' => false, 'error' => 'Invalid artifact type']);
        exit();
    }

    $stmt = $connection->prepare($query);
    if (!$stmt) {
        echo json_encode(['success' => false, 'error' => 'Failed to prepare statement']);
        exit();
    }

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
    echo json_encode(['success' => false, 'error' => 'An error occurred: ' . $e->getMessage()]);
}
?>
