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
});
  
function toggleSidebar() {
    const sidebar = document.getElementById("sidebar");
    sidebar.classList.toggle("hidden");
}
