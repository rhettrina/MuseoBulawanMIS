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
    const tableBody = document.getElementById('appointment-table');
    tableBody.innerHTML = ''; // Clear existing rows

    if (appointments.length === 0) {
        displayNoDataMessage();
        return;
    }

    appointments.forEach(appointment => {
        const row = document.createElement('tr');
        row.classList.add('border-t', 'border-gray-300', 'text-center');

        // Create cells for each column
        row.innerHTML = `
            <td class="px-4 py-2">${appointment.date}</td>
            <td class="px-4 py-2">${appointment.donor_name}</td>
            <td class="px-4 py-2">${appointment.title}</td>
            <td class="px-4 py-2">${appointment.status}</td>
            <td class="px-4 py-2">${appointment.transfer_status}</td>
            <td class="px-4 py-2">${appointment.updated_date}</td>
            <td class="px-4 py-2 flex justify-center space-x-2">
                <button class="bg-green-500 text-white p-2 rounded hover:bg-green-600" onclick="handleAction('preview', ${appointment.id})">
                    <i class="fas fa-eye"></i>
                </button>
                <button class="bg-blue-500 text-white p-2 rounded hover:bg-blue-600" onclick="handleAction('edit', ${appointment.id})">
                    <i class="fas fa-edit"></i>
                </button>
                <button class="bg-red-500 text-white p-2 rounded hover:bg-red-600" onclick="handleAction('delete', ${appointment.id})">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        `;
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
