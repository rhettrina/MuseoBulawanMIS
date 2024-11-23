<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, DELETE");
header("Access-Control-Allow-Headers: Content-Type, x-requested-with");

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// Include database connection file
include 'db_connect.php';

// Get donator ID from the URL query parameter
$donatorID = isset($_GET['id']) ? intval($_GET['id']) : null;

if ($donatorID) {
    // Start transaction
    $connextion->begin_transaction();

    try {
        // Delete related records from Artifact table
        $deleteArtifact = "DELETE FROM Artifact WHERE donatorID = ?";
        $stmtArtifact = $connextion->prepare($deleteArtifact);
        if (!$stmtArtifact) {
            throw new Exception('Failed to prepare Artifact deletion query.');
        }
        $stmtArtifact->bind_param("i", $donatorID);
        $stmtArtifact->execute();

        // Delete related records from Lending table
        $deleteLending = "DELETE FROM Lending WHERE donatorID = ?";
        $stmtLending = $connextion->prepare($deleteLending);
        if (!$stmtLending) {
            throw new Exception('Failed to prepare Lending deletion query.');
        }
        $stmtLending->bind_param("i", $donatorID);
        $stmtLending->execute();

        // Delete related records from Donation table
        $deleteDonation = "DELETE FROM Donation WHERE donatorID = ?";
        $stmtDonation = $connextion->prepare($deleteDonation);
        if (!$stmtDonation) {
            throw new Exception('Failed to prepare Donation deletion query.');
        }
        $stmtDonation->bind_param("i", $donatorID);
        $stmtDonation->execute();

        // Check if the donator has any remaining artifacts
        $checkArtifacts = "SELECT COUNT(*) AS artifactCount FROM Artifact WHERE donatorID = ?";
        $stmtCheckArtifacts = $connextion->prepare($checkArtifacts);
        if (!$stmtCheckArtifacts) {
            throw new Exception('Failed to prepare artifact check query.');
        }
        $stmtCheckArtifacts->bind_param("i", $donatorID);
        $stmtCheckArtifacts->execute();
        $resultCheck = $stmtCheckArtifacts->get_result();
        $artifactCount = $resultCheck->fetch_assoc()['artifactCount'];

        // If no other artifacts reference this donator, delete the donator record
        if ($artifactCount == 0) {
            $deleteDonator = "DELETE FROM Donator WHERE donatorID = ?";
            $stmtDonator = $connextion->prepare($deleteDonator);
            if (!$stmtDonator) {
                throw new Exception('Failed to prepare Donator deletion query.');
            }
            $stmtDonator->bind_param("i", $donatorID);
            $stmtDonator->execute();
        }

        // Commit transaction
        $connextion->commit();
        echo json_encode(['message' => 'Donator and related records deleted successfully.']);
    } catch (Exception $e) {
        // Rollback transaction in case of failure
        $connextion->rollback();
        echo json_encode(['error' => 'Deletion failed: ' . $e->getMessage()]);
    }
} else {
    // Error if donator ID is not provided
    echo json_encode(['error' => 'Donator ID is required.']);
}

// Close database connection
$connextion->close();
?>
