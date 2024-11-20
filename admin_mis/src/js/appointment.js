function init() {
    fetchTotalArticles();
    fetchArticles();  // Default to 'newest' sort
}

function fetchTotalArticles() {
    fetch('https://lightpink-dogfish-795437.hostingersite.com/admin_mis/src/php/fetchTotalArticles.php')
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                handleError('Error fetching total articles:', data.error);
                document.getElementById('total-articles').innerText = "Error fetching data";
            } else {
                document.getElementById('total-articles').innerText = data.total_articles;
            }
        })
        .catch(error => handleError('Error fetching total articles:', error));
}

// Fetch and populate the donations table
function fetchArticles(sort = 'newest') {
    fetch(`https://lightpink-dogfish-795437.hostingersite.com/admin_mis/src/php/fetchArticles.php?sort=${sort}`)
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                handleError('Error fetching articles:', data.error);
                displayNoDataMessage();
            } else {
                populateTable(data);
            }
        })
        .catch(error => {
            handleError('Error fetching articles:', error);
            displayNoDataMessage();
        });
}

function populateTable(articles) {
    const tableBody = document.getElementById('donation-table-body');
    tableBody.innerHTML = ''; // Clear existing rows

    if (articles.length === 0) {
        displayNoDataMessage();
        return;
    }

    articles.forEach(article => {
        const row = document.createElement('tr');
        row.classList.add('border-t', 'border-gray-300', 'text-center');

        // Create cells for each column
        row.innerHTML = `
            <td class="px-4 py-2">${article.date}</td>
            <td class="px-4 py-2">${article.donor_name}</td>
            <td class="px-4 py-2">${article.title}</td>
            <td class="px-4 py-2">${article.status}</td>
            <td class="px-4 py-2">${article.transfer_status}</td>
            <td class="px-4 py-2">${article.updated_date}</td>
            <td class="px-4 py-2 flex justify-center space-x-2">
                <button class="bg-green-500 text-white p-2 rounded hover:bg-green-600" onclick="handleAction('preview', ${article.id})">
                    <i class="fas fa-eye"></i>
                </button>
                <button class="bg-blue-500 text-white p-2 rounded hover:bg-blue-600" onclick="handleAction('edit', ${article.id})">
                    <i class="fas fa-edit"></i>
                </button>
                <button class="bg-red-500 text-white p-2 rounded hover:bg-red-600" onclick="handleAction('delete', ${article.id})">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        `;
        tableBody.appendChild(row);
    });
}

function handleAction(action, articleId) {
    switch (action) {
        case 'preview':
            console.log(`Preview article with ID: ${articleId}`);
            break;
        case 'edit':
            console.log(`Edit article with ID: ${articleId}`);
            break;
        case 'delete':
            console.log(`Delete article with ID: ${articleId}`);
            break;
        default:
            console.error('Unknown action:', action);
    }
}

function displayNoDataMessage() {
    const tableBody = document.getElementById('donation-table-body');
    tableBody.innerHTML = `
        <tr>
            <td colspan="7" class="text-center py-4">No donations found or an error occurred.</td>
        </tr>
    `;
}

function handleError(message, error) {
    console.error(message, error);
    alert(message); // Optional: Display a message to the user
}

document.getElementById("sort-donation").addEventListener("change", function () {
    const sortOption = this.value;
    fetchArticles(sortOption);
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
