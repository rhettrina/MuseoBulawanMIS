<?php
include 'db_connect.php';

$input = json_decode(file_get_contents("php://input"), true);

if (isset($input['unique_id'])) {
    $unique_id = $input['unique_id'];

    // Set all layouts to inactive
    $resetSql = "UPDATE floorplans SET active = 0";
    $connextion->query($resetSql);

    // Set the selected layout as active
    $sql = "UPDATE floorplans SET active = 1 WHERE unique_id = ?";
    $stmt = $connextion->prepare($sql);

    if ($stmt) {
        $stmt->bind_param('s', $unique_id);

        if ($stmt->execute()) {
            echo json_encode(["success" => true, "message" => "Layout set as active successfully"]);
        } else {
            echo json_encode(["success" => false, "message" => "Failed to update layout as active"]);
        }

        $stmt->close();
    } else {
        echo json_encode(["success" => false, "message" => "Failed to prepare SQL statement"]);
    }
} else {
    echo json_encode(["success" => false, "message" => "unique_id not provided"]);
}

$connextion->close();
?>
