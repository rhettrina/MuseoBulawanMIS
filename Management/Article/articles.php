<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.3.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Hind:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Articles</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
        <link rel="stylesheet" href="article.css">

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
                    <button onclick="location.href='../../Management/Article/articles.php'" class="article-button active">
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

            <?php
            include "article_db.php";
            $sql = "SELECT * FROM articles";
            $result = $conn->query($sql);

            $total_articles = $result->num_rows;
            ?>

            <div class="main-content">
                <div class="head">
                    <h2>Articles</h2>
                </div>
                
                <div class="head-container">
                    <div class="article-counter">
                        <div class="counter-left">
                            <div class="title-container">
                                <label for="total-articles">Toal<br>Articles</label>
                            </div>
                        </div>
                        <div class="counter-right">
                            <div class="counter-container">
                                <span id="total-articles"><?php echo $total_articles; ?></span>
                            </div>
                        </div>
                    </div>
                    <div>
                        <a href="./builder.html">
                            <button type="button" class="btn btn-light border-black">Create Article <i class="bi bi-plus-square"></i></button>
                        </a>
                    </div>
                </div>
                <div class="tool-bar">
                    <div class="sort-container">
                        <label for="sort"> By Date:</label>
                            <select id="sort">
                            <option value="newest">Newest</option>
                            <option value="oldest">Oldest</option>
                        </select>
                    </div>
                </div>
                
                <div class="table-head">
                    <div class="head-block">
                        <p>Date (yyyy-mm-dd)</p>
                        </div>
                        <div class="head-block">
                            <p>Title</p>
                        </div>
                        <div class="head-block">
                            <p>Type</p>
                        </div>
                        <div class="head-block">
                            <p>Last Updated (yyyy-mm-dd)</p>
                        </div>
                        <div class="head-block">
                            <p>Action Buttons</p>
                    </div>
                </div>
                <div class="article-table">
                    
                </div> 
               
                

            </div>


            <script src="articles.js"></script>

</body>

</html>