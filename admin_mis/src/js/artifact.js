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
        'donors-table'
    ];
    const sortContainerIdList = [
        'artifact-sort',
        'acquired-sort',
        'borrowing-sort',
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

        // Filter and map data to individual tables
        const artifacts = data.map(item => ({
            date: item.ArtifactSubmissionDate,
            title: item.artname,
            type: item.artifact_typeID, // Adjust this logic based on your needs
            lastUpdated: item.updated_date || "Not Edited",
        }));

        // Filter for type "donation" and map for acquired table
        const acquired = data
            .filter(item => item.artifact_typeID === "Donation") // Ensure only donation type is included
            .map(item => ({
                date: item.DonationSubmissionDate,
                donatorID: item.donatorID,
                artifactName: item.artifact_nameID,
                lastUpdated: item.updated_date,
                donorName: `${item.first_name} ${item.last_name}` // Concatenate first and last names
            }));

        // Filter for type "lending" and map for borrowing table
        const borrowing = data
        .filter(item => item.artifact_typeID === "Lending") // Ensure only lending type is included
        .map(item => ({
            date: item.LendingSubmissionDate,
            lendingID: item.lendingID,
            artifactName: item.artifact_nameID,
            duration: calculateDuration(item.starting_date, item.ending_date), // Use the calculateDuration function for duration
            update: item.updated_date
        }));
    

        const donators = data.map(item => ({
            date: item.ArtifactSubmissionDate,
            donor: `${item.first_name} ${item.last_name}`,
            donation: item.last_name,

        }));

        // Render the data into the tables
        renderTable(artifacts, "artifacts-table", ["date", "title", "type", "lastUpdated"]);
        renderTable(acquired, "acquired-table", ["date", "artifactName", "donorName", "lastUpdated"]);
        renderTable(borrowing, "borrowing-table", ["date", "artifactName","duration","update" ]);
        renderTable(donators, "donors-table", ["date", "donor", "donation"]);

    } catch (error) {
        console.error("Error fetching or processing data:", error);
    }
}

const calculateDuration = (startDate, endDate) => {
    const start = new Date(startDate);
    const end = new Date(endDate);

    if (isNaN(start.getTime()) || isNaN(end.getTime())) {
        return "Invalid date";
    }

    let years = end.getFullYear() - start.getFullYear();
    let months = end.getMonth() - start.getMonth();
    let days = end.getDate() - start.getDate();

    // Adjust for negative days
    if (days < 0) {
        months -= 1;
        const previousMonth = new Date(end.getFullYear(), end.getMonth(), 0); // Last day of the previous month
        days += previousMonth.getDate();
    }

    // Adjust for negative months
    if (months < 0) {
        years -= 1;
        months += 12;
    }

    // Build a readable duration string
    const yearString = years > 0 ? `${years} year${years > 1 ? 's' : ''}` : '';
    const monthString = months > 0 ? `${months} month${months > 1 ? 's' : ''}` : '';
    const dayString = days > 0 ? `${days} day${days > 1 ? 's' : ''}` : '';

    // Combine non-empty parts
    return [yearString, monthString, dayString].filter(Boolean).join(', ');
};

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

            cell.textContent = item[column] || "N/A";

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
