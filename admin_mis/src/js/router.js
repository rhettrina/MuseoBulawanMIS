document.addEventListener("DOMContentLoaded", () => {
    const links = document.querySelectorAll(".nav-link");
    const content = document.getElementById("content");
    let currentScript = null; // Keep track of the currently loaded script
    let cleanupFunctions = {}; // Store cleanup functions for specific pages

    const savedPage = localStorage.getItem("currentPage") || "dashboard";

    function loadContent(page) {
        if (typeof window.cleanup === "function") {
            console.log("Calling cleanup for current page...");
            window.cleanup(); // Call the page's cleanup function
            window.cleanup = null; // Reset cleanup for the next page
        }
    
        fetch(`src/views/${page}.html`)
            .then((response) => {
                if (!response.ok) {
                    throw new Error(
                        `Error loading page: ${response.statusText} (status code: ${response.status})`,
                    );
                }
                return response.text();
            })
            .then((html) => {
                content.innerHTML = html;
                updateActiveTab(page);
    
                unloadScript(); // Remove the previous page script
                loadScript(page);
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

            // Call the page-specific initialization if it exists
            if (typeof init === "function") {
                init();
            }

            // Check if the current page requires floorplans
            if (page === "floorplans" && typeof fetchAndDisplayFloorplans === "function") {
                fetchAndDisplayFloorplans();
            }

            // Optionally store cleanup functions for the page
            if (typeof cleanup === "function") {
                cleanupFunctions[page] = cleanup;
            }
        };
        script.onerror = () => {
            console.error(`Failed to load ${page}.js`);
        };

        document.body.appendChild(script);
        currentScript = script; // Keep track of the current script
    }

    function unloadScript() {
        if (currentScript) {
            console.log(`Unloading script: ${currentScript.src}`);
            document.body.removeChild(currentScript);
            currentScript = null;
        }
    }

    function updateActiveTab(activePage) {
        links.forEach((link) => {
            const page = link.getAttribute("data-page");
            if (activePage === page) {
                link.parentNode.classList.add("active");
            } else {
                link.parentNode.classList.remove("active");
            }
        });
    }

    loadContent(savedPage);

    updateActiveTab(savedPage);

    links.forEach((link) => {
        link.addEventListener("click", (e) => {
            e.preventDefault();

            const page = link.getAttribute("data-page");

            loadContent(page);

            localStorage.setItem("currentPage", page);

            updateActiveTab(page);
        });
    });

    const logoutButton = document.getElementById("logout-button");
    logoutButton.addEventListener("click", () => {
        localStorage.removeItem("currentPage");
        window.location.href = "\Museo Bulawan Visitor\admin_login\login.html";
    });

    const notificationButton = document.getElementById('notification-button');
    const notificationModal = document.getElementById('notification-modal');
    const closeNotificationButton = document.getElementById('close-notification');
    const notificationList = document.getElementById('notification-list');
    const notificationCounter = document.getElementById('notifaction-counter');

    // Sample notifications
    const notifications = [
        'New comment on your post',
        'New follower',
        'Your article was published',
    ];

    // Handle notification button click (Show modal)
    notificationButton.addEventListener('click', function() {
        // Clear notification counter
        notificationCounter.textContent = '';

        // Show the notification modal
        notificationModal.classList.remove('hidden');

        // Populate the notification list
        notificationList.innerHTML = ''; // Clear existing notifications
        notifications.forEach(notification => {
            const listItem = document.createElement('li');
            listItem.classList.add('text-sm', 'text-gray-700');
            listItem.textContent = notification;
            notificationList.appendChild(listItem);
        });
    });

    // Close the notification modal when the close button is clicked
    closeNotificationButton.addEventListener('click', function() {
        notificationModal.classList.add('hidden');
    });

    // Close the modal when clicking outside of it
    notificationModal.addEventListener('click', function(event) {
        if (event.target === notificationModal) {
            notificationModal.classList.add('hidden');
        }
    });
});

function toggleSidebar() {
    const sidebar = document.getElementById("sidebar");
    sidebar.classList.toggle("hidden");
}
