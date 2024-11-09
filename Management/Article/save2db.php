<?php
include('builder_DBconnection.php'); // Include database connection

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect POST data from the form
    $title = $_POST['title'];
    $articleType = $_POST['articleType'];
    $location = $_POST['location'];
    $author = $_POST['author'];
    $p1Left = $_POST['p1Left'];
    $p1Right = $_POST['p1Right'];
    $p2 = $_POST['p2'];
    $p3 = $_POST['p3'];
    $img1Details = $_POST['img1Details'];

    // Handle file uploads (you need to ensure that 'uploads' folder exists and is writable)
    function uploadImage($imgFile, $uploadDir) {
        if ($imgFile && $imgFile['error'] == 0) {
            $filePath = $uploadDir . basename($imgFile['name']);
            move_uploaded_file($imgFile['tmp_name'], $filePath);
            return $filePath;
        }
        return null;
    }

    $uploadDir = 'uploads/';
    $img1Path = uploadImage($_FILES['img1'], $uploadDir);
    $img2Path = uploadImage($_FILES['img2'], $uploadDir);
    $img3Path = uploadImage($_FILES['img3'], $uploadDir);

    // Database insertion
    $sql = "INSERT INTO articles (title, article_type, location, author, p1_left, p1_right, p2, p3, img1_details, img1_path, img2_path, img3_path)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssssssss", $title, $articleType, $location, $author, $p1Left, $p1Right, $p2, $p3, $img1Details, $img1Path, $img2Path, $img3Path);

    if ($stmt->execute()) {
        echo "Article saved successfully.";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
} else {
    // Respond with an error if the request method is not POST
    http_response_code(405); // Method Not Allowed
    echo "Error: Invalid request method.";
}
?>
