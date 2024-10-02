// Function to show specific pages
function showPage(pageId) {
    // Hide all page content sections
    var pages = document.querySelectorAll('.page-content');
    pages.forEach(function(page) {
        page.style.display = 'none';
    });

    // Show the selected page content
    document.getElementById(pageId).style.display = 'block';

    // Remove 'active' class from all tabs
    var tabs = document.querySelectorAll('.tab');
    tabs.forEach(function(tab) {
        tab.classList.remove('active');
    });

    // Add 'active' class to the clicked tab
    var activeTab = pageId === 'forms' ? 'forms-tab' :
                    pageId === 'transfer' ? 'transfer-tab' :
                    'donators-tab';
    document.getElementById(activeTab).classList.add('active');
}


    // Function to sort the donation table
    function sortTable() {
        const table = document.getElementById("donationTable");
        const tbody = table.querySelector("tbody");
        const rows = Array.from(tbody.querySelectorAll("tr"));
        
        // Get sort criteria
        const sortCriteria = document.getElementById("sort").value;

        rows.sort((a, b) => {
            const dateA = new Date(a.cells[0].innerText);
            const dateB = new Date(b.cells[0].innerText);

            if (sortCriteria === "date") {
                return dateB - dateA; // Sort by newest first
            } else {
                return dateA - dateB; // Sort by oldest first
            }
        });

        // Re-append sorted rows to tbody
        rows.forEach(row => tbody.appendChild(row));
    }

    // Event listener for sort dropdown
    document.getElementById("sort").addEventListener("change", sortTable);
