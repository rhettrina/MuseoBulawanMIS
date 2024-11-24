function init() {
    const defaultButton = document.querySelector('#tabs button:first-child');
    selectTab(defaultButton);
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
