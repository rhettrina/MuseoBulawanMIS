

(() => {
    function init() {
        const defaultButton = document.querySelector('#tabs button:first-child');
        
        window.artifactPage = { init, cleanup };
        
        if (!defaultButton) {
            console.error("Default button not found! Ensure the DOM structure is correct.");
            return;
        }

            selectTab(defaultButton);
    
        // Set "Artifacts" as the default tab
        console.log("Default button selected:", defaultButton);
       
    
        // Fetch and render tables
        fetchAndRenderTables();
    }
    function cleanup() {
        console.log("Cleaning up artifact page...");
        // Cleanup logic
    }

    
})();



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

    // Save the active tab index to local storage
    localStorage.setItem('activeTabIndex', buttonIndex);

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

function setDefaultActiveTab() {
    const buttons = document.querySelectorAll('#tabs button');

    // Load the active tab index from local storage
    const activeTabIndex = localStorage.getItem('activeTabIndex');
    const defaultButton = buttons[activeTabIndex] || buttons[0]; // Use saved index or default to the first button

    if (defaultButton) {
        selectTab(defaultButton); // Call selectTab with the default button
    } else {
        console.error("No default button found!");
    }
}

// Call the default tab selection when the page loads
document.addEventListener("DOMContentLoaded", setDefaultActiveTab);





// Initialize the default state when the page loads

async function fetchAndRenderTables() {
    try {
        const response = await fetch('https://museobulawan.online/development/admin_mis/src/php/fetchArtifacts.php'); // Replace with your PHP file's actual path
        const data = await response.json();

        if (data.error) {
            console.error("Error fetching data:", data.details);
            return;
        }

        // Transform data for each table
        const tableData = {
            "artifacts-table": data.map(item => ({
                date: item.ArtifactSubmissionDate,
                title: item.artname,
                type: item.artifact_typeID,
                lastUpdated: item.updated_date || "Not Edited",
            })),
            "acquired-table": data
                .filter(item => item.artifact_typeID === "Donation")
                .map(item => ({
                    date: item.DonationSubmissionDate,
                    artifactName: item.artifact_nameID,
                    donorName: `${item.first_name} ${item.last_name}`,
                    lastUpdated: item.updated_date || "Not Edited",
                })),
            "borrowing-table": data
                .filter(item => item.artifact_typeID === "Lending")
                .map(item => ({
                    date: item.LendingSubmissionDate,
                    artifactName: item.artifact_nameID,
                    duration: calculateDuration(item.starting_date, item.ending_date),
                    update: item.updated_date || "Not Edited",
                })),
            "donors-table": data.map(item => ({
                date: item.ArtifactSubmissionDate,
                donor: `${item.first_name} ${item.last_name}`,
                donation: item.artifact_nameID,
            })),
        };

        // Render each table
        for (const [tableId, tableContent] of Object.entries(tableData)) {
            const columns = getTableColumns(tableId);
            renderTable(tableContent, tableId, columns);
        }

    } catch (error) {
        console.error("Error fetching or processing data:", error);
    }
}

function getTableColumns(tableId) {
    const columnMap = {
        "artifacts-table": ["date", "title", "type", "lastUpdated"],
        "acquired-table": ["date", "artifactName", "donorName", "lastUpdated"],
        "borrowing-table": ["date", "artifactName", "duration", "update"],
        "donors-table": ["date", "donor", "donation"],
    };
    return columnMap[tableId] || [];
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

    if (days < 0) {
        months -= 1;
        const previousMonth = new Date(end.getFullYear(), end.getMonth(), 0);
        days += previousMonth.getDate();
    }

    if (months < 0) {
        years -= 1;
        months += 12;
    }

    const yearString = years > 0 ? `${years} year${years > 1 ? 's' : ''}` : '';
    const monthString = months > 0 ? `${months} month${months > 1 ? 's' : ''}` : '';
    const dayString = days > 0 ? `${days} day${days > 1 ? 's' : ''}` : '';

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

    tbody.innerHTML = "";

    if (data.length === 0) {
        const emptyRow = document.createElement("tr");
        const emptyCell = document.createElement("td");
        emptyCell.colSpan = columns.length + 1;
        emptyCell.className = "text-center py-4";
        emptyCell.textContent = `No data found.`;
        emptyRow.appendChild(emptyCell);
        tbody.appendChild(emptyRow);
        return;
    }

    data.forEach(item => {
        const row = document.createElement('tr');
        row.classList.add('border-t', 'border-gray-300', 'text-center');

        columns.forEach(column => {
            const cell = document.createElement('td');
            cell.classList.add('px-4', 'py-2', 'bg-white', 'border-black', 'border-t-2', 'border-b-2');

            if (column === columns[0]) cell.classList.add('rounded-l-[15px]', 'border-l-2');
            

            cell.textContent = item[column] || "N/A";

            row.appendChild(cell);
        });

        const actionCell = document.createElement('td');
        actionCell.classList.add('px-4', 'py-2', 'flex', 'justify-center', 'space-x-2', 'bg-white', 'border-black', 'rounded-r-[15px]', 'border-t-2', 'border-b-2', 'border-r-2');

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
}

fetchAndRenderTables();
