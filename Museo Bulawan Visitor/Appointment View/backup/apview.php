<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointment View</title><!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans:ital,wght@0,100..900;1,100..900&display=swap"
        rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Hina+Mincho&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap"
        rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <!-- Custom Stylesheet -->
    <link href="apstyle.css" rel="stylesheet">

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
</head>
<body>
<?php
// Database connection
$conn = new mysqli('localhost', 'root', '', 'my_database');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch data from the database
$id = 1; // Example: Replace this with the actual ID from the URL or form submission
$query = "SELECT * FROM form_data WHERE id = $id";
$result = $conn->query($query);

// Check if the query was successful and if there's data to fetch
if ($result && $result->num_rows > 0) {
    // Fetch the row data
    $row = $result->fetch_assoc();
} else {
    echo "No data found.";
    exit();
}

// Check if the approval button is clicked
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $status = ''; // Default status

    if (isset($_POST['approve'])) {
        $status = 'Approved'; // Update status to 'Approved'
    } elseif (isset($_POST['decline'])) {
        $status = 'Declined'; // Update status to 'Declined'
    }

    // Update the status in the database
    if ($status) {
        $updateSql = "UPDATE form_data SET status = '$status' WHERE id = $id";
        if ($conn->query($updateSql) === TRUE) {
            echo "<p>Status updated to: $status</p>";
        } else {
            echo "Error updating record: " . $conn->error;
        }
    }
}

$conn->close();
?>



<div class="spacer ms-5 mt-5">
    <div class="container ms-1">
        <div class="row">
            <div class="col-6 custom-card">
                <h4 class="me-3">Name</h4>
                <div class="d-flex justify-content-around flex-grow-1">
                    <div class="text-center custom-column">
                        <h4><?php echo htmlspecialchars($row['first_name']); ?></h4>
                        <hr class="custom-hr">
                        <h5 class="text-muted">First Name</h5>
                    </div>
                    <div class="text-center custom-column">
                        <h4><?php echo htmlspecialchars($row['last_name']); ?></h4>
                        <hr class="custom-hr">
                        <h5 class="text-muted">Last Name</h5>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Repeat similar HTML sections for the other fields like Email, Phone, Address, etc. -->


<div class="spacer ms-5">
    <div class="container ms-1">
        <div class="row">
            <div class="col-6 custom-card">
                <h4 class="me-3">Email</h4>
                <div class="d-flex justify-content-around flex-grow-1">
                    <div class="text-center custom-column">
                        <h4><?php echo htmlspecialchars($row['email']); ?></h4>
                        <hr class="custom-hr">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="spacer ms-5">
    <div class="container ms-1">
        <div class="row">
            <div class="col-6 custom-card">
                <h4 class="me-3">Phone Number</h4>
                <div class="d-flex justify-content-around flex-grow-1">
                    <div class="text-center custom-column">
                        <h4><?php echo htmlspecialchars($row['phone']); ?></h4>
                        <hr class="custom-hr">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="spacer ms-5">
    <div class="container ms-1">
        <div class="row">
            <div class="col-6 custom-card">
                <h4 class="me-3">Address</h4>
                <div class="d-flex justify-content-around flex-grow-1">
                    <div class="text-center custom-column">
                        <h4><?php echo htmlspecialchars($row['street'] . ', ' . $row['barangay'] . ', ' . $row['city'] . ', ' . $row['province']); ?></h4>
                        <hr class="custom-hr">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="spacer ms-5">
    <div class="container ms-1">
        <div class="row">
            <div class="col-8 custom-card">
                <h4 class="me-3">Organization</h4>
                <div class="d-flex justify-content-around flex-grow-1">
                    <div class="text-center custom-column">
                        <h4><?php echo htmlspecialchars($row['organization']); ?></h4>
                        <hr class="custom-hr">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="spacer ms-5">
    <div class="container ms-1">
        <div class="row">
            <div class="col-8 custom-card">
                <h4 class="me-3">Population Count</h4>
                <div class="d-flex justify-content-around flex-grow-1">
                    <div class="text-center custom-column">
                        <h4><?php echo htmlspecialchars($row['attendees']); ?></h4>
                        <hr class="custom-hr">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="spacer ms-5">
    <div class="container ms-1">
        <div class="row">
            <div class="col-8 custom-card">
                <h4 class="me-3">Preferred Date</h4>
                <div class="d-flex justify-content-around flex-grow-1">
                    <div class="text-center custom-column">
                        <h4><?php echo htmlspecialchars($row['preferred_date']); ?></h4>
                        <hr class="custom-hr">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="spacer ms-5">
    <div class="container ms-1">
        <div class="row">
            <div class="col-8 custom-card">
                <h4 class="me-3">Preferred Time</h4>
                <div class="d-flex justify-content-around flex-grow-1">
                    <div class="text-center custom-column">
                        <h4><?php echo htmlspecialchars($row['preferred_time']); ?></h4>
                        <hr class="custom-hr">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="spacer ms-5">
    <div class="container ms-1">
        <div class="row">
            <div class="col-8 custom-card">
                <h4 class="me-3">Notes</h4>
                <div class="d-flex justify-content-around flex-grow-1">
                    <div class="text-center custom-column">
                        <h4><?php echo htmlspecialchars($row['notes']); ?></h4>
                        <hr class="custom-hr">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Approve Visit Section -->
<h2 class="text-strong ms-5 mt-5">Approve Visit?</h2>

<form method="POST" action="">
    <div class="container ms-5 mt-3">
        <button type="submit" name="approve" class="btn custom-btn-yes">Yes</button>
        <button type="submit" name="decline" class="btn custom-btn-no">No</button>
    </div>
</form>


<div class="container my-5 ms-5">
    <div class="row">
        <div class="col-md-8">
            <div class="p-4 border rounded border-2 border-dark opacity-100 bg-white">
                <p>Noted, but the museum can only accommodate 50 people, as it may become crowded otherwise.</p>
            </div>
        </div>
    </div>
</div>

<div class="container me-3">
    <button type="button" class="btn custom-btn-yes">Submit</button>
</div>

</body>
</html>