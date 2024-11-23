function init() {
    // Call the display functions here
    fetchTotalDonations();
    fetchDonations();
    // No need to call deleteDonation here; it's triggered by specific actions
}

function fetchTotalDonations() {
    fetch('https://lightpink-dogfish-795437.hostingersite.com/admin_mis/src/php/fetchTotalDonations.php')
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok: ' + response.statusText);
            }
            return response.json(); // Parse JSON response
        })
        .then(data => {
            if (data.error) {
                console.error('Error from PHP:', data.error);
                displayErrorMessages();
            } else {
                populateTotalDonationData(data);
            }
        })
        .catch(error => {
            console.error('Error fetching total donations:', error);
            displayErrorMessages();
        });
}
// Fetch and populate the donations table
function fetchDonations(sort = 'newest') {
    fetch(`https://lightpink-dogfish-795437.hostingersite.com/admin_mis/src/php/fetchDonations.php?sort=${sort}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok: ' + response.statusText);
            }
            return response.json();
        })
        .then(data => {
            if (data.error) {
                console.error(data.error);
                displayNoDataMessage();
            } else {
                populateTable(data);
            }
        })
        .catch(error => {
            console.error('Error fetching donations:', error);
            displayNoDataMessage();
        });
}
function populateTable(donations) {
    const tableBody = document.getElementById('donations-table').querySelector('tbody');
    tableBody.innerHTML = ''; // Clear existing rows

    if (donations.length === 0) {
        displayNoDataMessage();
        return;
    }

    donations.forEach(donation => {
        const row = document.createElement('tr');
        row.classList.add('border-t', 'border-gray-300', 'text-center');

        // Create and populate cells
        const dateCell = document.createElement('td');
        dateCell.classList.add('px-4', 'py-0','bg-white', 'border-black' , 'rounded-l-[15px]', 'border-t-2', 'border-b-2', 'border-l-2');
        dateCell.textContent = donation.submission_date;


const donorCell = document.createElement('td');
donorCell.classList.add('px-4', 'py-0', 'bg-white', 'border-black', 'border-t-2', 'border-b-2');
donorCell.textContent = donation.donor_name;

// Title Cell
const titleCell = document.createElement('td');
titleCell.classList.add('px-4', 'py-2', 'bg-white', 'border-black', 'border-t-2', 'border-b-2');
titleCell.textContent = donation.artifact_title;

// Type Cell
const typeCell = document.createElement('td');
typeCell.classList.add('px-4', 'py-0', 'bg-white', 'border-black', 'border-t-2', 'border-b-2');
typeCell.textContent = donation.artifact_type;

// Status Cell
const statusCell = document.createElement('td');
statusCell.classList.add('px-4', 'py-0', 'bg-white', 'border-black', 'border-t-2', 'border-b-2');
statusCell.textContent = donation.status;

// Updated Date Cell
const updatedDateCell = document.createElement('td');
updatedDateCell.classList.add('px-4', 'py-0', 'bg-white', 'border-black', 'border-t-2', 'border-b-2');
updatedDateCell.textContent = donation.updated_date === "Not Edited" || !donation.updated_date ? "Not Edited" : donation.updated_date;

// Transfer Status Cell
const transferStatusCell = document.createElement('td');
transferStatusCell.classList.add('px-4', 'py-0', 'bg-white', 'border-black', 'border-t-2', 'border-b-2');
// Assuming createTransferStatusCell returns an element, append it
transferStatusCell.appendChild(createTransferStatusCell(donation));

// Action Buttons Cell
const actionCell = document.createElement('td');
actionCell.classList.add('px-4', 'py-0', 'bg-white', 'border-black', 'rounded-r-[15px]', 'border-t-2', 'border-b-2', 'border-r-2');
// Assuming createActionButtons returns an element, append it
actionCell.appendChild(createActionButtons(donation));

        // Append cells to row
        row.appendChild(dateCell);
        row.appendChild(donorCell);
        row.appendChild(titleCell);
        row.appendChild(typeCell);
        row.appendChild(statusCell);
        row.appendChild(transferStatusCell);
        row.appendChild(updatedDateCell);
        row.appendChild(actionCell);

        tableBody.appendChild(row);
    });
}
function displayErrorMessages() {
    const errorMessage = "Error fetching data";
    document.getElementById('total-donations').innerText = errorMessage;
    document.getElementById('total-accepted').innerText = errorMessage;
    document.getElementById('total-rejected').innerText = errorMessage;
    document.getElementById('total-donform').innerText = errorMessage;
    document.getElementById('total-lendform').innerText = errorMessage;
}
function populateTotalDonationData(data) {
    document.getElementById('total-donations').innerText = data.total_donations || 0;
    document.getElementById('total-accepted').innerText = data.accepted_donations || 0;
    document.getElementById('total-rejected').innerText = data.rejected_donations || 0;
    document.getElementById('total-donform').innerText = data.total_donation_forms || 0;
    document.getElementById('total-lendform').innerText = data.total_lending_forms || 0;
}
function displayNoDataMessage() {
    const tableBody = document.querySelector('tbody');
    tableBody.innerHTML = `
        <tr>
            <td colspan="8" class="text-center py-4">No donations found or an error occurred.</td>
        </tr>
    `;
}
function createTableCell(content) {
    const cell = document.createElement('td');
    cell.classList.add('px-4', 'py-2');
    cell.textContent = content;
    return cell;
}
function handleAction(action, donation) {
    console.log(`Action: ${action}`);
    console.log(`Donation Data:`, donation); // Log the full donation object
    console.log(`Artifact ID: ${donation.donID}`); // Log the specific ID for debugging

    switch (action) {
        case 'preview':
            console.log(`Preview donation with ID: ${donation.donID}`);
            break;
        case 'edit':
            console.log(`Edit donation with ID: ${donation.donID}`);
            break;
        case 'delete':
            console.log(`Delete donation with ID: ${donation.donID}`);
              // Open delete confirmation modal
              openDeleteModal((confirmed) => {
                if (confirmed) {
                    // Call delete function
                    deleteDonation(donation.donID);
                } else {
                    console.log(`Deletion canceled for ID: ${donation.donID}`);
                }
            });
            break;
            break;
        default:
            console.error('Unknown action:', action);
    }
}

function createTransferStatusCell(donation) {
    const cell = document.createElement('td');
    cell.classList.add('px-4', 'py-2');
    const dropdown = document.createElement('select');
    dropdown.classList.add('border','rounded');

    const statuses = ['ACQUIRED', 'FAILED', 'PENDING'];
    statuses.forEach(status => {
        const option = document.createElement('option');
        option.value = status.toUpperCase();
        option.textContent = status;
        option.selected = donation.transfer_status.toUpperCase() === status.toUpperCase();
        dropdown.appendChild(option);
    });

    dropdown.addEventListener('change', () => {
        const newStatus = dropdown.value;
        openStatusModal(donation.donID, donation.transfer_status, newStatus, dropdown);
    });

    cell.appendChild(dropdown);
    return cell;
}

function createActionButtons(donation) {
    const cell = document.createElement('td');
    cell.classList.add('px-4', 'py-2', 'flex', 'justify-center', 'space-x-2');

    // Define the actions and corresponding icons
    const actions = [
        { icon: 'eye', action: 'preview' },
        { icon: 'edit', action: 'edit' },
        { icon: 'trash', action: 'delete' },
    ];

    // Loop through the actions to create buttons
    actions.forEach(({ icon, action }) => {
        console.log(`Creating ${action} button for donation ID: ${donation.artifactID}`);
        const button = document.createElement('button');
        button.classList.add('bg-orange-400', 'text-white', 'p-2', 'rounded', 'hover:bg-orange-300');
        button.innerHTML = `<i class="fas fa-${icon}"></i>`;
        
        // Pass the full donation object to handleAction
        button.addEventListener('click', () => handleAction(action, donation));
        cell.appendChild(button);
    });

    return cell;
}
document.getElementById("sorts").addEventListener("change", function () {
    fetchDonations(this.value);
});
  
function deleteDonation(donID) {
    fetch(`https://lightpink-dogfish-795437.hostingersite.com/admin_mis/src/php/deleteDonations.php?id=${donID}`, {
        method: 'DELETE',
    })
        .then((response) => {
            if (!response.ok) {
                throw new Error('Failed to delete the donation.');
            }
            return response.json();
        })
        .then((data) => {
            if (data.message) {
                alert(data.message);

                // Dynamically remove the row from the table
                const row = document.querySelector(`[data-id="${donID}"]`);
                if (row) {
                    row.parentNode.removeChild(row);
                }
            } else {
                console.error(data.error || 'Unknown error occurred.');
            }
        })
        .catch((error) => {
            console.error('Error deleting donation:', error);
        });
}

function openDeleteModal(callback) {
    const modal = document.getElementById('delete-modal');
    modal.classList.remove('hidden');

    const confirmButton = document.getElementById('delete-confirm-button');
    const cancelButton = document.getElementById('delete-cancel-button');

    // Remove existing event listeners to avoid stacking
    confirmButton.replaceWith(confirmButton.cloneNode(true));
    cancelButton.replaceWith(cancelButton.cloneNode(true));

    document.getElementById('delete-confirm-button').addEventListener('click', function () {
        if (typeof callback === 'function') {
            callback(true);
        }
        closeModal('delete-modal');
    });

    document.getElementById('delete-cancel-button').addEventListener('click', function () {
        if (typeof callback === 'function') {
            callback(false);
        }
        closeModal('delete-modal');
    });
}

function confirmDeleteDonation(donID) {
    openDeleteModal((confirmed) => {
        if (confirmed) {
            deleteDonation(donID); // Call deleteDonation with the correct ID
        }
    });
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    modal.classList.add('hidden');
}


function updateTransferStatus(donID, newStatus) {
    fetch('https://lightpink-dogfish-795437.hostingersite.com/admin_mis/src/php/updateTransferStatus.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ donation: donID, transfer_status: newStatus })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Failed to update transfer status');
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            fetchDonations(); // Refresh the table to reflect updates
        } else {
            console.error('Failed to update transfer status:', data.error);
        }
    })
    .catch(error => {
        console.error('Error updating transfer status:', error);
    });
}

function openStatusModal(donID, currentStatus, newStatus, dropdown) {
    const modal = document.getElementById("transfer-status-modal");
    modal.classList.remove("hidden");

    // Ensure the modal body element exists
    const confirmationMessage = document.getElementById("status-confirmation-message");

    if (confirmationMessage) {
        confirmationMessage.textContent = `Do you want to confirm the change of transfer status from "${currentStatus}" to "${newStatus}" for the form ID: ${donID}?`;

        console.log(`Opening confirmation modal for form ID: ${donID}, current status: "${currentStatus}", new status: "${newStatus}"`);

        // When the user confirms
        document.getElementById("status-confirm-button").onclick = () => {
            console.log(`User confirmed the change for form ID: ${donID}, changing status from "${currentStatus}" to "${newStatus}"`);
            updateTransferStatus(donID, newStatus);
            closeModal("transfer-status-modal");
        };

        // When the user cancels
        document.getElementById("status-cancel-button").onclick = () => {
            console.log(`User canceled the status change for form ID: ${donID}. Status remains as "${currentStatus}"`);
            dropdown.value = currentStatus; // Revert the dropdown to its previous value
            closeModal("transfer-status-modal");
        };
    } else {
        console.error('Confirmation message element not found');
    }
}


  
init();  // Initialize everything when the script runs
