(() => {
    function init() {
        console.log("Initializing artifact page...");
        fetchDonations(); // Fetch and display donations
        fetchTotalDonations(); // Fetch and display donation stats
    }

    function cleanup() {
        console.log("Cleaning up artifact page...");
        // Perform cleanup logic, such as removing event listeners
        const sortsDropdown = document.getElementById("sorts");
        if (sortsDropdown) {
            sortsDropdown.removeEventListener("change", handleSortChange);
        }
        const tableBody = document.querySelector("tbody");
        if (tableBody) tableBody.innerHTML = ""; // Clear table content
    }

    window.artifactPage = { init, cleanup };
})();

// Fetch total donation statistics
function fetchTotalDonations() {
    fetch('https://museobulawan.online/development/admin_mis/src/php/fetchTotalDonations.php')
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok: ' + response.statusText);
            }
            return response.json(); // Parse JSON response
        })
        .then(data => {
            if (data.error) {
                console.error('Error from PHP:', data.error);
                displayErrorMessages();
            } else {
                populateTotalDonationData(data);
            }
        })
        .catch(error => {
            console.error('Error fetching total donations:', error);
            displayErrorMessages();
        });
}

// Fetch and populate the donations table
function fetchDonations(sort = 'newest') {
    fetch(`https://museobulawan.online/development/admin_mis/src/php/fetchDonations.php?sort=${sort}`)
        .then(response => response.json())
        .then(data => {
            const tableBody = document.getElementById('donations-table').querySelector('tbody');
            tableBody.innerHTML = ''; // Clear existing rows before appending
            if (data.length) {
                populateTable(data);
            } else {
                displayNoDataMessage();
            }
        })
        .catch(error => {
            console.error('Error fetching donations:', error);
            displayNoDataMessage();
        });
}

// Handle sorting logic
function handleSortChange(event) {
    console.log("Sorting donations...");
    const sortOption = event.target.value;
    fetchDonations(sortOption);
}

// Populate the donations table
function populateTable(donations) {
    const tableBody = document.getElementById('donations-table').querySelector('tbody');
    tableBody.innerHTML = ''; // Clear existing rows

    if (donations.length === 0) {
        displayNoDataMessage();
        return;
    }

    donations.forEach(donation => {
        const row = document.createElement('tr');
        row.classList.add('border-t', 'border-gray-300', 'text-center');

        const dateCell = createTableCell(donation.submission_date, ['px-4', 'py-0', 'bg-white', 'border-black', 'rounded-l-[15px]', 'border-t-2', 'border-b-2', 'border-l-2']);
        const donorCell = createTableCell(donation.donor_name, ['px-4', 'py-0', 'bg-white', 'border-black', 'border-t-2', 'border-b-2']);
        const titleCell = createTableCell(donation.artifact_title, ['px-4', 'py-2', 'bg-white', 'border-black', 'border-t-2', 'border-b-2']);
        const typeCell = createTableCell(donation.form_type, ['px-4', 'py-0', 'bg-white', 'border-black', 'border-t-2', 'border-b-2']);
        const statusCell = createTableCell(donation.status, ['px-4', 'py-0', 'bg-white', 'border-black', 'border-t-2', 'border-b-2']);
        const transferStatusCell = document.createElement('td');
        transferStatusCell.classList.add('px-4', 'py-0', 'bg-white', 'border-black', 'border-t-2', 'border-b-2');
        transferStatusCell.appendChild(createTransferStatusCell(donation));

        const updatedDateCell = createTableCell(donation.updated_date || "Not Edited", ['px-4', 'py-0', 'bg-white', 'border-black', 'border-t-2', 'border-b-2']);
        const actionCell = document.createElement('td');
        actionCell.classList.add('px-4', 'py-0', 'bg-white', 'border-black', 'rounded-r-[15px]', 'border-t-2', 'border-b-2', 'border-r-2');
        actionCell.appendChild(createActionButtons(donation));

        row.appendChild(dateCell);
        row.appendChild(donorCell);
        row.appendChild(titleCell);
        row.appendChild(typeCell);
        row.appendChild(statusCell);
        row.appendChild(transferStatusCell);
        row.appendChild(updatedDateCell);
        row.appendChild(actionCell);

        tableBody.appendChild(row);
    });
}

// Create a reusable table cell
function createTableCell(content, classes = []) {
    const cell = document.createElement('td');
    cell.textContent = content;
    classes.forEach(className => cell.classList.add(className));
    return cell;
}

// Display a message when no data is available
function displayNoDataMessage() {
    const tableBody = document.querySelector('tbody');
    tableBody.innerHTML = `
        <tr>
            <td colspan="8" class="text-center py-4">No donations found or an error occurred.</td>
        </tr>
    `;
}

// Display error messages in statistics fields
function displayErrorMessages() {
    const errorMessage = "Error fetching data";
    document.getElementById('total-donations').innerText = errorMessage;
    document.getElementById('total-accepted').innerText = errorMessage;
    document.getElementById('total-rejected').innerText = errorMessage;
    document.getElementById('total-donform').innerText = errorMessage;
    document.getElementById('total-lendform').innerText = errorMessage;
}

// Populate total donation statistics
function populateTotalDonationData(data) {
    document.getElementById('total-donations').innerText = data.total_donations || 0;
    document.getElementById('total-accepted').innerText = data.accepted_donations || 0;
    document.getElementById('total-rejected').innerText = data.rejected_donations || 0;
    document.getElementById('total-donform').innerText = data.total_donation_forms || 0;
    document.getElementById('total-lendform').innerText = data.total_lending_forms || 0;
}

// Create dropdown for transfer status
function createTransferStatusCell(donation) {
    const dropdown = document.createElement('select');
    dropdown.classList.add('border', 'rounded');

    ['Acquired', 'Failed', 'Pending'].forEach(status => {
        const option = document.createElement('option');
        option.value = status.toUpperCase();
        option.textContent = status;
        option.selected = donation.transfer_status.toUpperCase() === status.toUpperCase();
        dropdown.appendChild(option);
    });

    dropdown.addEventListener('change', () => {
        const newStatus = dropdown.value;
        openStatusModal(donation.donID, donation.transfer_status, newStatus, dropdown);
    });

    return dropdown;
}

// Create action buttons for each row
function createActionButtons(donation) {
    const cell = document.createElement('div');
    cell.classList.add('flex', 'justify-center', 'space-x-2');

    const actions = [
        { icon: 'eye', action: 'preview' },
        { icon: 'edit', action: 'edit' },
        { icon: 'trash', action: 'delete' },
    ];

    actions.forEach(({ icon, action }) => {
        const button = document.createElement('button');
        button.classList.add('bg-transparent', 'text-black', 'p-2', 'rounded', 'hover:bg-orange-300');
        button.innerHTML = `<i class="fas fa-${icon}"></i>`;
        button.addEventListener('click', () => handleAction(action, donation));
        cell.appendChild(button);
    });

    return cell;
}

// Handle actions (edit, delete, etc.)
function handleAction(action, donation) {
    switch (action) {
        case 'preview':
            console.log(`Preview ${donation.form_type} with ID: ${donation.donID}`);
            break;
        case 'edit':
            openFormModal(donation.donID, donation.form_type);
            break;
        case 'delete':
            confirmDeleteDonation(donation.donID);
            break;
        default:
            console.error('Unknown action:', action);
    }
}

// Confirm and delete donation
function confirmDeleteDonation(donID) {
    openDeleteModal((confirmed) => {
        if (confirmed) {
            deleteDonation(donID);
        }
    });
}

// Perform the deletion
function deleteDonation(donID) {
    fetch(`https://museobulawan.online/development/admin_mis/src/php/deleteDonations.php?id=${donID}`, { method: 'DELETE' })
        .then(response => {
            if (!response.ok) throw new Error('Failed to delete the donation.');
            return response.json();
        })
        .then(data => {
            if (data.message) {
                alert(data.message);
                fetchDonations(); // Refresh the table
            } else {
                console.error(data.error || 'Unknown error occurred.');
            }
        })
        .catch(error => console.error('Error deleting donation:', error));
}
