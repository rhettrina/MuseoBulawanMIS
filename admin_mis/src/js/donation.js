function init() {
    // Call the display functions here
    fetchTotalDonations();
    fetchDonations();
    deleteDonation(); 
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
                // Log the error and display it in the console
                console.error('Error from PHP:', data.error);
                // Display an error message in the UI
                document.getElementById('total-donations').innerText = "Error fetching data";
                document.getElementById('total-accepted').innerText = "Error fetching data";
                document.getElementById('total-rejected').innerText = "Error fetching data";
                document.getElementById('total-donform').innerText = "Error fetching data";
                document.getElementById('total-lendform').innerText = "Error fetching data";
            } else {
                // Populate data if there are no errors
                document.getElementById('total-donations').innerText = data.total_donations || 0;
                document.getElementById('total-accepted').innerText = data.accepted_donations || 0;
                document.getElementById('total-rejected').innerText = data.rejected_donations || 0;
                document.getElementById('total-donform').innerText = data.total_donation_forms || 0;
                document.getElementById('total-lendform').innerText = data.total_lending_forms || 0;
            }
        })
        .catch(error => {
            // Catch network or other fetch errors
            console.error('Error fetching total donations:', error);
            document.getElementById('total-donations').innerText = "Error fetching data";
            document.getElementById('total-accepted').innerText = "Error fetching data";
            document.getElementById('total-rejected').innerText = "Error fetching data";
            document.getElementById('total-donform').innerText = "Error fetching data";
            document.getElementById('total-lendform').innerText = "Error fetching data";
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

        // Create cells
        const dateCell = document.createElement('td');
        dateCell.classList.add('px-4', 'py-2');
        dateCell.textContent = donation.donation_date;

        const titleCell = document.createElement('td');
        titleCell.classList.add('px-4', 'py-2');
        titleCell.textContent = donation.item_name;

        const donorCell = document.createElement('td');
        donorCell.classList.add('px-4', 'py-2');
        donorCell.textContent = donation.donor_name;

        const statusCell = document.createElement('td');
        statusCell.classList.add('px-4', 'py-2');
        statusCell.textContent = donation.status;

        // Dropdown for transfer status
        const transferStatusCell = document.createElement('td');
        transferStatusCell.classList.add('px-4', 'py-2');

        const dropdown = document.createElement('select');
        dropdown.classList.add('border', 'p-2', 'rounded');
        const statuses = ['Accepted', 'Failed', 'Rejected'];
        statuses.forEach(status => {
            const option = document.createElement('option');
            option.value = status.toLowerCase();
            option.textContent = status;
            option.selected = donation.transfer_status.toLowerCase() === status.toLowerCase();
            dropdown.appendChild(option);
        });

        dropdown.addEventListener('change', () => {
            updateTransferStatus(donation.id, dropdown.value);
        });

        transferStatusCell.appendChild(dropdown);

        const updatedDateCell = document.createElement('td');
        updatedDateCell.classList.add('px-4', 'py-2');
        updatedDateCell.textContent = donation.updated_date === "Not Edited" || !donation.updated_date
            ? "Not Edited"
            : donation.updated_date;

        const actionCell = document.createElement('td');
        actionCell.classList.add('px-4', 'py-2', 'flex', 'justify-center', 'space-x-2');

        // Add buttons with event listeners
        const previewButton = document.createElement('button');
        previewButton.classList.add('bg-orange-400', 'text-white', 'p-2', 'rounded', 'hover:bg-orange-300');
        previewButton.innerHTML = `<i class="fas fa-eye"></i>`;
        previewButton.addEventListener('click', () => handleAction('preview', donation.id));

        const editButton = document.createElement('button');
        editButton.classList.add('bg-orange-400', 'text-white', 'p-2', 'rounded', 'hover:bg-orange-300');
        editButton.innerHTML = `<i class="fas fa-edit"></i>`;
        editButton.addEventListener('click', () => handleAction('edit', donation.id));

        const deleteButton = document.createElement('button');
        deleteButton.classList.add('bg-orange-400', 'text-white', 'p-2', 'rounded', 'hover:bg-orange-300');
        deleteButton.innerHTML = `<i class="fas fa-trash"></i>`;
        deleteButton.addEventListener('click', () => handleAction('delete', donation.id));

        actionCell.appendChild(previewButton);
        actionCell.appendChild(editButton);
        actionCell.appendChild(deleteButton);

        row.appendChild(dateCell);
        row.appendChild(titleCell);
        row.appendChild(donorCell);
        row.appendChild(statusCell);
        row.appendChild(transferStatusCell);
        row.appendChild(updatedDateCell);
        row.appendChild(actionCell);

        tableBody.appendChild(row);
    });
}


function handleAction(action, donationId) {
    switch (action) {
        case 'preview':
            console.log(`Preview donation with ID: ${donationId}`);
            // Implement preview functionality here
            break;
        case 'edit':
            console.log(`Edit donation with ID: ${donationId}`);
            // Implement edit functionality here
            break;
        case 'delete':
            // Show confirmation modal before deleting
            openDeleteModal((response) => {
                if (response) {
                    console.log(`Donation with ID ${donationId} deleted.`);
                    // Call deleteDonation function
                    deleteDonation(donationId);
                } else {
                    console.log("Delete action canceled.");
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
            <td colspan="7" class="text-center py-4">No donations found or an error occurred.</td>
        </tr>
    `;
}

document.getElementById("sort").addEventListener("change", function () {
    const sortOption = this.value;
    fetchDonations(sortOption);
});

// Delete donation via AJAX
function deleteDonation(donationId) {
    fetch(`https://lightpink-dogfish-795437.hostingersite.com/admin_mis/src/php/deleteDonations.php?id=${donationId}`, {
        method: 'DELETE',
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Failed to delete donation');
        }
        // Refresh the donation list after successful deletion
        fetchDonations();
    })
    .catch(error => {
        console.error('Error deleting donation:', error);
    });
}

// Function to open the delete confirmation modal
function openDeleteModal(callback) {
    const modal = document.getElementById("delete-modal");
    modal.classList.remove("hidden");

    // Handling button clicks
    document.getElementById("delete-confirm-button").onclick = () => {
        callback(true);  // Return 'true' if 'Delete' is clicked
        closeModal("delete-modal");
    };

    document.getElementById("delete-cancel-button").onclick = () => {
        callback(false);  // Return 'false' if 'Cancel' is clicked
        closeModal("delete-modal");
    };
}

// Function to close the modal
function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    modal.classList.add("hidden");
}

// Initialize data and actions
document.addEventListener('DOMContentLoaded', init);

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
            console.log('Transfer status updated successfully:', data);
            fetchDonations(); // Refresh the table to reflect updates
        } else {
            console.error('Failed to update transfer status:', data.error);
        }
    })
    .catch(error => {
        console.error('Error updating transfer status:', error);
    });
}
