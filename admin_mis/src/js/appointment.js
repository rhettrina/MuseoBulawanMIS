function init() {
    
    fetchDonations();
  
}



function displayErrorMessages() {
    const errorMessage = "Error fetching data";
    document.getElementById('total-appointments').innerText = errorMessage;
    document.getElementById('total-approved').innerText = errorMessage;
    document.getElementById('total-rejected').innerText = errorMessage;
    document.getElementById('expected-visitors').innerText = errorMessage;
    document.getElementById('present-visitors').innerText = errorMessage;
}

function populateTotalAppointmentData(data) {
    document.getElementById('total-appointments').innerText = data.total_donations || 0;
    document.getElementById('total-approved').innerText = data.accepted_donations || 0;
    document.getElementById('total-rejected').innerText = data.rejected_donations || 0;
    document.getElementById('expected-visitors').innerText = data.total_donation_forms || 0;
    document.getElementById('present-visitors').innerText = data.total_lending_forms || 0;
}

// Fetch and populate the appointment table
function fetchAppointments(sort = 'newest') {
    fetch(`https://lightpink-dogfish-795437.hostingersite.com/admin_mis/src/php/fetchAppointments.php?sort=${sort}`)
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

function populateAppointmentTable(appointments) {
    const tableBody = document.getElementById('appointment-table').querySelector('tbody');
    tableBody.innerHTML = ''; // Clear existing rows

    if (appointments.length === 0) {
        displayNoDataMessage();
        return;
    }

    appointments.forEach(appointment => {
        const row = document.createElement('tr');
        row.classList.add('border-t', 'border-gray-300', 'text-center');

        // Create and populate cells
        const dateCell = document.createElement('td');
        dateCell.classList.add('px-4', 'py-2','bg-white', 'border-black' , 'rounded-l-[15px]', 'border-t-2', 'border-b-2', 'border-l-2');
        dateCell.textContent = appointment.appointment_date;

        const timeCell = document.createElement('td');
        timeCell.classList.add('px-4', 'py-2', 'bg-white', 'border-black', 'border-t-2', 'border-b-2');
        timeCell.textContent = appointment.appointment_time;

        // Donor Cell (Now 'Attendee')
        const attendeeCell = document.createElement('td');
        attendeeCell.classList.add('px-4', 'py-2', 'bg-white', 'border-black', 'border-t-2', 'border-b-2');
        attendeeCell.textContent = appointment.attendee_name;

        // Item Name Cell
        const titleCell = document.createElement('td');
        titleCell.classList.add('px-4', 'py-2', 'bg-white', 'border-black', 'border-t-2', 'border-b-2');
        titleCell.textContent = appointment.service_name;

        // Status Cell
        const statusCell = document.createElement('td');
        statusCell.classList.add('px-4', 'py-2', 'bg-white', 'border-black', 'border-t-2', 'border-b-2');
        statusCell.textContent = appointment.status;

        // Updated Date Cell
        const updatedDateCell = document.createElement('td');
        updatedDateCell.classList.add('px-4', 'py-2', 'bg-white', 'border-black', 'border-t-2', 'border-b-2');
        updatedDateCell.textContent = appointment.updated_date === "Not Edited" || !appointment.updated_date ? "Not Edited" : appointment.updated_date;

        // Transfer Status Cell (If applicable)
        const transferStatusCell = document.createElement('td');
        transferStatusCell.classList.add('px-4', 'py-2', 'bg-white', 'border-black', 'border-t-2', 'border-b-2');
        transferStatusCell.appendChild(createTransferStatusCell(appointment));

        // Action Buttons Cell
        const actionCell = document.createElement('td');
        actionCell.classList.add('px-4', 'py-2', 'bg-white', 'border-black', 'rounded-r-[15px]', 'border-t-2', 'border-b-2', 'border-r-2');
        actionCell.appendChild(createActionButtons(appointment));

        // Append cells to row
        row.appendChild(dateCell);
        row.appendChild(timeCell);
        row.appendChild(attendeeCell);
        row.appendChild(titleCell);
        row.appendChild(statusCell);
        row.appendChild(updatedDateCell);
        row.appendChild(transferStatusCell);
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
            openPreviewModal();
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
  
function deleteDonation(appointmentId) {
    fetch(`https://lightpink-dogfish-795437.hostingersite.com/admin_mis/src/php/deleteAppointmnets.php?id=${appointmentId}`, {
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



  
function openPreviewModal() {
    const modal = document.getElementById("preview-modal");
    modal.classList.remove("hidden"); // Show the modal
}

function previewRow(formType, rowId) {
    // Set the modal to "Loading..." by default
    document.getElementById('preview-donor-name').textContent = "Loading...";
    document.getElementById('preview-item-name').textContent = "Loading...";
    document.getElementById('preview-donation-date').textContent = "Loading...";
    document.getElementById('preview-status').textContent = "Loading...";
    document.getElementById('preview-transfer-status').textContent = "Loading...";

    // Make the fetch request to get the data
    fetch(`https://lightpink-dogfish-795437.hostingersite.com/admin_mis/src/php/fetch-row.php?type=${formType}&id=${rowId}`, {
        method: 'GET',
    })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                // Populate modal with the fetched data based on the form type
                if (formType === 'donation' && data.row) {
                    document.getElementById('preview-donor-name').textContent = `${data.row.first_name} ${data.row.last_name}`;
                    document.getElementById('preview-item-name').textContent = data.row.artifact_title;
                    document.getElementById('preview-donation-date').textContent = data.row.submission_date || data.row.submitted_at;
                    document.getElementById('preview-status').textContent = data.row.status || 'N/A';
                    document.getElementById('preview-transfer-status').textContent = data.row.transfer_status || 'N/A';
                }

                // If it's lending, update with lending data (you may need to modify this as per your needs)
                else if (formType === 'lending' && data.row) {
                    document.getElementById('preview-donor-name').textContent = `${data.row.first_name} ${data.row.last_name}`;
                    document.getElementById('preview-item-name').textContent = data.row.artifact_title;
                    document.getElementById('preview-donation-date').textContent = data.row.submitted_at;
                    document.getElementById('preview-status').textContent = data.row.status || 'N/A';
                    document.getElementById('preview-transfer-status').textContent = data.row.transfer_status || 'N/A';
                }

                // Show the modal using Bootstrap
                const previewModal = new bootstrap.Modal(document.getElementById('preview-modal'));
                previewModal.show();
            } else {
                console.error('Failed to fetch data:', data.error);
                // Handle error (e.g., show an error message in the modal)
                document.getElementById('preview-donor-name').textContent = "Error loading data";
                document.getElementById('preview-item-name').textContent = "Error loading data";
                document.getElementById('preview-donation-date').textContent = "Error loading data";
                document.getElementById('preview-status').textContent = "Error loading data";
                document.getElementById('preview-transfer-status').textContent = "Error loading data";
            }
        })
        .catch(error => {
            console.error('Error fetching row data:', error);
            // Handle fetch error (e.g., show a general error message)
            document.getElementById('preview-donor-name').textContent = "Error loading data";
            document.getElementById('preview-item-name').textContent = "Error loading data";
            document.getElementById('preview-donation-date').textContent = "Error loading data";
            document.getElementById('preview-status').textContent = "Error loading data";
            document.getElementById('preview-transfer-status').textContent = "Error loading data";
        });
}

// Close modal logic
document.getElementById("preview-close-button").addEventListener("click", () => {
    const modal = document.getElementById("preview-modal");
    modal.classList.add("hidden"); // Hide the modal
});


  
init();  // Initialize everything when the script runs
