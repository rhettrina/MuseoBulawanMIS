function init() {
    // Call the display functions here
    fetchTotalDonations();
    fetchDonations();
    // No need to call deleteDonation here; it's triggered by specific actions
}

function fetchTotalDonations() {
    fetch('https://lightpink-dogfish-795437.hostingersite.com/admin_mis/src/php/fetchTotalDonations.php')
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

function fetchDonations(sort = 'newest') {
    fetch(`https://lightpink-dogfish-795437.hostingersite.com/admin_mis/src/php/fetchDonations.php?sort=${sort}`)
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

function populateTable(Donation) {
    const tableBody = document.getElementById('donations-table').querySelector('tbody');
    tableBody.innerHTML = ''; // Clear existing rows

    if (Donation.length === 0) {
        displayNoDataMessage();
        return;
    }

    Donation.forEach(donation => {
        const row = document.createElement('tr');
        row.classList.add('border-t', 'border-gray-300', 'text-center');

        // Create and populate cells
        const dateCell = document.createElement('td');
        dateCell.classList.add('px-4', 'py-0','bg-white', 'border-black' , 'rounded-l-[15px]', 'border-t-2', 'border-b-2', 'border-l-2');
        dateCell.textContent = donation.submission_date;


const donorCell = document.createElement('td');
donorCell.classList.add('px-4', 'py-0', 'bg-white', 'border-black', 'border-t-2', 'border-b-2');
donorCell.textContent = donation.donor_name;

// Title Cell
const titleCell = document.createElement('td');
titleCell.classList.add('px-4', 'py-2', 'bg-white', 'border-black', 'border-t-2', 'border-b-2');
titleCell.textContent = donation.artifact_title;

// Type Cell
const typeCell = document.createElement('td');
typeCell.classList.add('px-4', 'py-0', 'bg-white', 'border-black', 'border-t-2', 'border-b-2');
typeCell.textContent = donation.artifact_type;

// Status Cell
const statusCell = document.createElement('td');
statusCell.classList.add('px-4', 'py-0', 'bg-white', 'border-black', 'border-t-2', 'border-b-2');
statusCell.textContent = donation.status;

// Updated Date Cell
const updatedDateCell = document.createElement('td');
updatedDateCell.classList.add('px-4', 'py-0', 'bg-white', 'border-black', 'border-t-2', 'border-b-2');
updatedDateCell.textContent = donation.updated_date === "Not Edited" || !donation.updated_date ? "Not Edited" : donation.updated_date;

// Transfer Status Cell
const transferStatusCell = document.createElement('td');
transferStatusCell.classList.add('px-4', 'py-0', 'bg-white', 'border-black', 'border-t-2', 'border-b-2');
// Assuming createTransferStatusCell returns an element, append it
transferStatusCell.appendChild(createTransferStatusCell(donation));

// Action Buttons Cell
const actionCell = document.createElement('td');
actionCell.classList.add('px-4', 'py-0', 'bg-white', 'border-black', 'rounded-r-[15px]', 'border-t-2', 'border-b-2', 'border-r-2');
// Assuming createActionButtons returns an element, append it
actionCell.appendChild(createActionButtons(donation));

        // Append cells to row
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

function displayErrorMessages() {
    const errorMessage = "Error fetching data";
    document.getElementById('total-donations').innerText = errorMessage;
    document.getElementById('total-accepted').innerText = errorMessage;
    document.getElementById('total-rejected').innerText = errorMessage;
    document.getElementById('total-donform').innerText = errorMessage;
    document.getElementById('total-lendform').innerText = errorMessage;
}

function populateTotalDonationData(data) {
    document.getElementById('total-donations').innerText = data.total_donations || 0;
    document.getElementById('total-accepted').innerText = data.accepted_donations || 0;
    document.getElementById('total-rejected').innerText = data.rejected_donations || 0;
    document.getElementById('total-donform').innerText = data.total_donation_forms || 0;
    document.getElementById('total-lendform').innerText = data.total_lending_forms || 0;
}

function displayNoDataMessage() {
    const tableBody = document.querySelector('tbody');
    tableBody.innerHTML = `
        <tr>
            <td colspan="8" class="text-center py-4">No donations found or an error occurred.</td>
        </tr>
    `;
}

function createTableCell(content) {
    const cell = document.createElement('td');
    cell.classList.add('px-4', 'py-2');
    cell.textContent = content;
    return cell;
}

function handleAction(action, donatorID) {
    switch (action) {
        case 'preview':
            console.log(`Preview donation with ID: ${donatorID}`);
            break;
        case 'edit':
            console.log(`Edit donation with ID: ${donatorID}`);
            break;
            case 'delete':
                console.log(`Delete donation with ID: ${donatorID}`);
                break;
            
        default:
            console.error('Unknown action:', action);
    }
}

function createTransferStatusCell(donation) {
    const cell = document.createElement('td');
    cell.classList.add('px-4', 'py-2');
    const dropdown = document.createElement('select');
    dropdown.classList.add('border','rounded');

    const statuses = ['ACQUIRED', 'FAILED', 'PENDING'];
    statuses.forEach(status => {
        const option = document.createElement('option');
        option.value = status.toUpperCase();
        option.textContent = status;
        option.selected = donation.transfer_status.toUpperCase() === status.toUpperCase();
        dropdown.appendChild(option);
    });

    dropdown.addEventListener('change', () => {
        const newStatus = dropdown.value;
        openStatusModal(donation.artifactID, donation.transfer_status, newStatus, dropdown);
    });

    cell.appendChild(dropdown);
    return cell;
}

function createActionButtons(donation) {
    const cell = document.createElement('td');
    cell.classList.add('px-4', 'py-2', 'flex', 'justify-center', 'space-x-2');

    const previewButton = createActionButton('eye', 'preview', donation);
    const editButton = createActionButton('edit', 'edit', donation);
    const deleteButton = createActionButton('trash', 'delete', donation);

    cell.appendChild(previewButton);
    cell.appendChild(editButton);
    cell.appendChild(deleteButton);

    return cell;
}

function createActionButton(icon, action, donation) {
    const button = document.createElement('button');
    button.classList.add('bg-orange-400', 'text-white', 'p-2', 'rounded', 'hover:bg-orange-300');
    button.innerHTML = `<i class="fas fa-${icon}"></i>`;
    button.addEventListener('click', () => handleAction(action, donation.artifactID));
    return button;
}
 
document.getElementById("sorts").addEventListener("change", function () {
    fetchDonations(this.value);
});
  

  
init();  // Initialize everything when the script runs
