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
        // Delete from Artifact table
        $deleteArtifact = "DELETE FROM Artifact WHERE artifactID = ?";
        $stmtArtifact = $connextion->prepare($deleteArtifact);
        if (!$stmtArtifact) {
            throw new Exception('Failed to prepare Artifact deletion query.');
        }
        $stmtArtifact->bind_param("i", $donationId);
        $stmtArtifact->execute();

        // Delete from Lending table
        $deleteLending = "DELETE FROM Lending WHERE artifactID = ?";
        $stmtLending = $connextion->prepare($deleteLending);
        if (!$stmtLending) {
            throw new Exception('Failed to prepare Lending deletion query.');
        }
        $stmtLending->bind_param("i", $donationId);
        $stmtLending->execute();

        // Delete from Donation table
        $deleteDonation = "DELETE FROM Donation WHERE artifactID = ?";
        $stmtDonation = $connextion->prepare($deleteDonation);
        if (!$stmtDonation) {
            throw new Exception('Failed to prepare Donation deletion query.');
        }
        $stmtDonation->bind_param("i", $donationId);
        $stmtDonation->execute();

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
