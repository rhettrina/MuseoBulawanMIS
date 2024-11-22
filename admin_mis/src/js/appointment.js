
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
            return response.json();
        })
        .then(data => {
            if (data.error) {
                console.error('Error from PHP:', data.error);
                document.getElementById('total-appointments').innerText = "Error fetching data";
            } else {
                document.getElementById('total-appointments').innerText = data.total_articles;
            }
        })
        .catch(error => {
            console.error('Error fetching total appointments:', error);
            document.getElementById('total-appointments').innerText = "Error fetching data";
        });
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
        
        
}
function populateTable(appointments) {
    const tableBody = document.getElementById('appointment-table').querySelector('tbody');
    tableBody.innerHTML = ''; // Clear existing rows

    if (appointments.length === 0) {
        displayNoDataMessage();
        return;
    }

    appointments.forEach(appointment => {
        const row = document.createElement('tr');
        row.classList.add('border-t', 'border-gray-300', 'text-center');

        const dateCell = createCell('td', appointment.appointment_date, ['px-4', 'py-2', 'bg-white', 'border-black', 'rounded-l-[15px]', 'border-t-2', 'border-b-2', 'border-l-2']);
        const timeCell = createCell('td', appointment.appointment_time, ['px-4', 'py-2', 'bg-white', 'border-black', 'border-t-2', 'border-b-2']);
        const attendeeCell = createCell('td', appointment.donor_name, ['px-4', 'py-2', 'bg-white', 'border-black', 'border-t-2', 'border-b-2']);
        const attendeesCountCell = createCell('td', appointment.number_of_attendees, ['px-4', 'py-2', 'bg-white', 'border-black', 'border-t-2', 'border-b-2']);
        const statusCell = createCell('td', appointment.status, ['px-4', 'py-2', 'bg-white', 'border-black', 'border-t-2', 'border-b-2']);
        const updatedDateCell = createCell('td', appointment.updated_date, ['px-4', 'py-2', 'bg-white', 'border-black', 'border-t-2', 'border-b-2']);

        const actionCell = document.createElement('td');
        actionCell.classList.add('px-4', 'py-2', 'bg-white', 'border-black', 'border-t-2', 'border-b-2');

        const previewButton = createButton('fas fa-eye', 'Preview', () => handleAction('preview', appointment.id));
        const editButton = createButton('fas fa-edit', 'Edit', () => handleAction('edit', appointment.id));
        const deleteButton = createButton('fas fa-trash', 'Delete', () => handleAction('delete', appointment.id));

        actionCell.appendChild(previewButton);
        actionCell.appendChild(editButton);
        actionCell.appendChild(deleteButton);

        row.append(dateCell, timeCell, attendeeCell, attendeesCountCell, statusCell, updatedDateCell, actionCell);
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
