document.addEventListener('DOMContentLoaded', () => {
  const links = document.querySelectorAll('.nav-link');
  const content = document.getElementById('content');
  
  // Load the saved page from localStorage or default to 'dashboard'
  const savedPage = localStorage.getItem('currentPage') || 'dashboard';

  // Function to load content dynamically
  function loadContent(page) {
    // Fetch the HTML content dynamically
    fetch(`src/views/${page}.html`)
      .then(response => {
        if (!response.ok) {
          throw new Error(`Error loading page: ${response.statusText} (status code: ${response.status})`);
        }
        return response.text();
      })
      .then(html => {
        content.innerHTML = html;
        updateActiveTab(page);  // Update active tab after content is loaded

        // Load the associated JavaScript file if not already loaded
        loadScript(page);
      })
      .catch(error => {
        content.innerHTML = `<p class="text-red-500">Error: ${error.message}</p>`;
        console.error('Content Load Error:', error);
      });
  }

  // Function to dynamically load associated JavaScript
  function loadScript(page) {
    // Check if script is already loaded
    if (document.querySelector(`script[src="src/js/${page}.js"]`)) {
      console.log(`${page}.js is already loaded.`);
      return;
    }

    const script = document.createElement('script');
    script.src = `src/js/${page}.js`;  // Assume the JS file is named similarly to the HTML page
    script.onload = () => console.log(`${page}.js loaded successfully`);
    script.onerror = () => {
      console.error(`Failed to load ${page}.js`);
      content.innerHTML += `<p class="text-red-500">Failed to load JavaScript for ${page}. Please try again.</p>`;
    };
    document.body.appendChild(script);
  }

  // Load the saved content or default to Dashboard
  loadContent(savedPage);

  // Function to update active tab after loading content
  function updateActiveTab(activePage) {
    links.forEach(link => {
      const page = link.getAttribute('data-page'); // Directly get page from data-page
      if (activePage === page) {
        link.parentNode.classList.add('active');
      } else {
        link.parentNode.classList.remove('active');
      }
    });
  }

  // Initially set the active tab based on savedPage
  updateActiveTab(savedPage);

  // Attach event listeners to all navigation links
  links.forEach(link => {
    link.addEventListener('click', (e) => {
      e.preventDefault();

      // Get the page name from the data-page attribute
      const page = link.getAttribute('data-page');

      // Load the corresponding content
      loadContent(page);

      // Save the current page in localStorage
      localStorage.setItem('currentPage', page);

      // Update the active tab
      updateActiveTab(page);
    });
  });

  // Logout button functionality
  const logoutButton = document.getElementById('logout-button');
  if (logoutButton) {
    logoutButton.addEventListener('click', () => {
      localStorage.removeItem('currentPage'); // Clear the stored page on logout
      window.location.href = '/login'; // Redirect to login page (change URL as necessary)
    });
  }

});

// Function to toggle the sidebar visibility
function toggleSidebar() {
  const sidebar = document.getElementById('sidebar');
  if (sidebar) {
    sidebar.classList.toggle('hidden');
  }
}
