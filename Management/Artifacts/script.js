// Function to show specific pages
function showPage(pageId) {
    // Hide all page content sections and add blur to inactive pages
    var pages = document.querySelectorAll('.page-content');
    pages.forEach(function(page) {
        if (page.id === pageId) {
            // Show the selected page and remove blur
            page.style.display = 'block';

        } else {
            // Hide other pages and add blur
            page.style.display = 'none';
        }
    });

}

showPage('artifacts');

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


        if (sortCriteria === "date-newest") {
            return dateB - dateA; // Sort by newest first
        } else if (sortCriteria === "date-oldest") {
            return dateA - dateB; // Sort by oldest first
        } 
    });

    // Re-append sorted rows to tbody
    rows.forEach(row => tbody.appendChild(row));
}

// Event listener for sort dropdown
document.getElementById("sort").addEventListener("change", sortTable);

function toggleDetails(element) {
    const details = element.nextElementSibling;
    const arrow = element.querySelector('.arrow');

    if (details.classList.contains('active')) {
        details.classList.remove('active');
        arrow.classList.remove('up');
        arrow.classList.add('down');
    } else {
        details.classList.add('active');
        arrow.classList.remove('down');
        arrow.classList.add('up');
    }
}

function toggleDetails(element) {
    const details = element.nextElementSibling;
    const arrow = element.querySelector('.arrow');

    if (details.classList.contains('active')) {
        details.classList.remove('active');
        arrow.classList.remove('up');
        arrow.classList.add('down');
    } else {
        details.classList.add('active');
        arrow.classList.remove('down');
        arrow.classList.add('up');
    }
}