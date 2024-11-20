function init() {
    fetchTotalAppointments();
    fetchAppointments();
}

function fetchTotalAppointments() {
    fetch('https://example.com/api/fetchTotalAppointments.php')
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
                document.getElementById('total-appointments').innerText = data.total_appointments;
            }
        })
        .catch(error => {
            console.error('Error fetching total appointments:', error);
            document.getElementById('total-appointments').innerText = "Error fetching data";
        });
}

// Fetch and populate the appointments table
function fetchAppointments(sort = 'newest') {
    fetch(`https://example.com/api/fetchAppointments.php?sort=${sort}`)
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

function populateTable(appointments) {
    const tableBody = document.querySelector('tbody');
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
        nameCell.textContent = appointment.client_name;

        const statusCell = document.createElement('td');
        statusCell.classList.add('px-4', 'py-2');
        statusCell.textContent = appointment.status;

        const updatedDateCell = document.createElement('td');
        updatedDateCell.classList.add('px-4', 'py-2');
        updatedDateCell.textContent = appointment.updated_at === "Not Edited" || !appointment.updated_at
            ? "Not Edited"
            : appointment.updated_at;

        const actionCell = document.createElement('td');
        actionCell.classList.add('px-4', 'py-2', 'flex', 'justify-center', 'space-x-2');

        // Add buttons with event listeners
        const previewButton = document.createElement('button');
        previewButton.classList.add('bg-orange-400', 'text-white', 'p-2', 'rounded', 'hover:bg-orange-300');
        previewButton.innerHTML = `<i class="fas fa-eye"></i>`;
        previewButton.addEventListener('click', () => handleAction('preview', appointment.id));

        const editButton = document.createElement('button');
        editButton.classList.add('bg-orange-400', 'text-white', 'p-2', 'rounded', 'hover:bg-orange-300');
        editButton.innerHTML = `<i class="fas fa-edit"></i>`;
        editButton.addEventListener('click', () => handleAction('edit', appointment.id));

        const deleteButton = document.createElement('button');
        deleteButton.classList.add('bg-orange-400', 'text-white', 'p-2', 'rounded', 'hover:bg-orange-300');
        deleteButton.innerHTML = `<i class="fas fa-trash"></i>`;
        deleteButton.addEventListener('click', () => handleAction('delete', appointment.id));

        actionCell.appendChild(previewButton);
        actionCell.appendChild(editButton);
        actionCell.appendChild(deleteButton);

        row.appendChild(dateCell);
        row.appendChild(nameCell);
        row.appendChild(statusCell);
        row.appendChild(updatedDateCell);
        row.appendChild(actionCell);

        tableBody.appendChild(row);
    });
}

function handleAction(action, appointmentId) {
    switch (action) {
        case 'preview':
            console.log(`Preview appointment with ID: ${appointmentId}`);
            // Implement preview functionality here
            break;
        case 'edit':
            console.log(`Edit appointment with ID: ${appointmentId}`);
            // Implement edit functionality here
            break;
        case 'delete':
            console.log(`Delete appointment with ID: ${appointmentId}`);
            // Confirm before deleting
            break;
        default:
            console.error('Unknown action:', action);
    }
}

function displayNoDataMessage() {
    const tableBody = document.querySelector('tbody');
    tableBody.innerHTML = `
        <tr>
            <td colspan="5" class="text-center py-4">No appointments found or an error occurred.</td>
        </tr>
    `;
}

document.getElementById("sort").addEventListener("change", function () {
    const sortOption = this.value;
    fetchAppointments(sortOption);
});

function showCreateAppointmentModal() {
    const modal = document.getElementById('create-appointment-modal');
    modal.classList.remove('hidden');
}

document.getElementById('create-appointment-button').addEventListener('click', showCreateAppointmentModal);

function hideCreateAppointmentModal() {
    const modal = document.getElementById('create-appointment-modal');
    modal.classList.add('hidden');
}

document.getElementById('cancel-button').addEventListener('click', hideCreateAppointmentModal);
