
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
        dateCell.textContent = appointment.created_at;

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
        createdAtCell.textContent = appointment.appointment_date;

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
        row.appendChild(dateCell);
        row.appendChild(attendeeCell);
        row.appendChild(timeCell);
        
        row.appendChild(statusCell);
        row.appendChild(attendeesCountCell);
        
        row.appendChild(createdAtCell);
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
    console.log(`Fetching details for appointment ID: ${appointmentId}`);
    fetch(`https://lightpink-dogfish-795437.hostingersite.com/admin_mis/src/php/previewAppointments.php?sort=${appointmentId}`);
    const appointment = {
        name: "John Doe",
        email: "john.doe@example.com",
        phone: "123-456-7890",
        address: "123 Main Street, Cityville",
        purpose: "Business Meeting",
        organization: "ABC Corp",
        populationCount: 5,
        preferredDate: "2024-11-30",
        preferredTime: "10:00 AM",
        notes: "Meeting to discuss partnership."
    };

    // Populate modal fields
    document.getElementById('appointment-name').textContent = appointment.name || 'N/A';
    document.getElementById('appointment-email').textContent = appointment.email || 'N/A';
    document.getElementById('appointment-phone').textContent = appointment.phone || 'N/A';
    document.getElementById('appointment-address').textContent = appointment.address || 'N/A';
    document.getElementById('appointment-purpose').textContent = appointment.purpose || 'N/A';
    document.getElementById('appointment-organization').textContent = appointment.organization || 'N/A';
    document.getElementById('appointment-population').textContent = appointment.populationCount || 'N/A';
    document.getElementById('appointment-date').textContent = appointment.preferredDate || 'N/A';
    document.getElementById('appointment-time').textContent = appointment.preferredTime || 'N/A';
    document.getElementById('appointment-notes').textContent = appointment.notes || 'N/A';


    // Show the modal
    const modal = document.getElementById('appointment-modal');
    if (modal) {
        modal.classList.remove('hidden');
    } else {
        console.error('Appointment modal not found.');
    }
}


function openDeleteModal(callback) {
    const userResponse = confirm("Are you sure you want to delete this appointment?");
    callback(userResponse);
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
    document.getElementById('appointment-name').textContent = appointment.name || 'N/A';
    document.getElementById('appointment-email').textContent = appointment.email || 'N/A';
    document.getElementById('appointment-phone').textContent = appointment.phone || 'N/A';
    document.getElementById('appointment-address').textContent = appointment.address || 'N/A';
    document.getElementById('appointment-purpose').textContent = appointment.purpose || 'N/A';
    document.getElementById('appointment-organization').textContent = appointment.organization || 'N/A';
    document.getElementById('appointment-population').textContent = appointment.population || 'N/A';
    document.getElementById('appointment-date').textContent = appointment.date || 'N/A';
    document.getElementById('appointment-time').textContent = appointment.time || 'N/A';
    document.getElementById('appointment-notes').textContent = appointment.notes || 'N/A';

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

// Example buttons for testing
const editButton = document.createElement('button');
editButton.classList.add('bg-transparent', 'text-black', 'p-2', 'rounded', 'hover:bg-orange-300');
editButton.innerHTML = `<i class="fas fa-edit"></i>`;
editButton.addEventListener('click', () => handleAction('edit', {
    name: "Juan Dela Cruz",
    email: "juan@example.com",
    phone: "09786734766",
    address: "Ofelia Street, Barangay 2, Daet, Camarines Norte",
    purpose: "Educational Tour",
    organization: "Juan Dela Cruz Elementary School",
    population: "51",
    date: "August 25",
    time: "10:30-11:59",
    notes: "Good morning, there might still be changes to our count as some students are catching up."
}, 1));

const deleteButton = document.createElement('button');
deleteButton.classList.add('bg-transparent', 'text-black', 'p-2', 'rounded', 'hover:bg-orange-300');
deleteButton.innerHTML = `<i class="fas fa-trash"></i>`;
deleteButton.addEventListener('click', () => handleAction('delete', null, 1));

// Append buttons for testing
const actionCell = document.createElement('div');
actionCell.appendChild(editButton);
actionCell.appendChild(deleteButton);

document.body.appendChild(actionCell);

// Handle action function
function handleAction(action, appointment, id) {
    if (action === 'edit') {
        // Show modal with appointment details
        showAppointmentModal(appointment);
    } else if (action === 'delete') {
        console.log(`Delete appointment with ID: ${id}`);
    }
}



init();
// Fetch appointments and populate the table
