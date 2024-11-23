<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, DELETE");
header("Access-Control-Allow-Headers: Content-Type, x-requested-with");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}

// Include database connection file
include 'db_connect.php';

// Get donation ID from URL query parameter
$donationId = isset($_GET['id']) ? $_GET['id'] : null;

if ($donationId) {
    // Validate donationId to ensure it's numeric
    if (!is_numeric($donationId)) {
        echo json_encode(['error' => 'Invalid donation ID format']);
        exit;
    }

    // Correct column name for the query (replace `artifact_id` if needed)
    $donatorQuery = "SELECT donatorID FROM Artifact WHERE artifactId = ?";
    $stmt = $connextion->prepare($donatorQuery);

    if (!$stmt) {
        echo json_encode(['error' => 'Database query error']);
        exit;
    }

    $stmt->bind_param("i", $donationId);

    if ($stmt->execute()) {
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $donatorData = $result->fetch_assoc();
            $donatorId = $donatorData['donatorID'];

            // Begin deletion process
            $connextion->begin_transaction();

            try {
                // Delete records from Lending table
                $deleteLending = "DELETE FROM Lending WHERE donatorID = ?";
                $stmtLending = $connextion->prepare($deleteLending);
                $stmtLending->bind_param("i", $donatorId);
                $stmtLending->execute();

                // Delete records from Donation table
                $deleteDonation = "DELETE FROM Donation WHERE donatorID = ?";
                $stmtDonation = $connextion->prepare($deleteDonation);
                $stmtDonation->bind_param("i", $donatorId);
                $stmtDonation->execute();

                // Delete records from Donator table
                $deleteDonator = "DELETE FROM Donator WHERE donatorID = ?";
                $stmtDonator = $connextion->prepare($deleteDonator);
                $stmtDonator->bind_param("i", $donatorId);
                $stmtDonator->execute();

                // Commit transaction
                $connextion->commit();

                // Return success message
                echo json_encode(['message' => 'All related records deleted successfully']);
            } catch (Exception $e) {
                // Rollback transaction on failure
                $connextion->rollback();
                echo json_encode(['error' => 'Failed to delete records: ' . $e->getMessage()]);
            }
        } else {
            echo json_encode(['error' => 'Donation not found']);
        }
    } else {
        echo json_encode(['error' => 'Failed to retrieve donation information']);
    }

    $stmt->close();
} else {
    // Return error if ID is not provided
    echo json_encode(['error' => 'Donation ID is required']);
}

$connextion->close(); // Close connection
?>
