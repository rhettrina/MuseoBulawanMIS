
function init(){
    fetchTotalArticles();
    fetchArticles();
}

function fetchTotalArticles() {
    fetch('https://lightpink-dogfish-795437.hostingersite.com/admin_mis/src/php/fetchTotalArticles.php')
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok: ' + response.statusText);
            }
            return response.json();
        })
        .then(data => {
            if (data.error) {
                console.error('Error from PHP:', data.error);
                document.getElementById('total-articles').innerText = "Error fetching data";
            } else {
                document.getElementById('total-articles').innerText = data.total_articles;
            }
        })
        .catch(error => {
            console.error('Error fetching total articles:', error);
            document.getElementById('total-articles').innerText = "Error fetching data";
        });
}

// Fetch and populate the articles table
function fetchArticles(sort = 'newest') {
    fetch(`https://lightpink-dogfish-795437.hostingersite.com/admin_mis/src/php/fetchArticles.php?sort=${sort}`)
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
            console.error('Error fetching articles:', error);
            displayNoDataMessage();
        });
}

function populateTable(articles) {
    const tableBody = document.querySelector('tbody');
    tableBody.innerHTML = ''; // Clear existing rows

    if (articles.length === 0) {
        displayNoDataMessage();
        return;
    }

    articles.forEach(article => {
        const row = document.createElement('tr');
        row.classList.add('border-t', 'border-gray-300', 'text-center');

        // Create cells
        const dateCell = document.createElement('td');
        dateCell.classList.add('px-4', 'py-2');
        dateCell.textContent = article.created_at;

        const titleCell = document.createElement('td');
        titleCell.classList.add('px-4', 'py-2');
        titleCell.textContent = article.article_title;

        const typeCell = document.createElement('td');
        typeCell.classList.add('px-4', 'py-2');
        typeCell.textContent = article.article_type;

        const updatedDateCell = document.createElement('td');
        updatedDateCell.classList.add('px-4', 'py-2');
        updatedDateCell.textContent = article.updated_date === "Not Edited" || !article.updated_date
            ? "Not Edited"
            : article.updated_date;

        const actionCell = document.createElement('td');
        actionCell.classList.add('px-4', 'py-2', 'flex', 'justify-center', 'space-x-2');

        // Add buttons with event listeners
        const previewButton = document.createElement('button');
        previewButton.classList.add('bg-green-500', 'text-white', 'p-2', 'rounded', 'hover:bg-green-600');
        previewButton.innerHTML = `<i class="fas fa-eye"></i>`;
        previewButton.addEventListener('click', () => handleAction('preview', article.id));

        const editButton = document.createElement('button');
        editButton.classList.add('bg-blue-500', 'text-white', 'p-2', 'rounded', 'hover:bg-blue-600');
        editButton.innerHTML = `<i class="fas fa-edit"></i>`;
        editButton.addEventListener('click', () => handleAction('edit', article.id));

        const deleteButton = document.createElement('button');
        deleteButton.classList.add('bg-red-500', 'text-white', 'p-2', 'rounded', 'hover:bg-red-600');
        deleteButton.innerHTML = `<i class="fas fa-trash"></i>`;
        deleteButton.addEventListener('click', () => handleAction('delete', article.id));

        // Append buttons to the action cell
        actionCell.appendChild(previewButton);
        actionCell.appendChild(editButton);
        actionCell.appendChild(deleteButton);

        // Append cells to the row
        row.appendChild(dateCell);
        row.appendChild(titleCell);
        row.appendChild(typeCell);
        row.appendChild(updatedDateCell);
        row.appendChild(actionCell);

        // Append row to the table body
        tableBody.appendChild(row);
    });
}


function handleAction(action, articleId) {
    switch (action) {
        case 'preview':
            console.log(`Preview article with ID: ${articleId}`);
            // Implement preview functionality here
            break;
        case 'edit':
            console.log(`Edit article with ID: ${articleId}`);
            // Implement edit functionality here
            break;
        case 'delete':
            console.log(`Delete article with ID: ${articleId}`);
            // Confirm before deleting

            break;
        default:
            console.error('Unknown action:', action);
    }
}


// Display a "No Data" message in the table
function displayNoDataMessage() {
    const tableBody = document.querySelector('tbody');
    tableBody.innerHTML = `
        <tr>
            <td colspan="5" class="text-center py-4">No articles found or an error occurred.</td>
        </tr>
    `;
}

// Handle sort selection
document.getElementById("sort").addEventListener("change", function () {
    const sortOption = this.value;
    fetchArticles(sortOption);
});



// Function to show the Create Article Modal
function showCreateArticleModal() {
    const modal = document.getElementById('create-article-modal');
    modal.classList.remove('hidden'); // Remove the 'hidden' class to show the modal
  }
  
  // Add event listener to the Create Article button
  document.getElementById('create-article-button').addEventListener('click', showCreateArticleModal);


// Function to hide the Create Article Modal
function hideCreateArticleModal() {
    const modal = document.getElementById('create-article-modal');
    modal.classList.add('hidden'); // Add the 'hidden' class to hide the modal
  }
  
  // Add event listener to the Cancel button
  document.getElementById('cancel-button').addEventListener('click', hideCreateArticleModal);
  