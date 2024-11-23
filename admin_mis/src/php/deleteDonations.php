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

// Get donation ID from the URL query parameter
$donationId = isset($_GET['id']) ? intval($_GET['id']) : null;

if ($donationId) {
    // Start transaction
    $connextion->begin_transaction();

    try {
        // Get the donatorID associated with the artifact
        $getDonatorIdQuery = "SELECT artifactID FROM Artifact WHERE donatorID = ?";
        $stmtDonatorId = $connextion->prepare($getDonatorIdQuery);
        if (!$stmtDonatorId) {
            throw new Exception('Failed to prepare donator ID retrieval query.');
        }
        $stmtDonatorId->bind_param("i", $donationId);
        $stmtDonatorId->execute();
        $result = $stmtDonatorId->get_result();

        if ($result->num_rows === 0) {
            throw new Exception('No artifact found with the provided ID.');
        }

        $row = $result->fetch_assoc();
        $donatorID = $row['donatorID'];

        // Delete from Artifact table
        $deleteArtifact = "DELETE FROM Artifact WHERE donatorID = ?";
        $stmtArtifact = $connextion->prepare($deleteArtifact);
        if (!$stmtArtifact) {
            throw new Exception('Failed to prepare Artifact deletion query.');
        }
        $stmtArtifact->bind_param("i", $donationId);
        $stmtArtifact->execute();

        // Delete from Lending table
        $deleteLending = "DELETE FROM Lending WHERE donatorID = ?";
        $stmtLending = $connextion->prepare($deleteLending);
        if (!$stmtLending) {
            throw new Exception('Failed to prepare Lending deletion query.');
        }
        $stmtLending->bind_param("i", $donationId);
        $stmtLending->execute();

        // Delete from Donation table
        $deleteDonation = "DELETE FROM Donation WHERE donatorID = ?";
        $stmtDonation = $connextion->prepare($deleteDonation);
        if (!$stmtDonation) {
            throw new Exception('Failed to prepare Donation deletion query.');
        }
        $stmtDonation->bind_param("i", $donationId);
        $stmtDonation->execute();

        // Check if the donator has other artifacts
        $checkDonatorArtifacts = "SELECT COUNT(*) AS artifactCount FROM Artifact WHERE donatorID = ?";
        $stmtCheckDonator = $connextion->prepare($checkDonatorArtifacts);
        if (!$stmtCheckDonator) {
            throw new Exception('Failed to prepare Donator artifact check query.');
        }
        $stmtCheckDonator->bind_param("i", $donatorID);
        $stmtCheckDonator->execute();
        $resultCheck = $stmtCheckDonator->get_result();
        $artifactCount = $resultCheck->fetch_assoc()['artifactCount'];

        // If no other artifacts reference this donator, delete the donator
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
        echo json_encode(['message' => 'Donation and related records deleted successfully.']);
    } catch (Exception $e) {
        // Rollback transaction in case of failure
        $connextion->rollback();
        echo json_encode(['error' => 'Deletion failed: ' . $e->getMessage()]);
    }
} else {
    // Error if donation ID is not provided
    echo json_encode(['error' => 'Donation ID is required.']);
}

// Close database connection
$connextion->close();
?>
