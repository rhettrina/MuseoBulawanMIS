<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Hind:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointment</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
</head>

<body>
    <div class="header">
        <div class="head-wrapper">
            <div class="brand-wrapper">
                <img src="../../Museo Bulawan Visitor/source/LOGO.png" alt="" width="280px" height="280px">
                <div class="line-spacer">

                </div>
                <div class="text-wrapper">
                    <h1>
                        MUSEO BULAWAN
                    </h1>
                    <h2>
                        MANAGEMENT INFORMATION SYSTEM
                    </h2>
                </div>
            </div>
        </div>
    </div>

    <div class="display-wrapper">
        <div class="navigation-panel">
            <div class="nav-button">
                
                <div class="button-container">
                   
                    <button onclick="location.href='../../Management/Dashboard/dashboard.html'" class="dashboard-button">
                        <div class="db-icon">
                            <img src="../../Museo Bulawan Visitor/source/donation-button.png" alt="">
                        </div>
                        <div class="db-text">
                            Dashboard
                        </div>
                    </button>
                
                    
                    <button onclick="location.href='../../Management/Donation/donation.html'" class="donation-button">
                        <div class="dob-icon">
                            <img src="../../Museo Bulawan Visitor/source/donation-button.png" alt="">
                        </div>
                        <div class="dob-text">
                            Donation
                        </div>
                    </button>
                

                    <button onclick="location.href='../../Management/Artifacts/artifacts.html'" class="artifact-button">
                        <div class="ar-icon">
                            <img src="../../Museo Bulawan Visitor/source/artifact-icon.png" alt="">
                        </div>
                        <div class="ar-text">
                            Artifact
                        </div>

                    </button>
                    <button onclick="location.href='../../Management/Appointment/index.html'" class="appointment-button active">
                        <div class="ap-icon">
                            <img src="../../Museo Bulawan Visitor/source/appointment-icon.png" alt="">
                        </div>
                        <div class="ap-text">
                            Appointment
                        </div>
                    </button>
                    <button onclick="location.href='../../Management/Artifact Layout Management/artifact-layout.html'" class="layout-button">
                        <div class="lt-icon">
                            <img src="../../Museo Bulawan Visitor/source/artifact-layout.png" alt="">
                        </div>
                        <div class="lt-text">
                            Layout
                        </div>

                    </button>
                    <button onclick="location.href='../../Management/Article/articles.html'" class="article-button">
                        <div class="at-icon">
                            <img src="../../Museo Bulawan Visitor/source/article-icon.png" alt="">
                        </div>
                        <div class="at-text">
                            Article
                        </div>
                    </button>
                </div>
                <button class="logout-button">
                    <div class="log-icon">
                        <img src="../../Museo Bulawan Visitor/source/logout-icon.png" alt="">
                    </div>
                    <div class="log-text">
                        Logout
                    </div>
                </button>
            </div>
        </div>

        <div class="content-container">
            <div class="notification-container">
                <button class="notification-button">
                    <img src="../../Museo Bulawan Visitor/source/notifiaction-icon.png" alt="">
                </button>


            </div>

            
        <!-- Content Container -->
        <div class="content-container">
            <!-- Notification Button -->
            <div class="notification-container">
                <button class="notification-button">
                    <img src="../../Museo Bulawan Visitor/source/notification-icon.png" alt="Notification Icon">
                </button>
            </div>

            <!-- Main Content -->
            <div class="main-content">
                <!-- Page Content -->
                <div id="forms" class="page-content">
                    <!-- Appointment Title -->
                    <h2 class="donation-title">Appointment</h2>

                    <!-- Summary Boxes -->
                    <div class="donation-header">
                        <!-- Total Appointment -->
                        <div class="summary-box total">
                            <div class="summary-left">
                                <span class="summary-title">Total Appointment</span>
                            </div>
                            <div class="summary-right">
                                <span class="summary-number">271</span>
                            </div>
                        </div>
                        <!-- Approved -->
                        <div class="summary-box accepted">
                            <div class="summary-left">
                                <span class="summary-title">Approved</span>
                            </div>
                            <div class="summary-right">
                                <span class="summary-number">255</span>
                            </div>
                        </div>
                        <!-- Rejected -->
                        <div class="summary-box rejected">
                            <div class="summary-left">
                                <span class="summary-title">Rejected</span>
                            </div>
                            <div class="summary-right">
                                <span class="summary-number">16</span>
                            </div>
                        </div>
                        <!-- Expected Visitors -->
                        <div class="summary-box donation">
                            <div class="summary-left">
                                <span class="summary-title">Expected Visitors</span>
                            </div>
                            <div class="summary-right">
                                <span class="summary-number">256</span>
                            </div>
                        </div>
                        <!-- Present -->
                        <div class="summary-box lending">
                            <div class="summary-left">
                                <span class="summary-title">Present</span>
                            </div>
                            <div class="summary-right">
                                <span class="summary-number">15</span>
                            </div>
                        </div>
                    </div>

                    <!-- Navigation Tabs -->
                    <div class="nav-bar">
                        <div class="tab active" id="forms-tab" onclick="showPage('forms')">Forms</div>
                        <div class="tab" id="transfer-tab" onclick="showPage('transfer')">Transfer Status</div>
                        <div class="tab" id="donators-tab" onclick="showPage('donators-records')">Donators Records</div>
                    </div>


                        <?php
                    // Database connection
                    $conn = new mysqli('localhost', 'root', '', 'my_database');

                    if ($conn->connect_error) {
                        die("Connection failed: " . $conn->connect_error);
                    }

                    // Default sorting
                    $order = "DESC";
                    if (isset($_GET['sort'])) {
                        $sort = $_GET['sort'];
                        if ($sort == "date-oldest") {
                            $order = "ASC";
                        } else {
                            $order = "DESC";
                        }
                    }

                    // Query to retrieve data and sort by date and time
                    $sql = "SELECT preferred_date, first_name, last_name, preferred_time, attendees FROM form_data ORDER BY preferred_date $order, preferred_time $order";
                    $result = $conn->query($sql);
                    ?>

                    <!-- Sort Dropdown -->
                    <form method="GET" action="">
                        <div class="sort-container">
                            <label for="sort">By Date:</label>
                            <select id="sort" name="sort" onchange="this.form.submit()">
                                <option value="date-newest" <?php if ($order == "DESC") echo "selected"; ?>>Newest</option>
                                <option value="date-oldest" <?php if ($order == "ASC") echo "selected"; ?>>Oldest</option>
                            </select>
                        </div>
                    </form>

                    <!-- Data Table -->
                    <div class="table-container">
                        <table class="donation-table custom-table" id="donationTable">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Name</th>
                                    <th>Time</th>
                                    <th>Status</th>
                                    <th>Number</th>
                                    <th>Confirmation Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if ($result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        echo "<tr>";
                                        echo "<td>" . htmlspecialchars($row['preferred_date']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['first_name'] . " " . $row['last_name']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['preferred_time']) . "</td>";
                                        echo "<td>Confirmed</td>"; // Placeholder for status
                                        echo "<td>" . htmlspecialchars($row['attendees']) . "</td>";
                                        echo "<td>August 25, 2024</td>"; // Placeholder for confirmation date
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='6'>No data found.</td></tr>";
                                }

                                $conn->close();
                                ?>
                            </tbody>
                        </table>
                    </div>

                    
                </div> <!-- End of page-content -->
            </div> <!-- End of main-content -->
        </div> <!-- End of content-container -->
    </div> <!-- End of display-wrapper -->

</body>

</html>