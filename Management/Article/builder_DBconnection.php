<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $database_server = "localhost";
    $database_user = "root";
    $database_password = "";
    $database_name = "museo_bulawan";
    $conn = mysqli_connect($database_server, $database_user, $database_password, $database_name);

    if (!$conn) {
        echo json_encode(['success' => false, 'message' => 'Database connection failed']);
        exit();
    }

    // Receive the text data
    $article_title = mysqli_real_escape_string($conn, $_POST['article_title']);
    $article_type = mysqli_real_escape_string($conn, $_POST['article_type']);
    $location = mysqli_real_escape_string($conn, $_POST['location']);
    $author = mysqli_real_escape_string($conn, $_POST['author']);
    $imgu1_details = mysqli_real_escape_string($conn, $_POST['imgu1_details']);
    $p1box_left = mysqli_real_escape_string($conn, $_POST['p1box_left']);
    $p1box_right = mysqli_real_escape_string($conn, $_POST['p1box_right']);
    $p2box = mysqli_real_escape_string($conn, $_POST['p2box']);
    $p3box = mysqli_real_escape_string($conn, $_POST['p3box']);

    // Image upload handling
    $imgu1_target = $imgu2_target = $imgu3_target = '';
    if (isset($_FILES['imgu1']) && $_FILES['imgu1']['error'] == 0) {
        $imgu1 = $_FILES['imgu1'];
        $imgu1_target = 'uploads/' . basename($imgu1['name']);
        move_uploaded_file($imgu1['tmp_name'], $imgu1_target);
    }

    if (isset($_FILES['imgu2']) && $_FILES['imgu2']['error'] == 0) {
        $imgu2 = $_FILES['imgu2'];
        $imgu2_target = 'uploads/' . basename($imgu2['name']);
        move_uploaded_file($imgu2['tmp_name'], $imgu2_target);
    }

    if (isset($_FILES['imgu3']) && $_FILES['imgu3']['error'] == 0) {
        $imgu3 = $_FILES['imgu3'];
        $imgu3_target = 'uploads/' . basename($imgu3['name']);
        move_uploaded_file($imgu3['tmp_name'], $imgu3_target);
    }

    // Insert into the database
    $query = "INSERT INTO articles (article_title, article_type, location, author, imgu1, imgu2, imgu3, imgu1_details, p1box_left, p1box_right, p2box, p3box) 
              VALUES ('$article_title', '$article_type', '$location', '$author', '$imgu1_target', '$imgu2_target', '$imgu3_target', '$imgu1_details', '$p1box_left', '$p1box_right', '$p2box', '$p3box')";

    if (mysqli_query($conn, $query)) {
        echo json_encode(['success' => true, 'message' => 'Article saved successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error saving article']);
    }

    mysqli_close($conn);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>
