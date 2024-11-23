
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
    document.getElementById('total-expected-visitors').innerText = errorMessage;
    document.getElementById('total-present').innerText = errorMessage;
}

// Function to populate the data from the PHP response for appointments
function populateTotalAppointmentData(data) {
    document.getElementById('total-appointments').innerText = data.total_appointments || 0;
    document.getElementById('total-approved').innerText = data.total_approved || 0;
    document.getElementById('total-rejected').innerText = data.total_rejected || 0;
    document.getElementById('total-expected-visitors').innerText = data.total_expected_visitors || 0;
    document.getElementById('total-present').innerText = data.total_present || 0;
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
        attendeeCell.textContent = appointment.donor_name;

        const attendeesCountCell = document.createElement('td');
        attendeesCountCell.classList.add('px-4', 'py-2', 'bg-white', 'border-black', 'border-t-2', 'border-b-2');
        attendeesCountCell.textContent = appointment.number_of_attendees;

        const statusCell = document.createElement('td');
        statusCell.classList.add('px-4', 'py-2', 'bg-white', 'border-black', 'border-t-2', 'border-b-2');
        statusCell.textContent = appointment.status;
        
        const updatedDateCell = document.createElement('td');
        updatedDateCell.classList.add('px-4', 'py-2','bg-white', 'border-black' , 'border-t-2', 'border-b-2');
        updatedDateCell.textContent = appointment.updated_date === "Not Edited" || !appointment.updated_date
            ? "Not Edited"
            : appointment.updated_date;

        const actionCell = document.createElement('td');
        actionCell.classList.add('px-4', 'py-2', 'flex', 'justify-center', 'space-x-2', 'bg-white', 'border-black' , 'rounded-r-[15px]', 'border-t-2', 'border-b-2', 'border-r-2');

        // Add buttons with event listeners
        const previewButton = document.createElement('button');
        previewButton.classList.add('bg-transparent', 'text-black' , 'p-2', 'rounded', 'hover:bg-orange-300');
        previewButton.innerHTML = `<i class="fas fa-eye"></i>`;
        previewButton.addEventListener('click', () => handleAction('preview', appointment));

        const editButton = document.createElement('button');
        editButton.classList.add('bg-transparent', 'text-black', 'p-2', 'rounded', 'hover:bg-orange-300');
        editButton.innerHTML = `<i class="fas fa-edit"></i>`;
        editButton.addEventListener('click', () => handleAction('edit', appointment));

        const deleteButton = document.createElement('button');
        deleteButton.classList.add('bg-transparent', 'text-black', 'p-2', 'rounded', 'hover:bg-orange-300');
        deleteButton.innerHTML = `<i class="fas fa-trash"></i>`;
        deleteButton.addEventListener('click', () => handleAction('delete', appointment));


        actionCell.appendChild(previewButton);
        actionCell.appendChild(editButton);
        actionCell.appendChild(deleteButton);

        // Append cells to row
        row.appendChild(dateCell);
        row.appendChild(attendeeCell);
        row.appendChild(timeCell);
        row.appendChild(statusCell);
        row.appendChild(attendeesCountCell);
        row.appendChild(updatedDateCell);
        row.appendChild(actionCell);
        

        tableBody.appendChild(row);
    });
}


function handleAction(action, appointmentId) {
    switch (action) {
        case 'preview':
            console.log(`Preview article with ID: ${appointmentId}`);
            // Implement preview functionality here
            break;
        case 'edit':
            console.log(`Edit article with ID: ${appointmentId}`);
            // Implement edit functionality here
            break;
        case 'delete':
            // Show confirmation modal before deleting
            openDeleteModal((response) => {
                if (response) {
                    console.log(`Article with ID ${appointmentId} deleted.`);
                    // Implement delete functionality here
                    // For example: deleteArticle(articleId);
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

init();
// Fetch appointments and populate the table
