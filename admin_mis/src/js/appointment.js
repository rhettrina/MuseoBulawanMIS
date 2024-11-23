
function init(){
    fetchTotalAppointments();
    fetchAppointments();
}

function fetchTotalAppointments() {
    fetch('https://lightpink-dogfish-795437.hostingersite.com/admin_mis/src/php/fetchTotalAppointments.php')
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
    document.getElementById('total-approved').innerText = data.total_approved || 0;
    document.getElementById('total-rejected').innerText = data.total_rejected || 0;
    
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

function populateTable(appointments) {
    const tableBody = document.getElementById('appointment-table').querySelector('tbody');
    tableBody.innerHTML = ''; // Clear existing rows

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
        dateCell.textContent = appointment.appointment_date;

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
        statusCell.textContent = appointment.status;
        
        const createdAtCell = document.createElement('td');
        createdAtCell.classList.add('px-4', 'py-2','bg-white', 'border-black', 'border-t-2', 'border-b-2');
        createdAtCell.textContent = appointment.created_at;

        const actionCell = document.createElement('td');
        actionCell.classList.add('px-4', 'py-2', 'flex', 'justify-center', 'space-x-2', 'bg-white', 'border-black' , 'rounded-r-[15px]', 'border-t-2', 'border-b-2', 'border-r-2');

        // Add buttons with event listeners
        

            const editButton = document.createElement('button');
            editButton.classList.add('bg-transparent', 'text-black', 'p-2', 'rounded', 'hover:bg-orange-300');
            editButton.innerHTML = `<i class="fas fa-edit"></i>`;
            editButton.addEventListener('click', () => handleAction('edit', appointment));


            const deleteButton = document.createElement('button');
            deleteButton.classList.add('bg-transparent', 'text-black', 'p-2', 'rounded', 'hover:bg-orange-300');
            deleteButton.innerHTML = `<i class="fas fa-trash"></i>`;
            deleteButton.addEventListener('click', () => handleAction('delete', appointment.id));

            
            actionCell.appendChild(editButton);
            actionCell.appendChild(deleteButton);

        // Append cells to row
        row.appendChild(createdAtCell);
        row.appendChild(attendeeCell);
        row.appendChild(timeCell);
        
        row.appendChild(statusCell);
        row.appendChild(attendeesCountCell);
        
        
        row.appendChild(dateCell);
        row.appendChild(actionCell);

        tableBody.appendChild(row);
    });
}

function handleAction(action, appointmentId) {
    switch (action) {
        case 'edit':
            showAppointmentModal(appointmentId);
            init();
            break;
        case 'delete':
            openAppointmentDeleteModal((response) => {
                if (response) {
                    console.log(`Appointment with ID ${appointmentId} deleted.`);
                    init();
                } else {
                    console.log("Delete action canceled.");
                }
            });
            break;
        default:
            console.error('Unknown action:', action);
    }
}



function fetchAppointmentDetails(appointmentId) {
    fetch(`https://lightpink-dogfish-795437.hostingersite.com/admin_mis/src/php/previewAppointments.php?id=${appointmentId}`)
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        if (data.error) {
            console.error('Error fetching article:', data.error);
        } else {
            populateModal(data);
        }
    })
    .catch(error => {
        console.error('Error fetching article details:', error);
    });
    
}


function openAppointmentDeleteModal(callback) {
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


function displayNoDataMessage() {
    const tableBody = document.querySelector('tbody');
    tableBody.innerHTML = `
        <tr>
            <td colspan="7" class="text-center py-4">No appointment found or an error occurred.</td>
        </tr>
    `;
}




// Function to open the confirmation modal
function openConfirmationModal(callback) {
    const modal = document.getElementById("confirmation-modal");
    modal.classList.remove("hidden");
  
    // Handling button clicks
    document.getElementById("confirm-button").onclick = () => {
      callback(true);  // Return 'true' if 'Yes' is clicked
      closeModal("confirmation-modal");
    };
  
    document.getElementById("cancel-button").onclick = () => {
      callback(false);  // Return 'false' if 'No' is clicked
      closeModal("confirmation-modal");
    };
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
  


function openConfirmationModal(callback) {
    const modal = document.getElementById("confirmation-modal");
    modal.classList.remove("hidden"); // Show the confirmation modal

    document.getElementById("confirm-button").onclick = () => {
        callback(true); 
        closeModal("confirmation-modal");
    };

    document.getElementById("cancel-button").onclick = () => {
        callback(false); 
        closeModal("confirmation-modal");
    };
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.add("hidden"); // Add the 'hidden' class to hide the modal
    } else {
        console.error(`Modal with ID "${modalId}" not found.`);
    }
}

// Function to show and populate the modal
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

    // Show the modal
    if (modal) {
        modal.classList.remove('hidden');
    }
}




// Close modal logic
document.getElementById('close-appointment-modal').addEventListener('click', () => {
    const modal = document.getElementById('appointment-modal');
    if (modal) {
        modal.classList.add('hidden');
    }
});

document.getElementById('close-appointment-modal-btn').addEventListener('click', () => {
    const modal = document.getElementById('appointment-modal');
    if (modal) {
        modal.classList.add('hidden');
    }
});





init();
// Fetch appointments and populate the table
