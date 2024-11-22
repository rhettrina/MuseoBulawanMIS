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
        const dateCell = createTableCell(donation.donation_date);
        const titleCell = createTableCell(donation.item_name);
        const donorCell = createTableCell(donation.donor_name);

        const typeCell = createTableCell(donation.type);
        const statusCell = createTableCell(donation.status);
        const updatedDateCell = createTableCell(donation.updated_date === "Not Edited" || !donation.updated_date ? "Not Edited" : donation.updated_date);

        // Dropdown for transfer status
        const transferStatusCell = createTransferStatusCell(donation);

        // Action buttons
        const actionCell = createActionButtons(donation);

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

function createTableCell(content) {
    const cell = document.createElement('td');
    cell.classList.add('px-4', 'py-2');
    cell.textContent = content;
    return cell;
}

function createTransferStatusCell(donation) {
    const cell = document.createElement('td');
    cell.classList.add('px-4', 'py-2');
    const dropdown = document.createElement('select');
    dropdown.classList.add('border', 'p-2', 'rounded');

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
        openStatusModal(donation.id, donation.transfer_status, newStatus, dropdown);
    });

    cell.appendChild(dropdown);
    return cell;
}

function createActionButtons(donation) {
    const cell = document.createElement('td');
    cell.classList.add('px-4', 'py-2', 'flex', 'justify-center', 'space-x-2');

    const previewButton = createActionButton('eye', 'preview', donation);
    const editButton = createActionButton('edit', 'edit', donation);
    const deleteButton = createActionButton('trash', 'delete', donation);

    cell.appendChild(previewButton);
    cell.appendChild(editButton);
    cell.appendChild(deleteButton);

    return cell;
}

function createActionButton(icon, action, donation) {
    const button = document.createElement('button');
    button.classList.add('bg-orange-400', 'text-white', 'p-2', 'rounded', 'hover:bg-orange-300');
    button.innerHTML = `<i class="fas fa-${icon}"></i>`;
    button.addEventListener('click', () => handleAction(action, donation.id));
    return button;
}

function handleAction(action, donationId) {
    switch (action) {
        case 'preview':
            console.log(`Preview donation with ID: ${donationId}`);
            break;
        case 'edit':
            console.log(`Edit donation with ID: ${donationId}`);
            break;
        case 'delete':
            openDeleteModal(response => {
                if (response) {
                    deleteDonation(donationId);
                }
            });
            break;
        default:
            console.error('Unknown action:', action);
    }
}

function displayNoDataMessage() {
    const tableBody = document.querySelector('tbody');
    tableBody.innerHTML = `
        <tr>
            <td colspan="8" class="text-center py-4">No donations found or an error occurred.</td>
        </tr>
    `;
}

document.getElementById("sorts").addEventListener("change", function () {
    fetchDonations(this.value);
});





function deleteDonation(donationId) {
    fetch(`https://lightpink-dogfish-795437.hostingersite.com/admin_mis/src/php/deleteDonations.php?id=${donationId}`, {
        method: 'DELETE',
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Failed to delete donation');
        }
        fetchDonations(); // Refresh the donation list after deletion
    })
    .catch(error => {
        console.error('Error deleting donation:', error);
    });
}

function openDeleteModal(callback) {
    const modal = document.getElementById("delete-modal");
    modal.classList.remove("hidden");

    document.getElementById("delete-confirm-button").onclick = () => {
        callback(true);
        closeModal("delete-modal");
    };

    document.getElementById("delete-cancel-button").onclick = () => {
        callback(false);
        closeModal("delete-modal");
    };
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    modal.classList.add("hidden");
}

function updateTransferStatus(donationId, newStatus) {
    fetch('https://lightpink-dogfish-795437.hostingersite.com/admin_mis/src/php/updateTransferStatus.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id: donationId, transfer_status: newStatus })
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

function openStatusModal(donationId, currentStatus, newStatus, dropdown) {
    const modal = document.getElementById("transfer-status-modal");
    modal.classList.remove("hidden");

    // Ensure the modal body element exists
    const confirmationMessage = document.getElementById("status-confirmation-message");

    if (confirmationMessage) {
        confirmationMessage.textContent = `Do you want to confirm the change of transfer status from "${currentStatus}" to "${newStatus}" for the form ID: ${donationId}?`;

        console.log(`Opening confirmation modal for form ID: ${donationId}, current status: "${currentStatus}", new status: "${newStatus}"`);

        // When the user confirms
        document.getElementById("status-confirm-button").onclick = () => {
            console.log(`User confirmed the change for form ID: ${donationId}, changing status from "${currentStatus}" to "${newStatus}"`);
            updateTransferStatus(donationId, newStatus);
            closeModal("transfer-status-modal");
        };

        // When the user cancels
        document.getElementById("status-cancel-button").onclick = () => {
            console.log(`User canceled the status change for form ID: ${donationId}. Status remains as "${currentStatus}"`);
            dropdown.value = currentStatus; // Revert the dropdown to its previous value
            closeModal("transfer-status-modal");
        };
    } else {
        console.error('Confirmation message element not found');
    }
}



init();  // Initialize everything when the script runs
