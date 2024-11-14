 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Hind:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="style.css">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</head>
<body>

<?php include 'dform.php'; ?>


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
                
                    
                    <button onclick="location.href='../../Management/Donation/donation.html'" class="donation-button active">
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
                    <button onclick="location.href='../../Management/Appointment/index.html'" class="appointment-button">
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

                <div class="main-content">
                    <!-- Forms Page -->
                    <div id="forms" class="page-content">
                                
                                <h2 class="donation-title">Donation</h2>

                            <div class="donation-header">
                        <div class="summary-box total">
                            <div class="summary-left">
                                <span class="summary-title">Total Forms</span>
                            </div>
                            <div class="summary-right">
                                <!-- Output total forms count from PHP -->
                                <span class="summary-number"><?php echo $totalForms; ?></span>
                            </div>
                        </div>
                        <div class="summary-box accepted">
                            <div class="summary-left">
                                <span class="summary-title">Accepted Forms</span>
                            </div>
                            <div class="summary-right">
                                <!-- Output accepted forms count from PHP -->
                                <span class="summary-number"><?php echo $acceptedForms; ?></span>
                            </div>
                        </div>
                        <div class="summary-box rejected">
                            <div class="summary-left">
                                <span class="summary-title">Rejected Forms</span>
                            </div>
                            <div class="summary-right">
                                <!-- Output rejected forms count from PHP -->
                                <span class="summary-number"><?php echo $rejectedForms; ?></span>
                            </div>
                        </div>
                
                        <div class="summary-box donation">
                            <div class="summary-left">
                                <span class="summary-title">Donation Forms</span>
                            </div>
                            <div class="summary-right">
                                <!-- Output donation forms count from PHP -->
                                <span class="summary-number"><?php echo $totalDonationForms; ?></span>
                            </div>
                        </div>
                        
                        <div class="summary-box lending">
                            <div class="summary-left">
                                <span class="summary-title">Lending Forms</span>
                            </div>
                            <div class="summary-right">
                                <!-- Output lending forms count from PHP -->
                                <span class="summary-number"><?php echo $totalLendingForms; ?></span>
                            </div>
                        </div>
                    </div>



                
                                <!-- Navigation Tabs -->
                                <div class="nav-bar">
                                    <div class="tab active" id="forms-tab"onclick="showPage('forms')">Forms</div>
                                  <!--  <div class="tab " id="donators-tab" onclick="showPage('donators-records')">Donators Records</div> -->
                                 
                                    <!-- Sorting on the right -->
                                    <div class="sort-container">
                                        <label for="sort"> Sort By:</label>
                                        <select id="sort" onchange="sortTable()">
                                            <option value="date">Date</option>
                                            <option value="name">Name of Donor</option>
                                            <option value="title">Title</option>
                                            <option value="status">Status</option>
                                            <option value="transferstatus">Transfer Status</option>
                                            <option value="updated">Last Updated</option>
                                        </select>
                                    </div>

                                </div>
                                <div class="tab-head">
                                        <div class="tab-cell" id="dte">
                                            <p>Date (yyyy-mm-dd)</p>
                                        </div>
                                        <div class="tab-cell" id="nds">
                                            <p>Name of Donor/Sender</p>
                                        </div>
                                        <div class="tab-cell" id="ttl">
                                            <p>Title</p>
                                        </div>
                                        <div class="tab-cell" id="sta">
                                            <p>Status</p>
                                        </div>
                                        <div class="tab-cell" id="transta">
                                            <p>Transfer Status</p>
                                        </div>
                                        <div class="tab-cell" id="upd">
                                            <p>Updated(yyyy-mm-dd)</p>
                                        </div>
                                    </div>
                                
                                    
                                    <!-- Confirmation Modal -->
                                    
                                    <div class="modal" id="formModal" tabindex="-1" aria-labelledby="confirmationModalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="confirmationModalLabel">Confirm Transfer Status Update</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <p>Are you sure you want to update the transfer status?</p>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn-primary" id="confirmButton">Yes</button>
                                                    <button type="button" class="btn-secondary" data-bs-dismiss="modal">No</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                <!-- Scrollable Table -->
                            <div class="table-container">
                            <div class="donation-list">
                                    <?php
                                    if ($result->num_rows > 0) {
                                        while ($row = $result->fetch_assoc()) {
                                            // Assuming you have a 'form_type' column or logic to differentiate donation vs. lending
                                            $formType = isset($row['form_type']) ? $row['form_type'] : '';  // Default to empty if missing
                                            echo '<div class="donation-row" onclick="openModal(' . htmlspecialchars($row['id']) . ', \'' . htmlspecialchars($formType) . '\')">';
                                            
                                            echo '<div>' . htmlspecialchars($row['donation_date']) . '</div>'; // donation date
                                            echo '<div>' . htmlspecialchars($row['donor_name']) . '</div>'; // donor name
                                            echo '<div>' . htmlspecialchars($row['item_name']) . '</div>'; // item name
                                            echo '<div><span class="status ' . strtolower($row['status']) . '">' . htmlspecialchars($row['status']) . '</span></div>'; // status
                                            // Dropdown for transfer status
                                            echo '<div class="transfer-status">';
                                            echo '    <span class="current-status">' . htmlspecialchars($row['transfer_status']) . '</span>';
                                            echo '    <span class="dropdown-icon" onclick="toggleDropdown(this, ' . htmlspecialchars($row['id']) . ')">▼</span>';
                                            echo '    <div class="dropdown-options" id="dropdown-' . htmlspecialchars($row['id']) . '" style="display: none;">';
                                            echo '        <div class="status-option" onclick="updateTransferStatus(' . htmlspecialchars($row['id']) . ', \'Acquired\')">Acquired</div>';
                                            echo '        <div class="status-option" onclick="updateTransferStatus(' . htmlspecialchars($row['id']) . ', \'Pending\')">Pending</div>';
                                            echo '        <div class="status-option" onclick="updateTransferStatus(' . htmlspecialchars($row['id']) . ', \'Failed\')">Failed</div>';
                                            echo '    </div>';
                                            echo '</div>';

                                            echo '<div>' . htmlspecialchars($row['updated_date']) . '</div>'; // updated date
                                            echo '</div>'; // donation-row
                                        }
                                    } else {
                                        echo '<div class="no-data">No donation records found.</div>';
                                    }
                                    ?>
                                </div>

                                <div class="modal fade" id="confirmationModal" tabindex="-1" aria-labelledby="confirmationModalLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="confirmationModalLabel">Confirm Transfer Status Update</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                Are you sure you want to update the transfer status to <span id="newStatusText"></span>?
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                <button type="button" class="btn-primary" id="confirmar">Confirm</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>



                                <!-- Row Details Modal -->
                                <div class="modal fade custom-modal-width" id="rowDetailsModal" tabindex="-1" aria-labelledby="rowDetailsModalLabel" aria-hidden="true" data-bs-backdrop="static">
                                    <div class="modal-dialog modal-dialog-centered" style="width: 850px;">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="rowDetailsModalLabel">Donation Details</h5>
                                                
                                                
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                
                                                    
                                                <div id="modalContentContainer">

                                                    <h2 class= "text-muted ms-5 mt-5">INFORMATION</h2>
                                                    <div class="spacer ms-5">
                                                        <div class="container ms-1">
                                                            <div class="row">
                                                                <div>
                                                                    <div class="name-card">
                                                                        <h4 class="name-title">Name</h4>
                                                                        <div class="name-content">
                                                                            <div class="name-column">
                                                                                <!-- Dynamically updated via JavaScript -->
                                                                                <h4 class="text-down" ><?php echo $first_name; ?></h4>                                                                       
                                                                                <hr class="custom-hr">
                                                                                <h5 class="text-muted">First Name</h5>
                                                                            </div>
                                                                            <div class="name-column">
                                                                                <!-- Dynamically updated via JavaScript -->
                                                                                <h4 class="text-down" ><?php echo $last_name; ?></h4>
                                                                                <hr class="custom-hr">
                                                                                <h5 class="text-muted">Last Name</h5>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="spacer ms-5">
                                                        <div class="container ms-1">
                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <div class="name-card">
                                                                        <h4 class="name-title">Age</h4>
                                                                        <div class="name-content">
                                                                            <div class="name-column">
                                                                                <!-- Dynamically updated via JavaScript -->
                                                                                <h4 class="text-down"> <?php echo $age; ?></h4>
                                                                                <hr class="custom-hr">
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="name-card">
                                                                        <h4 class="name-title">Sex</h4>
                                                                        <div class="name-content">
                                                                            <div class="name-column">
                                                                                <!-- Dynamically updated via JavaScript -->
                                                                                <h4 class="text-down"> <?php echo $sex; ?></h4>
                                                                                <hr class="custom-hr">
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="spacer ms-5">
                                                        <div class="container ms-1">
                                                            <div class="row">
                                                                <div>
                                                                    <div class="name-card">
                                                                        <h4 class="name-title">Email</h4>
                                                                        <div class="name-content">
                                                                            <div class="name-column">
                                                                                <!-- Dynamically updated via JavaScript -->
                                                                                <h4 class="text-down" ><?php echo $email; ?></h4>                                                                        
                                                                                <hr class="custom-hr">
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="spacer ms-5">
                                                        <div class="container ms-1">
                                                            <div class="row">
                                                                <div>
                                                                    <div class="name-card">
                                                                        <h4 class="name-title">Phone Number</h4>
                                                                        <div class="name-content">
                                                                            <div class="name-column">
                                                                                <!-- Dynamically updated via JavaScript -->
                                                                                <h4 class="text-down" ><?php echo $phone; ?></h4>                                                                        
                                                                                <hr class="custom-hr">
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>    
                                                    
                                                    <div class="spacer ms-5">
                                                        <div class="container ms-1">
                                                            <div class="row">
                                                                <div>
                                                                    <div class="name-card">
                                                                        <h4 class="name-title">Organization</h4>
                                                                        <div class="name-content">
                                                                            <div class="name-column">
                                                                                <!-- Dynamically updated via JavaScript -->
                                                                                <h4 class="text-down" ><?php echo $org; ?></h4>                                                                        
                                                                                <hr class="custom-hr">
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>


                                                    <h2 class= "text-muted ms-5 mt-5">Address</h2>

                                                    <div class="spacer ms-5">
                                                        <div class="container ms-1">
                                                            <div class="row">
                                                                <div>
                                                                <div class="name-card">
                                                                    <h4 class="name-title">Location Details</h4>
                                                                    <div class="name-content">
                                                                                <!-- Dynamically updated via JavaScript -->
                                                                    <div class="name-column">
                                                                            <h4 class="text-down"><?php echo $province; ?></h4>
                                                                                        <hr class="custom-hr">
                                                                            <h5 class="text-muted">Province</h5>
                                                                        </div> 
                                                                        <div class="name-column">
                                                                                        
                                                                            <h4 class="text-down"><?php echo $city; ?></h4>
                                                                            <hr class="custom-hr">
                                                                            <h5 class="text-muted">City</h5>
                                                                        </div>
                                                                        <div class="name-column">
                                                                                    
                                                                            <h4 class="text-down"><?php echo $brgy; ?></h4>
                                                                            <hr class="custom-hr">
                                                                            <h5 class="text-muted">Barangay</h5>
                                                                        </div> 
                                                                        <div class="name-column">
                                                                                    
                                                                            <h4 class="text-down"><?php echo $street; ?></h4>
                                                                            <hr class="custom-hr">
                                                                            <h5 class="text-muted">Street</h5>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <h2 class= "text-muted ms-5 mt-5">About the Artifact</h2>

                                                    <div class="spacer ms-5">
                                                        <div class="container ms-1">
                                                            <div class="row">
                                                                <div >
                                                                    <div class="name-card">
                                                                        <h4 class="name-title">Title/Name of the Artifact</h4>
                                                                        <div class="name-content">
                                                                            <div class="name-column">
                                                                                <!-- Dynamically updated via JavaScript -->
                                                                                <h4 class="text-down" ><?php echo $title; ?></h4>                                                                        
                                                                                <hr class="custom-hr">
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="spacer ms-5">
                                                        <div class="container ms-1">
                                                            <div class="row">
                                                                <div >
                                                                    <div class="name-card">
                                                                        <h4 class="name-title">Artifact Description</h4>
                                                                        <div class="name-content">
                                                                            <div class="name-column">
                                                                                <!-- Dynamically updated via JavaScript -->
                                                                                <h4 class="text-down" ><?php echo $artifact_description; ?></h4>                                                                        
                                                                                <hr class="custom-hr">
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="spacer ms-5">
                                                        <div class="container ms-1">
                                                            <div class="row">
                                                                <div>
                                                                    <div class="name-card">
                                                                        <h4 class="name-title">How and where the artifact is acquired</h4>
                                                                        <div class="name-content">
                                                                            <div class="name-column">
                                                                                <!-- Dynamically updated via JavaScript -->
                                                                                <h4 class="text-down" ><?php echo $acquisition_details; ?></h4>                                                                        
                                                                                <hr class="custom-hr">
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="spacer ms-5">
                                                        <div class="container ms-1">
                                                            <div class="row">
                                                                <div >
                                                                    <div class="name-card">
                                                                        <h4 class="name-title">Information about the artifact that <br> the museum should know</h4>
                                                                        <div class="name-content">
                                                                            <div class="name-column">
                                                                                <!-- Dynamically updated via JavaScript -->
                                                                                <h4 class="text-down" ><?php echo $additional_info; ?></h4>                                                                        
                                                                                <hr class="custom-hr">
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="spacer ms-5">
                                                        <div class="container ms-1">
                                                            <div class="row">
                                                                <div >
                                                                    <div class="name-card">
                                                                        <h4 class="name-title">Brief narrative or story related to the artifact</h4>
                                                                        <div class="name-content">
                                                                            <div class="name-column">
                                                                                <!-- Dynamically updated via JavaScript -->
                                                                                <h4 class="text-down" ><?php echo $narrative; ?></h4>                                                                        
                                                                                <hr class="custom-hr">
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="spacer ms-5">
                                                        <div class="container ms-1">
                                                            <div class="row">
                                                                <div>
                                                                <div class="name-card">
                                                                    <h4 class="name-title">Image/s of the artifact</h4>
                                                                    <div class="name-content">
                                                                        <div class="name-column">
                                                                            <div class="image-gallery">
                                                                            <img src="<?php echo $img; ?>" alt="Donor Image" height="auto" width="150" class="artifact-image">
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="spacer ms-5">
                                                        <div class="container ms-1">
                                                            <div class="row">
                                                                <div>
                                                                    <div class="name-card">
                                                                        <h4 class="name-title">Relevant documentation or research about the artifact?</h4>
                                                                        <div class="name-content">
                                                                            <div class="name-column">
                                                                                <div class="file-holder">
                                                                                    <i class="fas fa-file-pdf"></i> <!-- PDF Icon -->
                                                                                    <p class="file-name text-down">Document_Name.pdf</p>
                                                                                    <p class="file-info text-down">Uploaded on: January 1, 2023</p>
                                                                                </div>
                                                                                <hr class="custom-hr">
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="spacer ms-5">
                                                        <div class="container ms-1">
                                                            <div class="row">
                                                                <div>
                                                                <div class="name-card">
                                                                    <h4 class="name-title">Related image/s about the artifact.</h4>
                                                                    <div class="name-content">
                                                                        <div class="name-column">
                                                                            <div class="image-gallery">
                                                                            <img src="<?php echo $img; ?>" alt="Donor Image" height="auto" width="150" class="artifact-image">
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <h2 class="text-strong ms-5 mt-5">Accept Request?</h2>
                                                        <div class="container ms-5 mt-3">
                                                            <button type="button" class="btn-yes">Yes</button>
                                                            <button type="button" class="btn-no">No</button>
                                                        </div>

                                                        <div class="container my-5 ms-5">
                                                            <div class="row">
                                                                <div class="col-md-8">
                                                                    <div class="p-4 border rounded border-2 border-dark opacity-100 bg-white">
                                                                        <h4>Note</h4>
                                                                        <p>Good morning Juan,</p>
                                                                        <p>I want to express my sincere gratitude for taking the time to complete the form. We truly
                                                                            appreciate your effort and generosity.</p>
                                                                        <p>We are honored to accept your donation and are committed to preserving the artifact with the
                                                                            utmost care. I will reach out to you later to discuss important details, such as the transfer
                                                                            process and the signing of the contract.</p>
                                                                        <p>Thank you once again, and I look forward to our upcoming conversation.</p>
                                                                        <p>Best regards,<br>[name]<br>[position]</p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="container me-3">
                                                        <button type="button" class="btn-yes">Submit</button>
                                                        </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>



                            </div>
                    </div>
                    <!--DONATORS RECORD -->
                    <div></div>
                </div>    
        </div>
    </div>    
                    
        

    <script src="script.js"></script>
    

</body>

</html>