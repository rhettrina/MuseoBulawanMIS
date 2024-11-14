<?php
// update.php
include 'db_conn.php'; // Ensure connection to the database

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $transfer_status = $_POST['transfer_status'];

    // Prepare the update statement
    $stmt = $db->prepare("UPDATE donations SET transfer_status = ? WHERE id = ?");
    $stmt->bind_param("si", $transfer_status, $id);

    if ($stmt->execute()) {
        echo "Transfer status updated successfully.";
    } else {
        echo "Error updating status: " . $stmt->error;
    }

    $stmt->close();
}
?>
