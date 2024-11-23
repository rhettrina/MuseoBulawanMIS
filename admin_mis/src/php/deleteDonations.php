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
$donationId = isset($_GET['id']) ? $_GET['id'] : null;

if ($donationId) {
    // Validate donationId to ensure it's numeric
    if (!is_numeric($donationId)) {
        echo json_encode(['error' => 'Invalid donation ID format. ID must be numeric.']);
        exit;
    }

    // Query to get donor ID using the donation ID
    $donorQuery = "SELECT donorID FROM Artifact WHERE artifactId = ?";
    $stmt = $connection->prepare($donorQuery);

    if (!$stmt) {
        echo json_encode(['error' => 'Database query preparation error.']);
        exit;
    }

    $stmt->bind_param("i", $donationId);

    if ($stmt->execute()) {
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $donorData = $result->fetch_assoc();
            $donorId = $donorData['donorID'];

            // Begin deletion process
            $connection->begin_transaction();

            try {
                // Delete records from the Lending table
                $deleteLending = "DELETE FROM Lending WHERE donorID = ?";
                $stmtLending = $connection->prepare($deleteLending);
                if (!$stmtLending) {
                    throw new Exception('Failed to prepare Lending deletion query.');
                }
                $stmtLending->bind_param("i", $donorId);
                $stmtLending->execute();

                // Delete records from the Donation table
                $deleteDonation = "DELETE FROM Donation WHERE donorID = ?";
                $stmtDonation = $connection->prepare($deleteDonation);
                if (!$stmtDonation) {
                    throw new Exception('Failed to prepare Donation deletion query.');
                }
                $stmtDonation->bind_param("i", $donorId);
                $stmtDonation->execute();

                // Delete records from the Donor table
                $deleteDonor = "DELETE FROM Donor WHERE donorID = ?";
                $stmtDonor = $connection->prepare($deleteDonor);
                if (!$stmtDonor) {
                    throw new Exception('Failed to prepare Donor deletion query.');
                }
                $stmtDonor->bind_param("i", $donorId);
                $stmtDonor->execute();

                // Commit transaction
                $connection->commit();
                echo json_encode(['message' => 'All related records deleted successfully.']);
            } catch (Exception $e) {
                // Rollback transaction on failure
                $connection->rollback();
                echo json_encode(['error' => 'Deletion failed: ' . $e->getMessage()]);
            }
        } else {
            echo json_encode(['error' => 'No matching donation found for the provided ID.']);
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
$connection->close();
?>
