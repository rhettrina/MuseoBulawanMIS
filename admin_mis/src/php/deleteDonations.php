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
    // Query to check if the donation exists
    $donorQuery = "SELECT donatorID FROM Artifact WHERE artifactID = ?";
    $stmt = $connextion->prepare($donorQuery);

    if (!$stmt) {
        echo json_encode(['error' => 'Database query preparation error.']);
        exit;
    }

    $stmt->bind_param("i", $donationId);

    if ($stmt->execute()) {
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $donorData = $result->fetch_assoc();
            $donorId = $donorData['donatorID'];

            // Begin transaction
            $connextion->begin_transaction();

            try {
                // Delete records from the Artifact table
                $deleteArtifact = "DELETE FROM Artifact WHERE artifactID = ?";
                $stmtArtifact = $connextion->prepare($deleteArtifact);
                if (!$stmtArtifact) {
                    throw new Exception('Failed to prepare Artifact deletion query.');
                }
                $stmtArtifact->bind_param("i", $donationId);
                $stmtArtifact->execute();

                // Delete related records from the Lending table
                $deleteLending = "DELETE FROM Lending WHERE donatorID = ?";
                $stmtLending = $connextion->prepare($deleteLending);
                if (!$stmtLending) {
                    throw new Exception('Failed to prepare Lending deletion query.');
                }
                $stmtLending->bind_param("i", $donorId);
                $stmtLending->execute();

                // Delete related records from the Donation table
                $deleteDonation = "DELETE FROM Donation WHERE donatorID = ?";
                $stmtDonation = $connextion->prepare($deleteDonation);
                if (!$stmtDonation) {
                    throw new Exception('Failed to prepare Donation deletion query.');
                }
                $stmtDonation->bind_param("i", $donorId);
                $stmtDonation->execute();

                // Delete the Donator record
                $deleteDonor = "DELETE FROM Donator WHERE donatorID = ?";
                $stmtDonor = $connextion->prepare($deleteDonor);
                if (!$stmtDonor) {
                    throw new Exception('Failed to prepare Donor deletion query.');
                }
                $stmtDonor->bind_param("i", $donorId);
                $stmtDonor->execute();

                // Commit transaction
                $connextion->commit();
                echo json_encode(['message' => 'All related records deleted successfully.']);
            } catch (Exception $e) {
                // Rollback transaction on failure
                $connextion->rollback();
                echo json_encode(['error' => 'Deletion failed: ' . $e->getMessage()]);
            }
        } else {
            echo json_encode(['error' => 'No matching artifact found for the provided ID.']);
        }
    } else {
        echo json_encode(['error' => 'Failed to execute donor ID retrieval query.']);
    }

    $stmt->close();
} else {
    // Error if donation ID is not provided
    echo json_encode(['error' => 'Donation ID is required.']);
}

// Close the database connection
$connextion->close();
?>
