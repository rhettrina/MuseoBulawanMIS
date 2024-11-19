function test(){
    console.log("test");
}

function fetchTotalArticles() {
    fetch('/admin_mis/src/php/fetchTotalArticles.php')
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
            console.error('Error fetching total articles:', error.message);
            document.getElementById('total-articles').innerText = "Error fetching data";
        });
}

// Fetch and populate the articles table
function fetchArticles(sort = 'newest') {
    fetch(`/admin_mis/src/php/fetchArticles.php?sort=${sort}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok: ' + response.statusText);
            }
            return response.json();
        })
        .then(data => {
            if (data.error) {
                console.error('Error from PHP:', data.error);
                displayNoDataMessage();
            } else {
                populateTable(data);
            }
        })
        .catch(error => {
            console.error('Error fetching articles:', error.message);
            displayNoDataMessage();
        });
}

// Populate the articles table
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

// Handle actions like preview, edit, and delete
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
            if (confirm('Are you sure you want to delete this article?')) {
                console.log(`Delete article with ID: ${articleId}`);
                // Implement delete functionality here
            }
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
    localStorage.setItem('sortOption', sortOption); // Save sort option in localStorage
    fetchArticles(sortOption);
});

// On page load, load the saved sort option from localStorage
document.addEventListener('DOMContentLoaded', () => {
    const savedSortOption = localStorage.getItem('sortOption') || 'newest';
    document.getElementById("sort").value = savedSortOption;
    fetchArticles(savedSortOption);
    fetchTotalArticles(); // Fetch total articles
});

// Create article modal functionality
const createArticleButton = document.getElementById('create-article-button');
const modal = document.getElementById('create-article-modal');
const cancelButton = document.getElementById('cancel-button');

// Show the modal when the Create Article button is clicked
createArticleButton.addEventListener('click', () => {
    modal.classList.remove('hidden');  // Show the modal
});

// Hide the modal when the Cancel button is clicked
cancelButton.addEventListener('click', () => {
    modal.classList.add('hidden');  // Hide the modal
    document.getElementById('create-article-form').reset(); // Reset the form
});

// Form submission handling
const createArticleForm = document.getElementById('create-article-form');
createArticleForm.addEventListener('submit', function(event) {
    event.preventDefault();
    const formData = new FormData(createArticleForm);
    // Perform form submission to backend for creating a new article
    fetch('/admin_mis/src/php/createArticle.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Error creating article: ' + response.statusText);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            alert('Article created successfully!');
            modal.classList.add('hidden');
            fetchArticles(); // Refresh the articles table
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error creating article:', error.message);
        alert('An error occurred while creating the article.');
    });
});
