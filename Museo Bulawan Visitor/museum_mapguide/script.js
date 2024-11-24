document.addEventListener("DOMContentLoaded", function () {
    const stickyNav = document.getElementById("stickyNav");

    window.addEventListener("scroll", function () {
        if (window.scrollY > 50) {
            // Add class to make background opaque and show links
            stickyNav.classList.add("scrolled");
        } else {
            // Remove class to make background transparent and hide links
            stickyNav.classList.remove("scrolled");
        }
    });
});

async function fetchAndProcessImagePath() {
    try {
      // Fetch the data from the PHP script
      const response = await fetch('https://your-domain.com/path-to/getActiveImagePath.php');
      const data = await response.json();
  
      if (data.success) {
        console.log("Original image path:", data.image_path);
  
        // Extract the filename from the image path
        const filename = data.image_path.split('/').pop();
        console.log("Extracted filename:", filename);
  
        // Construct the new path
        const basePath = 'https://museobulawan.online/development/admin_mis/src/uploads/layout-made/';
        const newPath = `${basePath}${filename}`;
        console.log("New path:", newPath);
  
        // Example: Update an image element in the frontend
        const imageElement = document.getElementById('map-placeholder');
        if (imageElement) {
          imageElement.src = newPath;
        }
      } else {
        console.error("Failed to fetch active image path:", data.message);
      }
    } catch (error) {
      console.error("Error while processing image path:", error);
    }
  }
  
  // Example: Call the function
  fetchAndProcessImagePath();
  