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
                unloadCurrentView(); // Unload the current view

                content.innerHTML = html; // Dynamically load the content
                
                // Update the active tab
                updateActiveTab(page);

                // Wait for content to be rendered before loading the script
                setTimeout(() => {
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
                cleanupFunctions[page] = cleanup; // Register cleanup function
            }
        };

        script.onerror = () => {
            console.error(`Failed to load ${page}.js`);
        };

        document.body.appendChild(script);
        currentScript = script; // Track the current script
    }

    function unloadCurrentView() {
        // Call the cleanup function if available
        const page = localStorage.getItem("currentPage");
        if (cleanupFunctions[page]) {
            try {
                cleanupFunctions[page](); // Execute the cleanup logic
                console.log(`${page} cleanup executed`);
            } catch (error) {
                console.error(`Error during cleanup of ${page}:`, error);
            }
            delete cleanupFunctions[page]; // Remove cleanup reference
        }

        // Remove the current script
        if (currentScript) {
            document.body.removeChild(currentScript);
            currentScript = null;
        }

        // Clear dynamic content
        content.innerHTML = "";
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

    // Modal functionality (optional)
    const notificationButton = document.getElementById("notification-button");
    const notificationModal = document.getElementById("notification-modal");
    const closeNotificationButton = document.getElementById("close-notification-button");

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

    // Logout functionality
    const logoutButton = document.getElementById("logout-button");
    logoutButton.addEventListener("click", () => {
        localStorage.removeItem("currentPage");
        window.location.href = "https://museobulawan.online/development/Museo%20Bulawan%20Visitor/admin_login/login.html";
    });
});
