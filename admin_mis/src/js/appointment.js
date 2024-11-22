function init() {
    fetchTotalAppointments();
    fetchAppointments();  // Default to 'newest' sort
}

function fetchTotalAppointments() {
    fetch('https://lightpink-dogfish-795437.hostingersite.com/admin_mis/src/php/fetchTotalAppointments.php')
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                handleError('Error fetching total appointments:', data.error);
                document.getElementById('total-appointments').innerText = "Error fetching data";
            } else {
                document.getElementById('total-appointments').innerText = data.total_appointments;
            }
        })
        .catch(error => handleError('Error fetching total appointments:', error));
}

// Fetch and populate the appointments table
function fetchAppointments(sort = 'newest') {
    fetch(`https://lightpink-dogfish-795437.hostingersite.com/admin_mis/src/php/fetchAppointments.php?sort=${sort}`)
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                handleError('Error fetching appointments:', data.error);
                displayNoDataMessage();
            } else {
                populateTable(data);
            }
        })
        .catch(error => {
            handleError('Error fetching appointments:', error);
            displayNoDataMessage();
        });
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

        // Create cells
        const dateCell = document.createElement('td');
        dateCell.classList.add('px-4', 'py-2');
        dateCell.textContent = appointment.date;

        const nameCell = document.createElement('td');
        nameCell.classList.add('px-4', 'py-2');
        nameCell.textContent = appointment.donor_name;

        const timeCell = document.createElement('td');
        timeCell.classList.add('px-4', 'py-2');
        timeCell.textContent = appointment.time;

        const numberCell = document.createElement('td');
        numberCell.classList.add('px-4', 'py-2');
        numberCell.textContent = appointment.number;

        const statusCell = document.createElement('td');
        statusCell.classList.add('px-4', 'py-2');
        statusCell.textContent = "Pending"; // Placeholder status

        const confirmationCell = document.createElement('td');
        confirmationCell.classList.add('px-4', 'py-2');
        confirmationCell.textContent = "N/A"; // Placeholder confirmation

        const actionCell = document.createElement('td');
        actionCell.classList.add('px-4', 'py-2', 'flex', 'justify-center', 'space-x-2');

        // Add buttons with event listeners
        const previewButton = document.createElement('button');
        previewButton.classList.add('bg-green-500', 'text-white', 'p-2', 'rounded', 'hover:bg-green-600');
        previewButton.innerHTML = `<i class="fas fa-eye"></i>`;
        previewButton.addEventListener('click', () => handleAction('preview', appointment.id));

        const editButton = document.createElement('button');
        editButton.classList.add('bg-blue-500', 'text-white', 'p-2', 'rounded', 'hover:bg-blue-600');
        editButton.innerHTML = `<i class="fas fa-edit"></i>`;
        editButton.addEventListener('click', () => handleAction('edit', appointment.id));

        const deleteButton = document.createElement('button');
        deleteButton.classList.add('bg-red-500', 'text-white', 'p-2', 'rounded', 'hover:bg-red-600');
        deleteButton.innerHTML = `<i class="fas fa-trash"></i>`;
        deleteButton.addEventListener('click', () => handleAction('delete', appointment.id));

        actionCell.appendChild(previewButton);
        actionCell.appendChild(editButton);
        actionCell.appendChild(deleteButton);

        row.appendChild(dateCell);
        row.appendChild(nameCell);
        row.appendChild(timeCell);
        row.appendChild(numberCell);
        row.appendChild(statusCell);
        row.appendChild(confirmationCell);
        row.appendChild(actionCell);

        tableBody.appendChild(row);
    });
}


function handleAction(action, appointmentId) {
    switch (action) {
        case 'preview':
            console.log(`Preview appointment with ID: ${appointmentId}`);
            break;
        case 'edit':
            console.log(`Edit appointment with ID: ${appointmentId}`);
            break;
        case 'delete':
            console.log(`Delete appointment with ID: ${appointmentId}`);
            break;
        default:
            console.error('Unknown action:', action);
    }
}

function displayNoDataMessage() {
    const tableBody = document.getElementById('appointment-table');
    tableBody.innerHTML = `
        <tr>
            <td colspan="7" class="text-center py-4">No appointments found or an error occurred.</td>
        </tr>
    `;
}

function handleError(message, error) {
    console.error(message, error);
    alert(message); // Optional: Display a message to the user
}

document.getElementById("sort-appointment").addEventListener("change", function () {
    const sortOption = this.value;
    fetchAppointments(sortOption);
});

// Modal toggling
document.getElementById("create-appointment-button").addEventListener("click", () => {
    document.getElementById("create-appointment-modal").classList.remove("hidden");
});

document.getElementById("cancel-button").addEventListener("click", () => {
    document.getElementById("create-appointment-modal").classList.add("hidden");
});

// Initialize on page load
window.onload = init;
