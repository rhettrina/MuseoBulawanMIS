const router = new Navigo("/");

// Function to load the requested view
async function loadView(viewName) {
  const mainPage = document.getElementById("main-page");
  
  // Show a loading spinner while the view is being fetched
  mainPage.innerHTML = "<div class='loading-spinner'>Loading...</div>";

  try {
    // Fetch the HTML content for the specified view
    const response = await fetch(`./src/views/${viewName}.html`);
    if (!response.ok) {
      throw new Error("Page not found");
    }

    // Update the main page content
    const content = await response.text();
    mainPage.innerHTML = content;

    // Load the JavaScript file associated with the view
    loadViewScript(viewName);

  } catch (error) {
    console.error("Error loading the view:", error);
    mainPage.innerHTML = "<p>Error loading the page. Please try again later.</p>";
  }
}

// Function to load the JavaScript file for a view
function loadViewScript(viewName) {
  // Remove any existing view-specific script to prevent memory leaks
  const existingScript = document.getElementById("view-script");
  if (existingScript) {
    existingScript.remove();
  }

  // Check if the view-specific JS file exists before trying to load it
  const scriptPath = `./src/js/views/${viewName}.js`;

  const xhr = new XMLHttpRequest();
  xhr.open('HEAD', scriptPath, true);
  xhr.onreadystatechange = function () {
    if (xhr.readyState === 4 && xhr.status === 200) {
      const script = document.createElement("script");
      script.src = scriptPath;
      script.id = "view-script";
      script.onload = () => console.log(`${viewName}.js loaded successfully.`);
      script.onerror = () => console.error(`Error loading the script for ${viewName}`);
      document.body.appendChild(script);
    } else if (xhr.readyState === 4 && xhr.status !== 200) {
      console.warn(`No script found for ${viewName}`);
    }
  };
  xhr.send();
}

// Function to update the active link
function updateActiveLink() {
  const links = document.querySelectorAll(".nav-link");
  const currentPath = window.location.pathname;

  links.forEach((link) => {
    const linkPath = link.getAttribute("href");

    if (linkPath === currentPath || (linkPath === "/dashboard" && currentPath === "/")) {
      link.classList.add("active");
    } else {
      link.classList.remove("active");
    }
  });
}

// Define all available routes in an array to reduce redundancy
const routes = ["dashboard", "donation", "artifact", "appointment", "layout", "article"];

// Setting up the routes dynamically
routes.forEach(route => {
  router.on(`/${route}`, () => {
    loadView(route).then(() => updateActiveLink());
  });
});

// Default route to dashboard
router.on("/", () => {
  loadView("dashboard").then(() => updateActiveLink());
});

// Handle 404 - Not Found
router.notFound(() => {
  document.getElementById("main-page").innerHTML = "<p>404 - Page not found</p>";
  updateActiveLink();
});

// Resolve the current route on load
router.resolve();

// Hook after navigation to update the active link
router.hooks({
  after: (done, match) => {
    updateActiveLink();
    done();
  },
});
