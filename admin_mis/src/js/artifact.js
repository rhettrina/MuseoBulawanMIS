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
        'donators'
    ];
    const sortContainerIdList = [
        'artifact-sort',
        'acquired-sort',
        'borrowing-sort',
        'donators-sort'
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
            donatorID: item.donatorID,
            firstName: item.first_name,
            lastName: item.last_name,
            email: item.email,
            age: item.age,
            city: item.city,
        }));

        // Render the data into the tables
        renderTable(artifacts, "artifacts-table", ["date", "title", "type", "lastUpdated"]);
        renderTable(acquired, "acquired-table", ["date", "artifactName", "donorName", "lastUpdated"]);
        renderTable(borrowing, "borrowing-table", ["date", "artifactName", "duration", "update"]);
        renderDonatorsTable(donators);

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

function renderDonatorsTable(data) {
    const table = document.getElementById('donators');
    const tbody = table.querySelector('tbody');
    tbody.innerHTML = "";

    if (data.length === 0) {
        const emptyRow = document.createElement('tr');
        const emptyCell = document.createElement('td');
        emptyCell.colSpan = 4; // Adjust for the correct number of columns
        emptyCell.className = "text-center py-4";
        emptyCell.textContent = "No donors found.";
        emptyRow.appendChild(emptyCell);
        tbody.appendChild(emptyRow);
        return;
    }

    data.forEach(donator => {
        const row = document.createElement('tr');
        row.classList.add('border-t', 'border-gray-300', 'text-center');

        // Populate row with data
        row.innerHTML = `
            <td class="px-4 py-2">${donator.donatorID}</td>
            <td class="px-4 py-2">${donator.firstName} ${donator.lastName}</td>
            <td class="px-4 py-2">${donator.city}</td>
            <td class="px-4 py-2">
                <button 
                    class="bg-orange-500 text-white px-2 py-1 rounded-md hover:bg-orange-600" 
                    onclick="toggleDropdown(this, ${donator.donatorID})">
                    Show Donations
                </button>
                <div 
                    class="hidden mt-2 p-2 bg-white border border-gray-300 shadow-lg rounded-md max-w-xs max-h-60 overflow-y-auto">
                </div>
            </td>
        `;
        tbody.appendChild(row);
    });
}

function toggleDropdown(button, donatorID) {
    const dropdown = button.nextElementSibling;
    dropdown.classList.toggle('hidden');

    if (!dropdown.classList.contains('hidden')) {
        fetchDonatorArtifacts(donatorID).then(artifacts => {
            dropdown.innerHTML = ''; // Clear previous data
            if (artifacts.length === 0) {
                dropdown.innerHTML = '<p class="text-sm text-gray-500">No artifacts found.</p>';
                return;
            }

            artifacts.forEach(artifact => {
                const item = document.createElement('div');
                item.className = "py-1 px-2 border-b last:border-none text-sm text-gray-700";
                item.textContent = `${artifact.artifactTitle} - ${artifact.artifactStatus} - ${artifact.transferStatus}`;
                dropdown.appendChild(item);
            });
        });
    }
}

async function fetchDonatorArtifacts(donatorID) {
    try {
        const response = await fetch(`fetchDonatorArtifacts.php?donatorID=${donatorID}`);
        const data = await response.json();
        if (data.error) {
            console.error("Error fetching artifacts:", data.details);
            return [];
        }
        return data;
    } catch (error) {
        console.error("Error fetching artifacts:", error);
        return [];
    }
}

// Call the function to fetch and render data when the page loads
fetchAndRenderTables();
