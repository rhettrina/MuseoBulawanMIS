
(() => {
    function init() {
        console.log("Initializing artifact page...");
        // Page-specific initialization
        fetchTotalDonations();
        fetchDonations();
    }

    function cleanup() {
        console.log("Cleaning up artifact page...");
        // Cleanup logic
    }

    window.artifactPage = { init, cleanup };
})();

// Fetch donations table data
function fetchDonations(sort = 'newest') {
    console.log(`Fetching donations with sort order: ${sort}`);
    fetch(`https://museobulawan.online/development/admin_mis/src/php/fetchDonations.php?sort=${sort}`)
        .then(response => {
            if (!response.ok) {
                throw new Error(`Failed to fetch donations: ${response.statusText}`);
            }
            return response.json();
        })
        .then(data => {
            populateTable(data);
        })
        .catch(error => {
            console.error('Error fetching donations:', error);
            displayNoDataMessage();
        });
}

// Fetch total donation statistics
function fetchTotalDonations() {
    console.log("Fetching total donation statistics...");
    fetch('https://museobulawan.online/development/admin_mis/src/php/fetchTotalDonations.php')
        .then(response => {
            if (!response.ok) {
                throw new Error('Failed to fetch total donations: ' + response.statusText);
            }
            return response.json();
        })
        .then(data => {
            populateTotalDonationData(data);
        })
        .catch(error => {
            console.error('Error fetching total donation statistics:', error);
            displayErrorMessages();
        });
}

// Handle sorting change
function handleSortChange(event) {
    const sortOption = event.target.value;
    console.log(`Sorting donations by: ${sortOption}`);
    fetchDonations(sortOption);
}

function populateTable(donations) {
    const tableBody = document.getElementById('donations-table').querySelector('tbody');
    tableBody.innerHTML = ''; // Clear existing rows

    if (!donations.length) {
        displayNoDataMessage();
        return;
    }

    donations.forEach(donation => {
        const row = document.createElement('tr');
        row.classList.add('border-t', 'border-gray-300', 'text-center');

        row.appendChild(createTableCell(donation.submission_date, ['px-4', 'py-0', 'bg-white', 'border-black', 'rounded-l-[15px]', 'border-t-2', 'border-b-2', 'border-l-2']));
        row.appendChild(createTableCell(donation.donor_name, ['px-4', 'py-0', 'bg-white', 'border-black', 'border-t-2', 'border-b-2']));
        row.appendChild(createTableCell(donation.artifact_title, ['px-4', 'py-2', 'bg-white', 'border-black', 'border-t-2', 'border-b-2']));
        row.appendChild(createTableCell(donation.form_type, ['px-4', 'py-0', 'bg-white', 'border-black', 'border-t-2', 'border-b-2']));
        row.appendChild(createTableCell(donation.status, ['px-4', 'py-0', 'bg-white', 'border-black', 'border-t-2', 'border-b-2']));
        
        const transferStatusCell = document.createElement('td');
        transferStatusCell.classList.add('px-4', 'py-0', 'bg-white', 'border-black', 'border-t-2', 'border-b-2');
        transferStatusCell.appendChild(createTransferStatusCell(donation));
        row.appendChild(transferStatusCell);

        row.appendChild(createTableCell(donation.updated_date || "Not Edited", ['px-4', 'py-0', 'bg-white', 'border-black', 'border-t-2', 'border-b-2']));
        const actionCell = document.createElement('td');
        actionCell.classList.add('px-4', 'py-0', 'bg-white', 'border-black', 'rounded-r-[15px]', 'border-t-2', 'border-b-2', 'border-r-2');
        actionCell.appendChild(createActionButtons(donation));
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

// Display a "No Data" message
function displayNoDataMessage() {
    const tableBody = document.querySelector('tbody');
    tableBody.innerHTML = `
        <tr>
            <td colspan="8" class="text-center py-4">No donations found or an error occurred.</td>
        </tr>
    `;
}

// Populate total donation statistics
function populateTotalDonationData(data) {
    document.getElementById('total-donations').innerText = data.total_donations || 0;
    document.getElementById('total-accepted').innerText = data.accepted_donations || 0;
    document.getElementById('total-rejected').innerText = data.rejected_donations || 0;
    document.getElementById('total-donform').innerText = data.total_donation_forms || 0;
    document.getElementById('total-lendform').innerText = data.total_lending_forms || 0;
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

// Create dropdown for transfer status
function createTransferStatusCell(donation) {
    const dropdown = document.createElement('select');
    dropdown.classList.add('border', 'rounded');

    const statuses = ['Acquired', 'Pending', 'Failed' ];
    statuses.forEach(status => {
        const option = document.createElement('option');
        option.value = status.toUpperCase();
        option.textContent = status;
        option.selected = donation.transfer_status.toUpperCase() === status.toUpperCase();
        dropdown.appendChild(option);
    });

    dropdown.addEventListener('change', () => {
        const newStatus = dropdown.value;
        console.log(`Updating transfer status for donation ID ${donation.formID} to ${newStatus}`);
        updateTransferStatus(donation.formID, newStatus);
    });

    return dropdown;
}

function updateTransferStatus(donID, newStatus) {
    fetch('https://museobulawan.online/development/admin_mis/src/php/updateTransferStatus.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            donation: donID,
            transfer_status: newStatus
        }),
    })
        .then(response => {
            if (!response.ok) {
                throw new Error('Failed to update transfer status');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                console.log(`Transfer status for donation ID ${donID} successfully updated to ${newStatus}`);
                fetchDonations(); // Refresh the table to reflect updates
            } else {
                console.error(`Failed to update transfer status: ${data.error}`);
            }
        })
        .catch(error => {
            console.error('Error updating transfer status:', error);
        });
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

// Handle user actions
function handleAction(action, donation) {
    console.log(`Handling action: ${action} for donation ID: ${donation.donID}`);
    switch (action) {
        case 'preview':
            console.log(`Preview donation ID: ${donation.donID}`);
            break;
        case 'edit':
            console.log(`Edit donation ID: ${donation.donID}`);
            break;
        case 'delete':
            console.log(`Delete donation ID: ${donation.donID}`);
            confirmDeleteDonation(donation.donID);
            break;
        default:
            console.error(`Unknown action: ${action}`);
    }
}

// Confirm deletion of a donation
function confirmDeleteDonation(donID) {
    if (confirm("Are you sure you want to delete this donation?")) {
        deleteDonation(donID);
    }
}

// Perform the actual deletion
function deleteDonation(donID) {
    fetch(`https://museobulawan.online/development/admin_mis/src/php/deleteDonations.php?id=${donID}`, { method: 'DELETE' })
        .then(response => {
            if (!response.ok) throw new Error('Failed to delete the donation.');
            return response.json();
        })
        .then(() => fetchDonations()) // Refresh the table
        .catch(error => console.error('Error deleting donation:', error));
}

window.onload = init;
