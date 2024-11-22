    <?php
    // Include the database connection
    header("Access-Control-Allow-Origin: *"); 
    header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); 
    header("Access-Control-Allow-Headers: Content-Type, x-requested-with");

        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
            exit(0); 
        }


    include('db_connect.php');

    $query = "SELECT COUNT(*) AS total-appointments FROM form_data";
$result = mysqli_query($connextion, $query);

if ($result) {
    $row = mysqli_fetch_assoc($result);
    if ($row) {
        echo json_encode(['total-appointments' => $row['total-appointments']]);
    } else {
        echo json_encode(['error' => 'No data found']);
    }
} else {
    echo json_encode(['error' => 'Error executing query']);
}


    // Close the database connection
    mysqli_close($connextion);


    ?>
