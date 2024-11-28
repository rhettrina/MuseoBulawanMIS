document.addEventListener("DOMContentLoaded", () => {
    const links = document.querySelectorAll(".nav-link");
    const content = document.getElementById("content");
    let currentScript = null; // Keep track of the currently loaded script
    let cleanupFunctions = {}; // Store cleanup functions for specific pages

    const savedPage = localStorage.getItem("currentPage") || "dashboard";
 function loadContent(page) {
    unloadScript(); // Reset before loading new content

    fetch(`src/views/${page}.html`)
        .then((response) => {
            if (!response.ok) {
                throw new Error(
                    `Error loading page: ${response.statusText} (status code: ${response.status})`
                );
            }
            return response.text();
        })
        .then((html) => {
            content.innerHTML = html;

            // Verify if the content element exists in the DOM
            if (!content) {
                console.error("Error: 'content' element is missing in the DOM.");
                return;
            }

            updateActiveTab(page);

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
    
            // Safely call page-specific initialization
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
    
    function displayError(targetId, errorMessage) {
        const targetElement = document.getElementById(targetId);
        if (targetElement) {
            targetElement.innerText = errorMessage;
        } else {
            console.error(`Error: Target element with ID '${targetId}' not found.`);
        }
    }
    
    // Example usage in page-specific logic
    function populateTotalAppointmentData() {
        try {
            const totalAppointmentsElement = document.getElementById("total-appointments");
            if (!totalAppointmentsElement) {
                throw new Error("Total appointments element not found.");
            }
            totalAppointmentsElement.innerText = "42"; // Example value
        } catch (error) {
            console.error("Error fetching total appointments:", error);
            displayError("error-message", "Failed to load total appointments data.");
        }
    }
    
    function displayAppointmentErrorMessages(message) {
        const errorContainer = document.getElementById("appointment-error");
        if (errorContainer) {
            errorContainer.innerText = message;
        } else {
            console.error("Error: Appointment error container not found.");
        }
    }
    

    function unloadScript() {
        // Clear dynamically added elements from the content area
        if (content) {
            console.log("Clearing content...");
            content.innerHTML = ""; // Remove all child elements in the content area
        }
    
        // Remove the current script if loaded
        if (currentScript) {
            console.log(`Unloading script: ${currentScript.src}`);
            document.body.removeChild(currentScript);
            currentScript = null;
        }
    
        // Reset global states and cleanup functions
        if (typeof window.cleanup === "function") {
            console.log("Calling cleanup for global state...");
            window.cleanup(); // Call the global cleanup function
            window.cleanup = null; // Clear it to avoid accidental reuse
        }
    
        // Clear any other DOM elements or event listeners specific to the previous view
        Object.keys(cleanupFunctions).forEach((page) => {
            if (typeof cleanupFunctions[page] === "function") {
                console.log(`Calling cleanup for page: ${page}`);
                cleanupFunctions[page]();
            }
        });
    
        cleanupFunctions = {}; // Reset cleanup functions
    
        console.log("State and DOM reset complete.");
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
        window.location.href = "https://museobulawan.online/development/Museo%20Bulawan%20Visitor/admin_login/login.html";
    });

    // Manage notifications
    const notificationButton = document.getElementById("notification-button");
    const notificationModal = document.getElementById("notification-modal");
    const notificationList = document.getElementById("notification-list");
    const closeNotificationButton = document.getElementById("close-notification");
    const notificationCounter = document.getElementById("notification-counter");

    // Notifications array
    let notifications = [];

    // Function to fetch notifications from the backend API
    async function fetchNotifications() {
        try {
            const response = await fetch("https://example.com/api/notifications");
            if (!response.ok) {
                throw new Error(`Error fetching notifications: ${response.statusText}`);
            }
            const data = await response.json();

            // Map the fetched data to the notifications array
            notifications = data.map(item => ({
                type: item.type,
                from: item.from,
                title: item.title,
                expDate: item.expDate,
                date: item.date,
                time: item.time,
            }));

            updateNotificationCounter(); // Update the counter after fetching
            loadCalendar(); // Reload the calendar to reflect new notifications
        } catch (error) {
            console.error("Failed to fetch notifications:", error);
            
            
        }
    }

    // Function to update the notification counter
    function updateNotificationCounter() {
        if (notificationCounter) {
            const count = notifications.length;
            notificationCounter.textContent = count > 0 ? count : '';
        }
    }

    // Call fetchNotifications initially to load notifications
    fetchNotifications();

    // Open the notification modal
    notificationButton.addEventListener("click", function () {
        // Clear the notification counter
        if (notificationCounter) {
            notificationCounter.textContent = '';
        }

        // Show the modal
        notificationModal.classList.remove("hidden");

        // Populate notifications dynamically
        notificationList.innerHTML = ''; // Clear existing notifications
        notifications.forEach(notification => {
            const listItem = document.createElement('li');
            listItem.classList.add('p-3', 'bg-gray-200', 'rounded-md', 'mb-2');

            const typeElement = document.createElement('p');
            typeElement.classList.add('font-semibold');
            typeElement.textContent = notification.type;
            listItem.appendChild(typeElement);

            if (notification.from) {
                const fromElement = document.createElement('p');
                fromElement.classList.add('text-sm', 'text-gray-600');
                fromElement.textContent = `From: ${notification.from}`;
                listItem.appendChild(fromElement);
            }

            if (notification.title) {
                const titleElement = document.createElement('p');
                titleElement.classList.add('text-sm', 'text-gray-600');
                titleElement.textContent = `Title: ${notification.title}`;
                listItem.appendChild(titleElement);
            }

            if (notification.expDate) {
                const expDateElement = document.createElement('p');
                expDateElement.classList.add('text-sm', 'text-gray-600');
                expDateElement.textContent = `Exp Date: ${notification.expDate}`;
                listItem.appendChild(expDateElement);
            }

            if (notification.date) {
                const dateElement = document.createElement('p');
                dateElement.classList.add('text-sm', 'text-gray-600');
                dateElement.textContent = `Date: ${notification.date}`;
                listItem.appendChild(dateElement);
            }

            if (notification.time) {
                const timeElement = document.createElement('p');
                timeElement.classList.add('text-sm', 'text-gray-600');
                timeElement.textContent = `Time: ${notification.time}`;
                listItem.appendChild(timeElement);
            }

            notificationList.appendChild(listItem);
        });
    });

    // Close the modal on close button click
    closeNotificationButton.addEventListener("click", function () {
        notificationModal.classList.add("hidden");
    });

    // Close modal on clicking outside the content
    notificationModal.addEventListener("click", function (event) {
        if (event.target === notificationModal) {
            notificationModal.classList.add("hidden");
        }
    });

    // Calendar Logic
    const calendarMonth = document.getElementById("calendar-month");
    const calendarDays = document.getElementById("calendar-days");

    const today = new Date();
    let currentMonth = today.getMonth();
    let currentYear = today.getFullYear();

    const monthNames = [
        "January", "February", "March", "April", "May", "June",
        "July", "August", "September", "October", "November", "December"
    ];

    function loadCalendar() {
        calendarMonth.textContent = `${monthNames[currentMonth]} ${currentYear}`;
    
        // Clear previous days
        calendarDays.innerHTML = "";
    
        const firstDay = new Date(currentYear, currentMonth, 1).getDay();
        const lastDate = new Date(currentYear, currentMonth + 1, 0).getDate();
    
        for (let i = 0; i < firstDay; i++) {
            const emptyCell = document.createElement("div");
            calendarDays.appendChild(emptyCell);
        }
    
        for (let day = 1; day <= lastDate; day++) {
            const dayElement = document.createElement("div");
            dayElement.textContent = day;
            dayElement.classList.add("cursor-pointer", "rounded-md", "hover:bg-gray-200");
    
            const dateString = `${currentYear}-${String(currentMonth + 1).padStart(2, "0")}-${String(day).padStart(2, "0")}`;
            const notification = notifications.find(
                (n) => n.expDate === dateString || n.date === dateString
            );
    
            if (notification) {
                dayElement.classList.add("bg-white", "text-black", "font-semibold");
    
                dayElement.addEventListener("click", () => {
                    showDateModal(dateString); // Pass the clicked date to the modal function
                });
            }
    
            calendarDays.appendChild(dayElement);
        }
    }
    

    function showDateModal(dateString) {
        const dateModalContent = document.getElementById("date-modal-content");
        const dateModal = document.getElementById("date-modal");
    
        if (!dateModalContent || !dateModal) {
            console.error("Modal content or modal container is missing from the DOM.");
            return;
        }
    
        // Filter notifications for the clicked date
        const filteredNotifications = notifications.filter(
            (notification) =>
                notification.expDate === dateString || notification.date === dateString
        );
    
        // Populate the modal
        dateModalContent.innerHTML = `<h3 class="text-lg font-semibold mb-4">Schedules for ${dateString}</h3>`;
    
        if (filteredNotifications.length === 0) {
            dateModalContent.innerHTML += `<p class="text-sm text-gray-600">No schedules for this date.</p>`;
        } else {
            filteredNotifications.forEach((notification) => {
                const notificationElement = document.createElement("div");
                notificationElement.classList.add("p-3", "bg-gray-100", "rounded-md", "mb-2");
    
                const typeElement = document.createElement("p");
                typeElement.classList.add("font-semibold");
                typeElement.textContent = notification.type;
                notificationElement.appendChild(typeElement);
    
                if (notification.from) {
                    const fromElement = document.createElement("p");
                    fromElement.classList.add("text-sm", "text-gray-600");
                    fromElement.textContent = `From: ${notification.from}`;
                    notificationElement.appendChild(fromElement);
                }
    
                if (notification.title) {
                    const titleElement = document.createElement("p");
                    titleElement.classList.add("text-sm", "text-gray-600");
                    titleElement.textContent = `Title: ${notification.title}`;
                    notificationElement.appendChild(titleElement);
                }
    
                if (notification.time) {
                    const timeElement = document.createElement("p");
                    timeElement.classList.add("text-sm", "text-gray-600");
                    timeElement.textContent = `Time: ${notification.time}`;
                    notificationElement.appendChild(timeElement);
                }
    
                dateModalContent.appendChild(notificationElement);
            });
        }
    
        // Show the modal
        dateModal.classList.remove("hidden");
    }
    
    // Load the calendar on page load
    loadCalendar();

    


});

function toggleSidebar() {
     const sidebar = document.getElementById("sidebar");
    sidebar.classList.toggle("hidden");
}

