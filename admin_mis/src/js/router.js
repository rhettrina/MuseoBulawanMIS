document.addEventListener("DOMContentLoaded", () => {
    const links = document.querySelectorAll(".nav-link");
    const content = document.getElementById("content");
    let currentScript = null;
    let cleanupFunctions = {}; // Store cleanup functions

    const savedPage = localStorage.getItem("currentPage") || "dashboard";

    function loadContent(page) {
        fetch(`src/views/${page}.html`)
            .then((response) => {
                if (!response.ok) {
                    throw new Error(`Error loading page: ${response.statusText}`);
                }
                return response.text();
            })
            .then((html) => {
                content.innerHTML = html; // Dynamically load the content
    
                // Update the active tab
                updateActiveTab(page);
    
                // Wait for content to be rendered before loading the script
                setTimeout(() => {
                    unloadScript();
                    loadScript(page);
                }, 0); // Adjust delay if necessary
            })
            .catch((error) => {
                content.innerHTML = `<p class="text-red-500">Error: ${error.message}</p>`;
                console.error("Content Load Error:", error);
            });
    }
    

    function loadScript(page) {
        const script = document.createElement("script");
        script.src = `src/js/${page}.js`;

        script.onload = () => {
            console.log(`${page}.js loaded successfully`);
            if (typeof init === "function") {
                init(); // Initialize page-specific functionality
            }
            if (typeof cleanup === "function") {
                cleanupFunctions[page] = cleanup;
            }
        };

        script.onerror = () => {
            console.error(`Failed to load ${page}.js`);
        };

        document.body.appendChild(script);
        currentScript = script; // Track the current script
    }

    function unloadScript() {
        if (currentScript) {
            document.body.removeChild(currentScript);
            currentScript = null;
        }
    }

    function updateActiveTab(activePage) {
        links.forEach((link) => {
            const page = link.getAttribute("data-page");
            if (page === activePage) {
                link.classList.add("active");
            } else {
                link.classList.remove("active");
            }
        });
    }
    

    // Load the initial view
    loadContent(savedPage);

    links.forEach((link) => {
        link.addEventListener("click", (e) => {
            e.preventDefault();
            const page = link.getAttribute("data-page");

            // Load the selected view and save the state
            loadContent(page);
            localStorage.setItem("currentPage", page);
        });
    });

    
    

    if (notificationButton && notificationModal && closeNotificationButton) {
        notificationButton.addEventListener("click", () => {
            notificationModal.classList.remove("hidden");
        });

        closeNotificationButton.addEventListener("click", () => {
            notificationModal.classList.add("hidden");
        });

        // Optionally handle closing the modal on outside click
        notificationModal.addEventListener("click", (event) => {
            if (event.target === notificationModal) {
                notificationModal.classList.add("hidden");
            }
        });
    }



    // Additional functionality (e.g., logout)
    const logoutButton = document.getElementById("logout-button");
    logoutButton.addEventListener("click", () => {
        localStorage.removeItem("currentPage");
        window.location.href = "admin_login/login.html";
    });




});
