document.addEventListener("DOMContentLoaded", () => {
    const links = document.querySelectorAll(".nav-link");
    const content = document.getElementById("content");
  

    const savedPage = localStorage.getItem("currentPage") || "dashboard";
  
   
    function loadContent(page) {
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
      
            if (typeof init === "function") {
              init();
            }
        };
        script.onerror = () => {
            console.error(`Failed to load ${page}.js`);
        };
        document.body.appendChild(script);
    }
  
    loadContent(savedPage);
  
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

// Close sidebar if clicked outside
document.addEventListener("click", function(event) {
    const sidebar = document.getElementById("sidebar");
    const button = document.querySelector("button[onclick='toggleSidebar()']"); // Sidebar toggle button
    
    // Check if the clicked element is not the sidebar or the toggle button
    if (!sidebar.contains(event.target) && !button.contains(event.target)) {
        sidebar.classList.add("hidden");
    }
});