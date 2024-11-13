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

showPage('forms');

// Function to sort the donation table
function sortTable() {
    const table = document.querySelector('.donation-list'); // Select the donation list
    const rows = Array.from(table.querySelectorAll('.donation-row')); // Get all donation rows
    const sortBy = document.getElementById('sort').value; // Get the selected sort option

    // Define the sorting function
    rows.sort((a, b) => {
        let aValue, bValue;

        // Determine which cell to sort by based on the selected value
        switch (sortBy) {
            case 'updated':
                aValue = new Date(a.children[5].textContent.trim());
                bValue = new Date(b.children[5].textContent.trim());
                return aValue - bValue; // Ascending order for dates
            case 'name':
                aValue = a.children[1].textContent.toLowerCase();
                bValue = b.children[1].textContent.toLowerCase();
                return aValue.localeCompare(bValue); // String comparison
            case 'title':
                aValue = a.children[2].textContent.toLowerCase();
                bValue = b.children[2].textContent.toLowerCase();
                return aValue.localeCompare(bValue); // String comparison
            case 'status':
                aValue = a.children[3].textContent.toLowerCase();
                bValue = b.children[3].textContent.toLowerCase();
                return aValue.localeCompare(bValue); // String comparison
            case 'transferstatus':
                aValue = a.children[4].querySelector('.current-status').textContent.toLowerCase();
                bValue = b.children[4].querySelector('.current-status').textContent.toLowerCase();
                return aValue.localeCompare(bValue); // String comparison
            case 'date':
                aValue = new Date(a.children[0].textContent.trim());
                bValue = new Date(b.children[0].textContent.trim());
                return aValue - bValue; // Ascending order for dates
            default:
                return 0; // No sorting if no valid option is selected
        }
    });

    // Remove existing rows and append sorted rows
    rows.forEach(row => table.appendChild(row));
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


function toggleDropdown(element, id) {
    const options = element.nextElementSibling;
    options.style.display = options.style.display === 'none' ? 'block' : 'none';
}
 
let transferId; // Variable to store the ID of the transfer status to update
let newTransferStatus; // Variable to store the new transfer status

function updateTransferStatus(id, status) {
    transferId = id; // Set the ID of the record to update
    newTransferStatus = status; // Set the new transfer status
    
    // Show the confirmation modal
    const modal = new bootstrap.Modal(document.getElementById('confirmationModal'));
    modal.show();
}

// Confirm button logic
document.getElementById('confirmar').addEventListener('click', function() {
    // Create an XMLHttpRequest object for AJAX request
    const xhr = new XMLHttpRequest();
    xhr.open("POST", "update.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    
    xhr.onload = function () {
        if (xhr.status === 200) {
            location.reload(); // Refresh the page to see changes
        } else {
            alert("Error updating status."); // Display error message if request fails
        }
    };

    // Send the request with the ID and new transfer status
    xhr.send("id=" + transferId + "&transfer_status=" + newTransferStatus);
});





function openModal() {
    // Show the modal
    var rowDetailsModal = new bootstrap.Modal(document.getElementById('rowDetailsModal'));
    rowDetailsModal.show();

    // Select the modal content container to dynamically update it
    var modalContentContainer = document.getElementById('modalContentContainer');
    
}


// You can call the openModal function whenever needed, for example:


document.addEventListener('click', function(event) {
    // Get all dropdown options and the modal element
    const dropdowns = document.querySelectorAll('.dropdown-options');
    //const stsoption = document.getElementById('status-option');
    const modal = document.getElementById('rowDetailsModal');

    // Check if the clicked element is outside of any dropdown
    dropdowns.forEach(dropdown => {
        if (!dropdown.contains(event.target)) {
            dropdown.style.display = 'none'; // Close dropdown if clicked outside
        }
        
    });

    

    // Check if the modal exists and is clicked, exclude dropdown click from opening modal
    if (modal && !modal.contains(event.target)) {

        // Example: You can put your modal logic to open the modal only if no dropdown is clicked
    }
});

// Ensure that dropdown toggles stop the event propagation so modal logic doesn't get triggered
document.querySelectorAll('.dropdown-icon, .status-option').forEach(item => {
    item.addEventListener('click', function(event) {
        event.stopPropagation(); // Prevent event from propagating to the document
    });
});
