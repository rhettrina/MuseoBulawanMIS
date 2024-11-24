// Initialize the application
function init() {
    fetchTotalAppointments();
    fetchAppointments();
}

// Fetch total appointments data
function fetchTotalAppointments() {
    fetch('https://museobulawan.online/development/admin_mis/src/php/fetchTotalAppointments.php')
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok: ' + response.statusText);
            }
            return response.json(); // Parse JSON response
        })
        .then(data => {
            if (data.error) {
                console.error('Error from PHP:', data.error);
                displayAppointmentErrorMessages(); // Display error messages
            } else {
                populateTotalAppointmentData(data); // Populate appointment data on success
            }
        })
        .catch(error => {
            console.error('Error fetching total appointments:', error);
            displayAppointmentErrorMessages(); // Handle fetch errors
        });
}

// Function to display error messages in all relevant fields for appointments
function displayAppointmentErrorMessages() {
    const errorMessage = "Error fetching data";
    document.getElementById('total-appointments').innerText = errorMessage;
    document.getElementById('total-approved').innerText = errorMessage;
    document.getElementById('total-rejected').innerText = errorMessage;
}

// Function to populate the data from the PHP response for appointments
function populateTotalAppointmentData(data) {
    document.getElementById('total-appointments').innerText = data.total_appointments || 0;
    document.getElementById('total-approved').innerText = data.approved_appointments || 0;
    document.getElementById('total-rejected').innerText = data.rejected_appointments || 0;
}

// Event listener for sorting appointments
document.getElementById('sortA').addEventListener('change', function () {
    const selectedSort = this.value; // Get selected value
    fetchAppointments(selectedSort); // Fetch appointments based on sort
});

// Fetch and populate the appointment table
function fetchAppointments(sort = 'newest') {
    fetch(`https://museobulawan.online/development/admin_mis/src/php/fetchAppointments.php?sort=${sort}`)
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
            console.error('Error fetching appointments:', error);
            displayNoDataMessage();
        });
}

// Populate the appointment table
function populateTable(appointments) {
    const tableBody = document.getElementById('appointment-table').querySelector('tbody');
    tableBody.innerHTML = ''; 
    // Check if there are appointments
    if (appointments.length === 0) {
        displayNoDataMessage();
        return;
    }

    // Populate table rows
    appointments.forEach(appointment => {
        const row = document.createElement('tr');
        row.classList.add('border-t', 'border-gray-300', 'text-center');

        // Create and populate cells
        const dateCell = document.createElement('td');
        dateCell.classList.add('px-4', 'py-2', 'bg-white', 'border-black', 'rounded-l-[15px]', 'border-t-2', 'border-b-2', 'border-l-2');
        dateCell.textContent = appointment.appointment_created_at;

        const timeCell = document.createElement('td');
        timeCell.classList.add('px-4', 'py-2', 'bg-white', 'border-black', 'border-t-2', 'border-b-2');
        timeCell.textContent = appointment.appointment_time;

        const attendeeCell = document.createElement('td');
        attendeeCell.classList.add('px-4', 'py-2', 'bg-white', 'border-black', 'border-t-2', 'border-b-2');
        attendeeCell.textContent = appointment.visitor_name;

        const attendeesCountCell = document.createElement('td');
        attendeesCountCell.classList.add('px-4', 'py-2', 'bg-white', 'border-black', 'border-t-2', 'border-b-2');
        attendeesCountCell.textContent = appointment.number_of_attendees;

        const statusCell = document.createElement('td');
        statusCell.classList.add('px-4', 'py-2', 'bg-white', 'border-black', 'border-t-2', 'border-b-2');
        statusCell.textContent = appointment.appointment_status;

        const confirmationDateCell = document.createElement('td');
        confirmationDateCell.classList.add('px-4', 'py-2', 'bg-white', 'border-black', 'border-t-2', 'border-b-2');
        confirmationDateCell.textContent = appointment.appointment_confirmation_date;

        const actionCell = document.createElement('td');
        actionCell.classList.add('px-4', 'py-2', 'flex', 'justify-center', 'space-x-2', 'bg-white', 'border-black', 'rounded-r-[15px]', 'border-t-2', 'border-b-2', 'border-r-2');

        // Add buttons with event listeners
        const editButton = document.createElement('button');
        editButton.classList.add('bg-transparent', 'text-black', 'p-2', 'rounded', 'hover:bg-orange-300');
        editButton.innerHTML = `<i class="fas fa-edit"></i>`;
        editButton.addEventListener('click', () => handleAction('edit', appointment));

        const deleteButton = document.createElement('button');
        deleteButton.classList.add('bg-transparent', 'text-black', 'p-2', 'rounded', 'hover:bg-orange-300');
        deleteButton.innerHTML = `<i class="fas fa-trash"></i>`;
        deleteButton.addEventListener('click', () => handleAction('delete', appointment.fkID));

        actionCell.appendChild(editButton);
        actionCell.appendChild(deleteButton);

        // Append cells to row
        row.appendChild(dateCell);
        row.appendChild(attendeeCell);
        row.appendChild(timeCell);
        row.appendChild(statusCell);
        row.appendChild(attendeesCountCell);
        row.appendChild(confirmationDateCell);
        row.appendChild(actionCell);

        tableBody.appendChild(row);
    });
}

// Handle actions for edit and delete
function handleAction(action, data) {
    switch (action) {
        case 'edit':
            showAppointmentModal(data); // 'data' is the appointment object
            break;
        case 'delete':
            openAppointmentDeleteModal((response) => {
                if (response) {
                    deleteAppointment(data) // 'data' is appointment.id
                        .then(() => {
                            console.log(`Appointment with ID ${data} deleted.`);
                            init(); // Refresh the data/display
                        })
                        .catch(error => {
                            console.error('Error deleting appointment:', error);
                            alert('An error occurred while deleting the appointment.');
                        });
                } else {
                    console.log("Delete action canceled.");
                }
            });
            break;
        default:
            console.error('Unknown action:', action);
    }
}
/*-----------------------------------------------------------------------------------------------------------------------------------------------*/
// Approve button handler
document.getElementById("approve-appointment-btn").addEventListener("click", () => {
    const appointmentId = getAppointmentId();
    console.log(`Updating status to approved for appointment ID: ${appointmentId}`);
    updateAppointmentStatus(appointmentId, "approved");
});

// Reject button handler
document.getElementById("reject-appointment-btn").addEventListener("click", () => {
    const appointmentId = getAppointmentId();
    console.log(`Updating status to rejected for appointment ID: ${appointmentId}`);
    updateAppointmentStatus(appointmentId, "rejected");
});

// Get the appointment ID from the modal
function getAppointmentId() {
    return document.getElementById("appointment-modal").dataset.appointmentId;
}


// Function to update appointment status
function updateAppointmentStatus(appointmentId, status) {
    // Validate inputs
    if (!appointmentId || !status) {
        console.error("Invalid appointmentId or status:", appointmentId, status);
        alert("Invalid appointment ID or status.");
        return;
    }

    const payload = JSON.stringify({
        appointmentId: appointmentId, // Consistent with PHP expectation
        status: status                // Consistent with PHP expectation
    });

    // Log the payload before sending
    console.log("Payload being sent:", payload);
    console.log("Sending request to processAppointment.php...");

    fetch('https://museobulawan.online/development/admin_mis/src/php/processAppointment.php', { 
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: payload
    })
    .then(response => {
        console.log("Received response with status:", response.status);
        if (!response.ok) {
            throw new Error(`HTTP error! Status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        console.log("Response from PHP:", data);
        if (data.success) {
            alert("Appointment status updated successfully!");
            closeModal();
            fetchAppointments(); // Refresh the appointment list or update the UI
        } else {
            console.error("Error response from PHP:", data.message);
            alert("Failed to update appointment: " + data.message);
        }
    })
    .catch(error => {
        console.error("Error updating appointment:", error);
        alert('An error occurred while updating the appointment status.');
    });
}

// Approve button handler
document.getElementById("approve-appointment-btn").addEventListener("click", () => {
    const appointmentId = getAppointmentId();
    console.log(`Updating status to approved for appointment ID: ${appointmentId}`);
    updateAppointmentStatus(appointmentId, "approved");
});

// Reject button handler
document.getElementById("reject-appointment-btn").addEventListener("click", () => {
    const appointmentId = getAppointmentId();
    console.log(`Updating status to rejected for appointment ID: ${appointmentId}`);
    updateAppointmentStatus(appointmentId, "rejected");
});

// Get the appointment ID from the modal
function getAppointmentId() {
    const modal = document.getElementById("appointment-modal");
    const appointmentId = modal.dataset.appointmentId;
    if (!appointmentId) {
        console.error("No appointment ID found in the modal.");
    }
    return appointmentId;
}

// Close modal function
function closeModal() {
    const modal = document.getElementById("appointment-modal");
    if (modal) {
        modal.classList.add("hidden");
    } else {
        console.error("Modal element not found.");
    }
}

/*-----------------------------------------------------------------------------------------------------------------------------------------------*/




// Function to display a message when no data is available
function displayNoDataMessage() {
    const tableBody = document.querySelector('tbody');
    tableBody.innerHTML = `
        <tr>
            <td colspan="7" class="text-center py-4">No appointment found or an error occurred.</td>
        </tr>
    `;
}



// Function to show and populate the appointment modal
function showAppointmentModal(appointment) {
    if (!appointment) {
        console.error('Appointment data is undefined or null');
        return;
    }   
    const modal = document.getElementById('appointment-modal');

    // Populate modal fields
    document.getElementById('appointment-name').textContent = appointment.visitor_name || 'N/A';
    document.getElementById('appointment-email').textContent = appointment.visitor_email || 'N/A';
    document.getElementById('appointment-phone').textContent = appointment.visitor_phone || 'N/A';
    document.getElementById('appointment-address').textContent = appointment.visitor_address || 'N/A';
    document.getElementById('appointment-purpose').textContent = appointment.appointment_purpose || 'N/A';
    document.getElementById('appointment-organization').textContent = appointment.visitor_organization || 'N/A';
    document.getElementById('appointment-population').textContent = appointment.number_of_attendees || 'N/A';
    document.getElementById('appointment-date').textContent = appointment.appointment_date || 'N/A';
    document.getElementById('appointment-time').textContent = appointment.appointment_time || 'N/A';
    document.getElementById('appointment-notes').textContent = appointment.appointment_notes || 'N/A';

    modal.dataset.appointmentId = appointment.formID;
    // Show the modal
    if (modal) {
        modal.classList.remove('hidden');
    }
    
}

// Close modal logic
document.getElementById('close-appointment-modal').addEventListener('click', () => {
    closeModal('appointment-modal');
});

document.getElementById('close-appointment-modal-btn').addEventListener('click', () => {
    closeModal('appointment-modal');
});

// Function to close a modal by ID
function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.add('hidden');
    } else {
        console.error(`Modal with ID "${modalId}" not found.`);
    }
}

// Function to open the delete confirmation modal
function openAppointmentDeleteModal(callback) {
    const modal = document.getElementById("delete-modal");

    if (!modal) {
        console.error("Delete modal not found.");
        return;
    }

    modal.classList.remove("hidden"); // Show the modal

    // Get buttons
    const confirmButton = document.getElementById("delete-confirm-button");
    const cancelButton = document.getElementById("delete-cancel-button");

    if (!confirmButton || !cancelButton) {
        console.error("Delete modal buttons not found.");
        return;
    }

    // Define handlers
    const confirmHandler = () => {
        callback(true); // Confirm deletion
        closeModal("delete-modal");
        cleanupEventListeners(); // Clean up event listeners
    };

    const cancelHandler = () => {
        callback(false); // Cancel deletion
        closeModal("delete-modal");
        cleanupEventListeners(); // Clean up event listeners
    };

    // Add event listeners
    confirmButton.addEventListener("click", confirmHandler);
    cancelButton.addEventListener("click", cancelHandler);

    // Function to remove event listeners to prevent duplicates
    function cleanupEventListeners() {
        confirmButton.removeEventListener("click", confirmHandler);
        cancelButton.removeEventListener("click", cancelHandler);
    }
}

function deleteAppointment(fkID) {
    return fetch(`https://museobulawan.online/development/admin_mis/src/php/deleteAppointments.php?id=${fkID}`, {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
        },
    })
    .then(response => {
        return response.json().then(data => {
            if (response.ok) {
                return data;
            } else {
                throw new Error(data.error || 'Failed to delete appointment');
            }
        });
    });
}

// Start the application
init();
