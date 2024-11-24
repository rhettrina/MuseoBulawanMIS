function init() {
    const defaultButton = document.querySelector('#tabs button:first-child');
    selectTab(defaultButton);
    fetchAndRenderTables();
}

function selectTab(selectedButton) {
    const buttons = document.querySelectorAll('#tabs button');
    const tables = document.querySelectorAll('table');
    const sortContainers = document.querySelectorAll('.sort-container'); // Select all sort containers

    // Remove active styles from all buttons and hide all tables and sort containers
    buttons.forEach((button) => {
        button.classList.remove('text-black', 'font-bold', 'active-tab');
        button.classList.add('text-gray-500', 'font-medium');
    });
    tables.forEach((table) => {
        table.classList.add('hidden');
    });
    sortContainers.forEach((container) => {
        container.classList.add('hidden');
    });

    // Apply active styles to the selected button
    selectedButton.classList.remove('text-gray-500', 'font-medium');
    selectedButton.classList.add('text-black', 'font-bold', 'active-tab');

    // Display the corresponding table
    const buttonIndex = Array.from(buttons).indexOf(selectedButton);
    const tableIdList = [
        'artifacts-table',
        'acquired-table',
        'borrowing-table',
        'display-table',
        'donors-table'
    ];
    const sortContainerIdList = [
        'artifact-sort',
        'acquired-sort',
        'borrowing-sort',
        'display-sort',
        'donors-sort'
    ];

    // Show the corresponding table
    const tableToShow = document.getElementById(tableIdList[buttonIndex]);
    if (tableToShow) {
        tableToShow.classList.remove('hidden');
    } else {
        console.error(`No table found for button index ${buttonIndex}`);
    }

    // Show the corresponding sort container if applicable
    if (buttonIndex < sortContainerIdList.length) {
        const sortContainerToShow = document.getElementById(sortContainerIdList[buttonIndex]);
        if (sortContainerToShow) {
            sortContainerToShow.closest('.sort-container').classList.remove('hidden');
        }
    }
}


// Initialize the default state when the page loads
document.addEventListener('DOMContentLoaded', init);

async function fetchAndRenderTables() {
    try {
        // Fetch the JSON data from the PHP endpoint
        const response = await fetch('https://museobulawan.online/development/admin_mis/src/php/fetchArtifacts.php'); // Replace with your PHP file's actual path
        const data = await response.json();

        if (data.error) {
            console.error("Error fetching data:", data.details);
            return;
        }

        // Map data to individual tables
        const artifacts = data.map(item => ({
            date: item.ArtifactSubmissionDate,
            title: item.artifact_nameID || "N/A",
            type: item.type, // Adjust this logic based on your needs
            display_status: true,
            lastUpdated: item.updated_date || "Not Edited",
        }));

        const acquired = data.map(item => ({
            donationID: item.DonatorSubmissionDate,
            donatorID: item.donatorID,
            artifactName: item.artifact_nameID,
            lastUpdated: item.updated_date,
            donorName: `${item.first_name} ${item.last_name}` // Concatenate first and last names
        }));

        const borrowing = data.map(item => ({
            lendingID: item.lendingID,
            donatorID: item.donatorID,
            artifactName: item.artifact_nameID,
            duration: item.lending_durationID,
            reason: item.lending_reason,
        }));

        const display = data.map(item => ({
            artifactID: item.artifact_nameID, // Adjust if there's a separate artifactID in your schema
            type: "Artifact Type", // Replace with the actual type if available
            name: item.artifact_nameID,
            status: "Active", // Replace with the actual status if available
        }));

        const donators = data.map(item => ({
            donatorID: item.donatorID,
            firstName: item.first_name,
            lastName: item.last_name,
            email: item.email,
            age: item.age,
            city: item.city,
        }));

        // Render the data into the tables
        renderTable(artifacts, "artifacts-table", ["date", "title", "type", "display_status","lastUpdated"]);
        renderTable(acquired, "acquired-table", ["donationID",  "artifactName","donorName","lastUpdated"]);
        renderTable(borrowing, "borrowing-table", ["lendingID", "donatorID", "artifactName", "duration", "reason"]);
        renderTable(display, "display-table", ["artifactID", "type", "name", "status"]);
        renderTable(donators, "donors-table", ["donatorID", "firstName", "lastName", "email", "age", "city"]);
        
    } catch (error) {
        console.error("Error fetching or processing data:", error);
    }
}

function renderTable(data, tableId, columns) {
    const table = document.getElementById(tableId);

    if (!table) {
        console.error(`Table with ID ${tableId} not found`);
        return;
    }

    const tbody = table.querySelector("tbody");
    if (!tbody) {
        console.error(`Table body not found in table ${tableId}`);
        return;
    }

    // Clear existing rows
    tbody.innerHTML = "";

    if (data.length === 0) {
        const emptyRow = document.createElement("tr");
        const emptyCell = document.createElement("td");
        emptyCell.colSpan = columns.length + 1; // Include action column
        emptyCell.className = "text-center py-4";
        emptyCell.textContent = `No data found.`;
        emptyRow.appendChild(emptyCell);
        tbody.appendChild(emptyRow);
        return;
    }

    // Populate table rows
    data.forEach(item => {
        const row = document.createElement('tr');
        row.classList.add('border-t', 'border-gray-300', 'text-center');

        // Create cells based on columns
        columns.forEach(column => {
            const cell = document.createElement('td');
            cell.classList.add('px-4', 'py-2', 'bg-white', 'border-black', 'border-t-2', 'border-b-2');

            // Add specific styles for first and last columns
            if (column === columns[0]) cell.classList.add('rounded-l-[15px]', 'border-l-2');
            if (column === columns[columns.length - 1]) cell.classList.add('rounded-r-[15px]', 'border-r-2');

            // Handle display_status column to show "on display" or "not displayed"
            if (column === 'display_status') {
                cell.textContent = item[column] ? "on display" : "not displayed";
            } else {
                cell.textContent = item[column] || "N/A";
            }

            row.appendChild(cell);
        });

        // Action cell
        const actionCell = document.createElement('td');
        actionCell.classList.add('px-4', 'py-2', 'flex', 'justify-center', 'space-x-2', 'bg-white', 'border-black', 'rounded-r-[15px]', 'border-t-2', 'border-b-2', 'border-r-2');

        // Add buttons with event listeners
        const previewButton = createActionButton('fas fa-eye', 'preview', item.id);
        const editButton = createActionButton('fas fa-edit', 'edit', item.id);
        const deleteButton = createActionButton('fas fa-trash', 'delete', item.id);

        actionCell.append(previewButton, editButton, deleteButton);
        row.appendChild(actionCell);
        tbody.appendChild(row);
    });
}

function createActionButton(iconClass, action, id) {
    const button = document.createElement('button');
    button.classList.add('bg-transparent', 'text-black', 'p-2', 'rounded', 'hover:bg-orange-300');
    button.innerHTML = `<i class="${iconClass}"></i>`;
    button.addEventListener('click', () => handleAction(action, id));
    return button;
}

function handleAction(action, id) {
    console.log(`Action: ${action}, ID: ${id}`);
    // Add your specific logic for each action here
}



  
// Call the function to fetch and render data when the page loads
fetchAndRenderTables();
