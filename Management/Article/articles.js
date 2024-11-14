// Function to preview an article
function previewArticle(articleId) {
    console.log(`Previewing article with ID: ${articleId}`);
    // Add functionality to display article details in a preview modal
}

// Function to edit an article
function editArticle(articleId) {
    console.log(`Editing article with ID: ${articleId}`);
    // Add functionality to redirect to or open an article editing interface
}

async function deleteArticle(articleId) {
    const confirmDelete = confirm("Are you sure you want to delete this article?");
    if (confirmDelete) {
        try {
            const response = await fetch(`delete_article.php`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ id: articleId })
            });

            if (response.ok) {
                alert("Article deleted successfully.");
                loadArticles(); // Reload articles to reflect the deletion
            } else {
                const errorData = await response.json();
                alert(errorData.message || "Failed to delete the article.");
            }
        } catch (error) {
            console.error("Error deleting article:", error);
            alert("An error occurred while trying to delete the article.");
        }
    }
}

async function loadArticles(sortOrder = "newest") {
    try {
        const response = await fetch(`get_articles.php?sort=${sortOrder === "oldest" ? "oldest" : "newest"}`);
        const data = await response.json();

        // Update total article count
        document.getElementById('total-articles').innerText = data.total;

        // Select the article table container
        const articleTable = document.querySelector('.article-table');
        articleTable.innerHTML = ''; // Clear any existing content

        data.articles.forEach(article => {
            const articleData = document.createElement('div');
            articleData.classList.add('article-data');
        
            // Format the created_at date
            const createdAt = new Date(article.created_at);
            const formattedDate = createdAt.toLocaleDateString('en-CA'); // 'en-CA' format is YYYY-MM-DD
        
            articleData.innerHTML = `
                <div class="data-wrapper">
                    <p>${formattedDate}</p> <!-- Use formatted date here -->
                </div>
                <div class="data-wrapper">
                    <p>${article.article_title}</p>
                </div>
                <div class="data-wrapper">
                    <p>${article.article_type}</p>
                </div>
                <div class="data-wrapper">
                    <p>${article.updated_date || 'N/A'}</p>
                </div>
                <div class="data-wrapper">
                    <button type="button" class="btn btn-light view-btn" data-id="${article.id}"><i class="bi bi-eye"></i></button>
                    <button type="button" class="btn btn-light edit-btn" data-id="${article.id}"><i class="bi bi-pencil-square"></i></button>
                    <button type="button" class="btn btn-light delete-btn" data-id="${article.id}"><i class="bi bi-trash"></i></button>
                </div>
            `;
        
            // Append article data to the table
            articleTable.appendChild(articleData);
        });

        // Add event listeners for buttons
        document.querySelectorAll('.view-btn').forEach(button => {
            button.addEventListener('click', event => {
                const articleId = event.target.closest('button').getAttribute('data-id');
                previewArticle(articleId);
            });
        });

        document.querySelectorAll('.edit-btn').forEach(button => {
            button.addEventListener('click', event => {
                const articleId = event.target.closest('button').getAttribute('data-id');
                editArticle(articleId);
            });
        });

        document.querySelectorAll('.delete-btn').forEach(button => {
            button.addEventListener('click', event => {
                const articleId = event.target.closest('button').getAttribute('data-id');
                deleteArticle(articleId);
            });
        });

    } catch (error) {
        console.error("Error loading articles:", error);
    }
}

// Add event listener to the sort dropdown menu
document.getElementById('sort').addEventListener('change', function(event) {
    const sortOrder = event.target.value;
    loadArticles(sortOrder); // Load articles based on selected sort order
});



// Call the loadArticles function to initialize data
loadArticles();


