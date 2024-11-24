(() => {
    function init() {
        console.log("Initializing artifact page...");
        fetchDonations();
        fetchTotalDonations();
    }

    function cleanup() {
        console.log("Cleaning up artifact page...");
        // Cleanup logic
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

// Placeholder functions for display and table population
function displayErrorMessages() {
    console.error("Display error messages called");
}

function populateTotalDonationData(data) {
    console.log("Populate total donation data:", data);
}

function populateTable(data) {
    console.log("Populate table with data:", data);
}

function displayNoDataMessage() {
    console.log("No data to display");
}


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

        // Extract form_type to ensure correct handling
        const formType = donation.form_type;

        // Create and populate cells
        const dateCell = document.createElement('td');
        dateCell.classList.add('px-4', 'py-0','bg-white', 'border-black' , 'rounded-l-[15px]', 'border-t-2', 'border-b-2', 'border-l-2');
        dateCell.textContent = donation.submission_date;

        const donorCell = document.createElement('td');
        donorCell.classList.add('px-4', 'py-0', 'bg-white', 'border-black', 'border-t-2', 'border-b-2');
        donorCell.textContent = donation.donor_name;

        const titleCell = document.createElement('td');
        titleCell.classList.add('px-4', 'py-2', 'bg-white', 'border-black', 'border-t-2', 'border-b-2');
        titleCell.textContent = donation.artifact_title;;

        const typeCell = document.createElement('td');
        typeCell.classList.add('px-4', 'py-0','bg-white','border-black','border-t-2', 'border-b-2');
        typeCell.textContent = formType;
        
        const statusCell = document.createElement('td');
        statusCell.classList.add('px-4', 'py-0', 'bg-white', 'border-black', 'border-t-2', 'border-b-2');
        statusCell.textContent = donation.status;

        const transferStatusCell = document.createElement('td');
        transferStatusCell.classList.add('px-4', 'py-0', 'bg-white', 'border-black', 'border-t-2', 'border-b-2');
        transferStatusCell.appendChild(createTransferStatusCell(donation));

        const updatedDateCell = document.createElement('td');
        updatedDateCell.classList.add('px-4', 'py-0', 'bg-white', 'border-black', 'border-t-2', 'border-b-2');
        updatedDateCell.textContent = donation.updated_date === "Not Edited" || !donation.updated_date ? "Not Edited" : donation.updated_date;
        
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
function handleAction(action, donation) {
    console.log(`Action: ${action}`);
    console.log(`Donation Data:`, donation); // Debug: Log the donation object
    console.log(`Artifact ID: ${donation.donID}`); // Log the specific ID for debugging

    // Use the form_type directly from the donation object
    const formType = donation.form_type;
    console.log(`Form Type: ${formType}`); // Debug: Log the form type

    switch (action) {
        case 'preview':
            console.log(`Preview ${formType} with ID: ${donation.donID}`);
            break;
        case 'edit':
            console.log(`Edit ${formType} with ID: ${donation.donID}`);
            openFormModal(donation.donID, formType);
            break;
        case 'delete':
            console.log(`Delete ${formType} with ID: ${donation.donID}`);
            confirmDeleteDonation(donation.donID);
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

    const statuses = ['Acquired', 'Failed', 'Pending'];
    statuses.forEach(status => {
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

    cell.appendChild(dropdown);
    return cell;
}
function createActionButtons(donation) {
    const cell = document.createElement('td');
    cell.classList.add('px-4', 'py-2', 'flex', 'justify-center', 'space-x-2');

    // Define the actions and corresponding icons
    const actions = [
        { icon: 'eye', action: 'preview' },
        { icon: 'edit', action: 'edit' },
        { icon: 'trash', action: 'delete' },
    ];

    // Loop through the actions to create buttons
    actions.forEach(({ icon, action }) => {
        const button = document.createElement('button');
        button.classList.add(
            'bg-transparent',   // Transparent background
            'text-black',       // Black text
            'p-2',              // Padding
            'rounded',          // Rounded corners
            'hover:bg-orange-300' // Hover effect with orange background
        );
        button.innerHTML = `<i class="fas fa-${icon}"></i>`;
        
        // Pass the full donation object to handleAction
        button.addEventListener('click', () => handleAction(action, donation));
        cell.appendChild(button);
    });

    return cell;
}

document.getElementById("sorts").addEventListener("change", function () {
    fetchDonations(this.value);
});
  
function deleteDonation(donID) {
    fetch(`https://museobulawan.online/development/admin_mis/src/php/deleteDonations.php?id=${donID}`, {
        method: 'DELETE',
    })
        .then((response) => {
            if (!response.ok) {
                throw new Error('Failed to delete the donation.');
            }
            return response.json();
        })
        .then((data) => {
            if (data.message) {
                alert(data.message);

                // Dynamically remove the row from the table
                const row = document.querySelector(`[data-id="${donID}"]`);
                if (row) {
                    row.parentNode.removeChild(row);
                }
            } else {
                console.error(data.error || 'Unknown error occurred.');
            }
        })
        .catch((error) => {
            console.error('Error deleting donation:', error);
        });
}

function openDeleteModal(callback) {
    const modal = document.getElementById('delete-modal');
    modal.classList.remove('hidden');

    const confirmButton = document.getElementById('delete-confirm-button');
    const cancelButton = document.getElementById('delete-cancel-button');

    // Remove existing event listeners to avoid stacking
    confirmButton.replaceWith(confirmButton.cloneNode(true));
    cancelButton.replaceWith(cancelButton.cloneNode(true));

    document.getElementById('delete-confirm-button').addEventListener('click', function () {
        if (typeof callback === 'function') {
            callback(true);
        }
        closeDModal('delete-modal');
    });

    document.getElementById('delete-cancel-button').addEventListener('click', function () {
        if (typeof callback === 'function') {
            callback(false);
        }
        closeDModal('delete-modal');
    });
}

function confirmDeleteDonation(donID) {
    openDeleteModal((confirmed) => {
        if (confirmed) {
            deleteDonation(donID); // Call deleteDonation with the correct ID
        }
    });
}

function closeDModal(modalId) {
    const modal = document.getElementById(modalId);
    modal.classList.add('hidden');
}


function updateTransferStatus(donID, newStatus) {
    fetch('https://museobulawan.online/development/admin_mis/src/php/updateTransferStatus.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ donation: donID, transfer_status: newStatus })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Failed to update transfer status');
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            fetchDonations(); // Refresh the table to reflect updates
        } else {
            console.error('Failed to update transfer status:', data.error);
        }
    })
    .catch(error => {
        console.error('Error updating transfer status:', error);
    });
}

function openStatusModal(donID, currentStatus, newStatus, dropdown) {
    // Validate inputs
    if (!donID || !newStatus || !dropdown) {
        console.error('Invalid parameters passed to openStatusModal:', { donID, currentStatus, newStatus, dropdown });
        return;
    }

    // Dynamically retrieve the actual current status from the dropdown to ensure correctness
    currentStatus = dropdown.getAttribute("data-current-status") || currentStatus;

    const modal = document.getElementById("transfer-status-modal");
    const confirmButton = document.getElementById("status-confirm-button");
    const cancelButton = document.getElementById("status-cancel-button");
    const confirmationMessage = document.getElementById("status-confirmation-message");

    // Ensure modal and buttons exist
    if (!modal || !confirmButton || !cancelButton || !confirmationMessage) {
        console.error("Modal or required elements not found");
        return;
    }

    // Update the modal content and show it
    confirmationMessage.textContent = `Do you want to confirm the change of transfer status from "${currentStatus}" to "${newStatus}" for the donor with ID: ${donID}?`;
    modal.classList.remove("hidden");

    console.log(`Opening confirmation modal for donator ID: ${donID}, current status: "${currentStatus}", new status: "${newStatus}"`);

    // When the user confirms
    confirmButton.onclick = () => {
        console.log(`User confirmed the change for donator ID: ${donID}, changing status from "${currentStatus}" to "${newStatus}"`);

        // Make a fetch request to update the status
        fetch('https://museobulawan.online/development/admin_mis/src/php/updateTransferStatus.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                donID: donID,
                transfer_status: newStatus
            }),
        })
            .then((response) => {
                if (!response.ok) {
                    throw new Error('Failed to update transfer status');
                }
                return response.json();
            })
            .then((data) => {
                if (data.success) {
                    console.log(`Transfer status successfully changed from "${currentStatus}" to "${newStatus}"`);
                    dropdown.setAttribute("data-current-status", newStatus); // Update the current status reference
                    dropdown.value = newStatus; // Reflect the change in the dropdown
                } else {
                    console.error('Failed to update transfer status:', data.error);
                    dropdown.value = currentStatus; // Revert dropdown to its previous value
                }
            })
            .catch((error) => {
                console.error('Error updating transfer status:', error);
                dropdown.value = currentStatus; // Revert dropdown to its previous value
            })
            .finally(() => {
                closeTModal("transfer-status-modal");
            });
    };

    // When the user cancels
    cancelButton.onclick = () => {
        console.log(`User canceled the status change for donator ID: ${donID}. Status remains as "${currentStatus}"`);
        dropdown.value = currentStatus; // Revert the dropdown to original value
        closeTModal("transfer-status-modal");
    };
}

function closeTModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.add("hidden");
    }
} 

function openFormModal(donID, formType) {
    fetch(`https://museobulawan.online/development/admin_mis/src/php/getFormDetails.php?donID=${donID}&formType=${formType}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Failed to fetch form details');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                populateFormModal(data.formDetails, formType);
                document.getElementById('form-modal').classList.remove('hidden');
            } else {
                console.error('Error fetching form details:', data.error);
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
}

  function populateFormModal(details, formType) {
    // Populate personal details
    document.getElementById('modal-first-name').textContent = details.first_name;
    document.getElementById('modal-last-name').textContent = details.last_name;
    document.getElementById('modal-age').textContent = details.age;
    document.getElementById('modal-sex').textContent = details.sex;
    document.getElementById('modal-email').textContent = details.email;
    document.getElementById('modal-phone').textContent = details.phone;
    document.getElementById('modal-organization').textContent = details.organization;
    // Populate each part of the address separately
    document.getElementById('modal-street').textContent = details.street;
    document.getElementById('modal-barangay').textContent = details.barangay;
    document.getElementById('modal-city').textContent = details.city;
    document.getElementById('modal-province').textContent = details.province;
    
    // Populate artifact details
    document.getElementById('modal-artifact-title').textContent = details.artifact_nameID;
    document.getElementById('modal-artifact-description').textContent = details.artifact_description;
    document.getElementById('modal-acquisition').textContent = details.acquisition;
    document.getElementById('modal-additional-info').textContent = details.additional_info;
    document.getElementById('modal-narrative').textContent = details.narrative;
    document.getElementById('modal-images').textContent = details.artifact_img;
    document.getElementById('modal-documentation').textContent = details.documentation;
    document.getElementById('modal-related').textContent = details.related_img;
  
    // Handle Lending-Specific Fields
    const lendingFields = document.getElementById('lending-fields');
    if (formType === 'Lending') {
      lendingFields.classList.remove('hidden');

      // Example usage with details object
      document.getElementById('modal-loan-duration').textContent = 
          `${calculateDuration(details.starting_date, details.ending_date)}`;

      document.getElementById('modal-display-condition').textContent = details.display_conditions;
      document.getElementById('modal-liability-concern').textContent = details.liability_concerns;
      document.getElementById('modal-reason').textContent = details.lending_reason;
    } else {
      lendingFields.classList.add('hidden');
    }
  
    // Set modal title
    document.getElementById('modal-title').textContent = `${formType} Form Details`;
  }
  document.querySelectorAll('[data-modal-close]').forEach(button => {
    button.addEventListener('click', closeformModal);
});
const calculateDuration = (startDate, endDate) => {
    const start = new Date(startDate);
    const end = new Date(endDate);

    // Calculate the difference in milliseconds
    const diffInMilliseconds = end - start;

    // Convert the difference to days, months, and years
    const diffInDays = Math.floor(diffInMilliseconds / (1000 * 60 * 60 * 24));
    const diffInYears = Math.floor(diffInDays / 365);
    const diffInMonths = Math.floor((diffInDays % 365) / 30);

    // Build a readable duration string
    const years = diffInYears > 0 ? `${diffInYears} year${diffInYears > 1 ? 's' : ''}` : '';
    const months = diffInMonths > 0 ? `${diffInMonths} month${diffInMonths > 1 ? 's' : ''}` : '';
    const days = diffInDays % 30 > 0 ? `${diffInDays % 30} day${diffInDays % 30 > 1 ? 's' : ''}` : '';

    // Combine non-empty parts
    return [years, months, days].filter(Boolean).join(', ');
};

 function closeformModal() {
    const modal = document.getElementById('form-modal');
    if (modal) {
        modal.classList.add('hidden');
    } else {
        console.error('Modal with id "form-modal" not found');
    }
}



  
init();  // Initialize everything when the script runs
