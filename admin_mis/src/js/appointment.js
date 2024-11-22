function initAppointments() {
    fetchAppointments(); // Fetch and display appointments on initialization
}


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
                displayNoAppointmentsMessage();
            } else {
                populateAppointmentsTable(data);
            }
        })
        .catch(error => {
            console.error('Error fetching appointments:', error);
            displayNoAppointmentsMessage();
        });
}

function populateAppointmentsTable(appointments) {
    const tableBody = document.getElementById('appointment-table').querySelector('tbody');
    tableBody.innerHTML = ''; // Clear existing rows

    if (appointments.length === 0) {
        displayNoAppointmentsMessage();
        return;
    }

    appointments.forEach(appointment => {
        const row = document.createElement('tr');
        row.classList.add('border-t', 'border-gray-300', 'text-center');

        // Create and populate cells
        const dateCell = createTableCell(appointment.date);
        const timeCell = createTableCell(appointment.time);
        const donorCell = createTableCell(appointment.donor_name);
        const attendeesCell = createTableCell(appointment.number);
        const statusCell = createStatusDropdownCell(appointment);

        // Action buttons
        const actionCell = createAppointmentActionButtons(appointment);

        // Append cells to row
        row.appendChild(dateCell);
        row.appendChild(timeCell);
        row.appendChild(donorCell);
        row.appendChild(attendeesCell);
        row.appendChild(statusCell);
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

function createStatusDropdownCell(appointment) {
    const cell = document.createElement('td');
    cell.classList.add('px-4', 'py-2');
    const dropdown = document.createElement('select');
    dropdown.classList.add('border', 'p-2', 'rounded');

    const statuses = ['Scheduled', 'Completed', 'Cancelled'];
    statuses.forEach(status => {
        const option = document.createElement('option');
        option.value = status;
        option.textContent = status;
        option.selected = appointment.status === status;
        dropdown.appendChild(option);
    });

    dropdown.addEventListener('change', () => {
        const newStatus = dropdown.value;
        openAppointmentStatusModal(appointment.id, appointment.status, newStatus, dropdown);
    });

    cell.appendChild(dropdown);
    return cell;
}

function createAppointmentActionButtons(appointment) {
    const cell = document.createElement('td');
    cell.classList.add('px-4', 'py-2', 'flex', 'justify-center', 'space-x-2');

    const deleteButton = createActionButton('trash', 'delete', appointment);

    cell.appendChild(deleteButton);

    return cell;
}

function createActionButton(icon, action, appointment) {
    const button = document.createElement('button');
    button.classList.add('bg-red-400', 'text-white', 'p-2', 'rounded', 'hover:bg-red-300');
    button.innerHTML = `<i class="fas fa-${icon}"></i>`;
    button.addEventListener('click', () => handleAppointmentAction(action, appointment.id));
    return button;
}

function handleAppointmentAction(action, appointmentId) {
    switch (action) {
        case 'delete':
            openDeleteModal(response => {
                if (response) {
                    deleteAppointment(appointmentId);
                }
            });
            break;
        default:
            console.error('Unknown action:', action);
    }
}

function deleteAppointment(appointmentId) {
    fetch(`https://lightpink-dogfish-795437.hostingersite.com/admin_mis/src/php/deleteAppointment.php?id=${appointmentId}`, {
        method: 'DELETE',
    })
        .then(response => {
            if (!response.ok) {
                throw new Error('Failed to delete appointment');
            }
            fetchAppointments(); // Refresh the appointment list after deletion
        })
        .catch(error => {
            console.error('Error deleting appointment:', error);
        });
}

function openAppointmentStatusModal(appointmentId, currentStatus, newStatus, dropdown) {
    const modal = document.getElementById("appointment-status-modal");
    modal.classList.remove("hidden");

    const confirmationMessage = document.getElementById("appointment-status-confirmation-message");
    if (confirmationMessage) {
        confirmationMessage.textContent = `Do you want to confirm the status change from "${currentStatus}" to "${newStatus}" for appointment ID: ${appointmentId}?`;

        document.getElementById("appointment-status-confirm-button").onclick = () => {
            updateAppointmentStatus(appointmentId, newStatus);
            closeModal("appointment-status-modal");
        };

        document.getElementById("appointment-status-cancel-button").onclick = () => {
            dropdown.value = currentStatus; // Revert to previous status
            closeModal("appointment-status-modal");
        };
    } else {
        console.error('Confirmation message element not found');
    }
}

function updateAppointmentStatus(appointmentId, newStatus) {
    fetch('https://lightpink-dogfish-795437.hostingersite.com/admin_mis/src/php/updateAppointmentStatus.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id: appointmentId, status: newStatus })
    })
        .then(response => {
            if (!response.ok) {
                throw new Error('Failed to update appointment status');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                fetchAppointments(); // Refresh the table
            } else {
                console.error('Failed to update appointment status:', data.error);
            }
        })
        .catch(error => {
            console.error('Error updating appointment status:', error);
        });
}

function displayNoAppointmentsMessage() {
    const tableBody = document.querySelector('#appointments-table tbody');
    tableBody.innerHTML = `
        <tr>
            <td colspan="5" class="text-center py-4">No appointments found or an error occurred.</td>
        </tr>
    `;
}



initAppointments(); // Initialize appointment management
