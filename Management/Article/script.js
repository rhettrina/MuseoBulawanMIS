// Function to show specific pages
function showPage(pageId) {
    var pages = document.querySelectorAll('.page-content');
    pages.forEach(function(page) {
        if (page.id === pageId) {
            page.style.display = 'block';

            // Load articles data if the articles page is displayed
            if (pageId === 'artifacts') {
                loadArticles();
            }
        } else {
            page.style.display = 'none';
        }
    });
}
// Function to load articles data
function loadArticles() {
    fetch('http://localhost/MuseoBulawanMIS/Management/Article/fetch_articles.php')
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok: ' + response.statusText);
            }
            return response.json();
        })
        .then(data => {
            console.log("Data fetched successfully:", data); // Confirm data is fetched
            
            const articlesList = document.getElementById('articles-list');
            articlesList.innerHTML = ''; // Clear existing content

            if (data.length === 0) {
                console.log("No articles found");
                articlesList.innerHTML = '<div>No articles available</div>';
                return;
            }

            data.forEach(article => {
                const row = document.createElement('div');
                row.classList.add('articles-row');

                row.innerHTML = `
                     <div>${article.created_at}</div>
                    <div>${article.article_title || 'No Title'}</div>
                    <div>${article.article_type || 'No Type'}</div>
                    <div>${article.updated_date || 'N/A'}</div>
                    <div>
                        <button type="button" class="btn btn-light edit-btn" data-id="${article.id}">
                            <i class="bi bi-pencil-square"></i>
                        </button>
                        <button type="button" class="btn btn-light delete-btn" data-id="${article.id}">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                `;
                articlesList.appendChild(row);
            });

            document.querySelectorAll('.edit-btn').forEach(button => {
                button.addEventListener('click', handleEdit);
            });

            document.querySelectorAll('.delete-btn').forEach(button => {
                button.addEventListener('click', handleDelete);
            });
        })
        .catch(error => console.error('Error fetching data:', error));
}

document.addEventListener('DOMContentLoaded', () => {
    showPage('artifacts');
});


function handleEdit(event) {
    // Use event.currentTarget to ensure we're getting the button's data-id
    const articleId = event.currentTarget.dataset.id;
    console.log(`Edit article with ID: ${articleId}`);
}

function handleDelete(event) {
    // Use event.currentTarget for consistency
    const articleId = event.currentTarget.dataset.id;
    console.log(`Delete article with ID: ${articleId}`);
}
