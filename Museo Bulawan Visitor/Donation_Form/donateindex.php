


<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donation Form</title>

    <!-- Fonts -->
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
    <link href="donatestyle.css" rel="stylesheet">

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>

</head>


<body>
    <div class="wrapper">
        <div id="container1">
            <nav class="navbar navbar-expand-lg navbar-dark me-3">
                <p id="open">Open Daily 9:00am-5:00pm, Monday- Friday, Close During Holidays</p>

                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#morebtn"
                    aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="fa-solid fa-caret-down"></span>
                </button>

                <div class="collapse navbar-collapse" id="morebtn">



                    <ul class="navbar-nav ms-auto px-4">
                        <li class="nav-item">
                            <a class="nav-link" href="">About Us</a>
                        </li>
                        <li class="nav-item"></li>
                        <a class="nav-link" href="">Visit Us</a>
                        </li>
                        <li class="nav-item"></li>
                        <a class="nav-link" href="">Log In</a>
                        </li>
                    </ul>
                </div>
            </nav>
        </div>
    </div>

    <div class="wrapperwhite mt-3">
        <div id="container2">
            <nav class="navbar navbar-expand-lg navbar-light">
                <div class="logowrapper">
                    <img src="LOGO.png" alt="" id="logo">
                    <div class="vr border border-4 ms-5 border-dark opacity-100" style="height: 85;"></div>
                    <a id="brand" class="navbar-brand text-start" href="">Museo<br>Bulawan</a>
                </div>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#burgerbtn"
                    aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="burgerbtn">



                    <ul class="hovering navbar-nav fs-5 gap-3">
                        <li class="nav-item">
                            <a class="nav-link" href="">Home</a>

                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="">Collections</a>

                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="">News and Events</a>

                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="">About Us</a>

                        </li>
                    </ul>
                    <button class="btn ms-auto"><img src="search.png" alt="" id="searchbtn"></button>
                </div>




            </nav>
            <hr class="border border-1 border-dark opacity-100">
        </div>
    </div>

    <div class="donationtitle container mt-4 mx-0">
        <h1 class="ms-5 py-3">Donation Form</h1>
    </div>
    <form action="process_donation.php" method="POST" enctype="multipart/form-data" id="artifactForm">
    <input type="hidden" name="formType" value="donation"> <!-- Valid example with a set value -->

        <div class="intro container p-5 mt-4">
            <p>In addition to preserving your historic objects it is important to remember to preserve the history or
                story that goes with them. For example, the uniform worn by your great grand father is just a uniform if
                the story is lost. Take the time to write down the story that goes with your objects; include any
                background details that would help our team understand the significance of the item.</p>

            <h3>Tell us about yourself.</h3>
                
            <!-- Name Section -->
            <div class="form-group row align-items-center mt-4 gap-1">
                <label for="firstName" class="col-sm-2 col-form-label fs-5"><b>Name</b> <span
                        class="text-danger">*</span></label>
                <div class="col-sm-4">
                    <input type="text" class="form-control border-2 border-dark" id="firstName" name="firstName" placeholder="First Name" required>
                </div>
                <div class="col-sm-5">
                    <input type="text" class="form-control border-2 border-dark" id="lastName" name="lastName" placeholder="Last Name" required>

                </div>
            </div>

            <!-- Age and Gender Section -->
            <div class="form-group row align-items-center mt-4">
                <label for="age" class="col-sm-2 col-form-label fs-5"><b>Age</b> <span
                        class="text-danger">*</span></label>
                <div class="col-sm-2">
                    <input type="number" class="form-control border-2 border-dark" id="age" name="age" placeholder="Age" min="0" required>
                </div>
                <div class="col-2">

                </div>
                <label class="col-sm-1 col-form-label fs-5"><b>Sex</b></label>

                <div class="col-sm-4">
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="sex" id="male" value="male">
                        <label class="form-check-label" for="male">Male</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="sex" id="female" value="female">
                        <label class="form-check-label" for="female">Female</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="sex" id="others" value="others">
                        <label class="form-check-label" for="others">Others</label>
                    </div>

                </div>

            </div>

            <!-- Contact Information Section -->
            <div class="form-group row align-items-center mt-4">
                <label for="email" class="col-sm-2 col-form-label fs-5"><b>Email</b> <span
                        class="text-danger">*</span></label>
                <div class="col-sm-5">
                    <input type="email" class="form-control border-2 border-dark" id="email" name="email" placeholder="example@gmail.com" required>
                </div>
                <label for="phone" class="col-sm-2 col-form-label fs-5"><b>Phone Number</b> <span
                        class="text-danger">*</span></label>
                <div class="col-sm-2">
                    <input type="tel" class="form-control border-2 border-dark" id="phone" name="phone" placeholder="+6309123456789"
                        required>
                </div>
            </div>

            <!-- Organization Section -->
            <div class="form-group row align-items-center mt-4">
                <label for="organization" class="col-sm-2 col-form-label fs-5"><b>Organization</b></label>
                <div class="col-sm-5">
                    <input type="text" class="form-control border-2 border-dark" id="organization" name="organization"
                        placeholder="School/Institution/etc">
                </div>
            </div>

            <!-- Address Section -->
            <h3 class="mt-4">Where do you live.</h3>
            <div class="form-group row align-items-center mt-4">
                <label for="province" class="col-sm-2 col-form-label fs-5"><b>Province</b> <span
                        class="text-danger">*</span></label>
                <div class="col-sm-4">
                    <select class="form-control border-2 border-dark" id="province" name="province" required>
                        <option selected disabled>Select Province</option>
                        <option>Camarines Norte</option>
                        <option>Province 1</option>
                        <option>Province 2</option>
                        <option>Province 3</option>
                    </select>
                </div>
                <label for="city" class="col-sm-1 col-form-label fs-5"><b>City</b> <span
                        class="text-danger">*</span></label>
                <div class="col-sm-4">
                    <input type="text" class="form-control border-2 border-dark" id="city" name="city" placeholder="City" required>
                </div>
            </div>
            <div class="form-group row align-items-center mt-4">
                <label for="barangay" class="col-sm-2 col-form-label fs-5"><b>Barangay</b> <span
                        class="text-danger">*</span></label>
                <div class="col-sm-4">
                    <input type="text" class="form-control border-2 border-dark" id="barangay" name="barangay" placeholder="Barangay"
                        required>
                </div>
                <label for="street" class="col-sm-1 col-form-label fs-5"><b>Street</b></label>
                <div class="col-sm-4">
                    <input type="text" class="form-control border-2 border-dark" id="street" name="street" placeholder="Street">
                </div>
            </div>



        </div>

        <div class="intro container p-5 my-4">
            <h3>About the artifact.</h3>

            <div class="form-group row align-items-center  mt-4">
                <label for="artifactTitle" class="col-sm-3 col-form-label fs-5"><b>Title/Name of the Artifact.</b> <span
                        class="text-danger">*</span></label>
                <div class="col-sm-9">
                    <input type="text" class="form-control border-2 border-dark" id="artifactTitle" name="artifactTitle" placeholder="Title/Name of the Artifact" required>

                </div>
            </div>

            <div class="form-group row align-items-center mt-4">
                <label for="artifactDescription" class="col-sm-3 col-form-label fs-5"><b>Artifact Description</b> <span
                        class="text-danger">*</span></label>
                <div class="col-sm-9">
                    <textarea class="form-control border-2 border-dark" id="artifactDescription" name="artifactDescription" placeholder="Artifact Description" required></textarea>
                </div>
            </div>

            <div class="form-group row align-items-center mt-4">
                <label for="acquisition" class="col-sm-3 col-form-label fs-5"><b>How and where did you acquire the
                        artifact?</b> <span class="text-danger">*</span></label>
                <div class="col-sm-9">
                    <input type="text" class="form-control border-2 border-dark" id="acquisition" name="acquisition"
                        placeholder="Acquisition Details" required>
                </div>
            </div>

            <div class="form-group row align-items-center mt-4">
                <label for="additionalInfo" class="col-sm-3 col-form-label fs-5"><b>Additional Information</b></label>
                <div class="col-sm-9">
                    <textarea class="form-control border-2 border-dark" id="additionalInfo" name="additionalInfo"
                        placeholder="Is there any other information about the artifact that the museum should know?"></textarea>
                </div>
            </div>

            <div class="form-group row align-items-center mt-4">
                <label for="narrative" class="col-sm-3 col-form-label fs-5"><b>Brief Narrative or Story</b></label>
                <div class="col-sm-9">
                    <textarea class="form-control border-2 border-dark" id="narrative" name="narrative"
                        placeholder="Would you like to provide a brief narrative or story related to the artifact?"></textarea>
                </div>
            </div>

            <div class="form-group row align-items-center mt-5 justify-content-between">
                <label for="artifactImages" class="col-sm-3 col-form-label fs-5"><b>Images of the artifact</b> <span
                        class="text-danger">*</span></label>
                <div class="col-sm-2"></div>
                <div class="col-sm-6">
                    <input type="text" class="form-control border-2 border-dark mb-2" id="artifactImages" name="artifactImages"
                        placeholder="Paste Link" required>
                    <input type="file" class="form-control border-2 border-dark" id="artifactFiles" name="artifact_img" multiple>
                </div>
            </div>

            <div class="form-group row align-items-center mt-4 justify-content-between">
                <div class="col-sm-5">
                    <label for="documentation" class="col-form-label fs-5"><b>Relevant Documentation</b></label>
                    <p>Such as Provenance Documents, Historical Records, Research Papers, Certificates of Authenticity,
                        Catalogs or Inventories.</p>
                </div>
                <div class="col-sm-6">
                    <input type="text" class="form-control border-2 border-dark mb-2" id="documentation" name="documentation"
                        placeholder="Paste Link">
                    <input type="file" class="form-control border-2 border-dark" id="documentationFiles" 
                    name="documentation_img" multiple>
                </div>
            </div>

            <div class="form-group row align-items-center mt-4 justify-content-between">
                <div class="col-sm-5">
                    <label for="relatedImages" class="col-form-label fs-5"><b>Any related images</b></label>
                    <p>For example an image of your grandfather wearing the clothes, of image of an artifact while being
                        used.</p>
                </div>
                <div class="col-sm-6">
                    <input type="text" class="form-control border-2 border-dark mb-2" id="relatedImages" name="relatedImages"
                        placeholder="Paste Link">
                    <input type="file" class="form-control border-2 border-dark" id="relatedFiles" name="related_img" multiple>
                </div>
            </div>
            <div class="col-md-12 d-flex justify-content-end mt-5">
                <button type="submit" class="btn btn-light px-5 py-2" onclick="openModal()" >Submit</button>
            </div>
            </div>
   
           
        </form> 

    <!-- Modal Structure -->
    <div id="confirmationModal" class="modal">
        <div class="modal-content">
        <p>Are you sure you want to submit this form?</p>
        <button onclick="confirmSubmission()">Yes</button>
        <button onclick="closeModal()">No</button>
        </div>
    </div>

    <div class="wrapper">

        <div class="lowercontainer ms-5 pt-4">
            <div class="row d-flex">
                <div class="col-12">
                    <ul class="nav-links">
                        <li><a class="text-decoration-none" href="">Home</a></li>
                        <div class="vr border border-1 border-light opacity-100" style="height: 40px;"></div>
                        <li><a class="text-decoration-none" href="">Collections</a></li>
                        <div class="vr border border-1 border-light opacity-100" style="height: 40px;"></div>
                        <li><a class="text-decoration-none" href="">News & Events</a></li>
                        <div class="vr border border-1 border-light opacity-100" style="height: 40px;"></div>
                        <li><a class="text-decoration-none" href="">About Us</a></li>
                    </ul>
                </div>
            </div>
        </div>



        <hr class="border border-1 border-light opacity-100 mx-4">

        <div class="side-footer mx-5">
            <div class="row d-flex justify-content-between">
                <div class="col-lg-7 col-sm-12">
                    <div class="museo-bulawan-cmp d-flex justify-content-start">
                        <img src="LOGO.png" alt="">
                        <h2>Museo Bulawan</h2>
                    </div>
                    <p class="lead">Helping us raise awareness regarding Camnortenos identity is crucial, and
                        your
                        support can make a significant difference.</p>
                    <div class="container-fluid d-flex flex-wrap p-0 pb-4">
                        <p class="me-3">We gratefully accept donations or just lending of your artifact will greatly
                            help us.
                        </p>
                        <button type="button" class="btn btn-light btn-lg">Support Us</button>
                    </div>
                </div>

                <div class="col-lg-2 col-sm-12">
                    <nav class="navbar">
                        <ul class="navbar-nav text-start navbar-dark">
                            <li class="nav-item">
                                <a class="nav-link" href=""><b>Visit Us</b></a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="">About Us</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="">Book a Tour</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="">Contact Us</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="">Map View</a>
                            </li>
                        </ul>
                    </nav>
                </div>

                <div class="col-lg-3 col-sm-12 mt-2">
                    <h5>Opening Hours</h5>
                    <p>Camarines Norte Provincial Capitol Grounds,
                        Daet Philippines</p>
                    <p>Open Daily 9:00am-5:00pm,
                        Monday- Friday,</p>
                </div>

            </div>
        </div>
    </div>
<<script src="script.js"></script>
</body>

</html>