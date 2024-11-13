// Function to show specific pages
function showPage(pageId) {
    const pages = document.querySelectorAll('.page-content');
    pages.forEach(function (page) {
        if (page.id === pageId) {
            page.style.display = 'block';
            if (pageId === 'artifacts') {
                loadArticles();
            }
        } else {
            page.style.display = 'none';
        }
    });
}

function fetch_total() {
    fetch('http://localhost/MuseoBulawanMIS/Management/Article/fetch_total.php')
        .then(response => response.json())
        .then(data => {
            const total = data.total;
            document.getElementById('article_total').textContent = total;
        })
        .catch(error => console.error('Error fetching total:', error));
}

function loadArticles(sortOrder = 'newest') {
    fetch('http://localhost/MuseoBulawanMIS/Management/Article/fetch_articles.php')
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok: ' + response.statusText);
            }
            return response.json();
        })
        .then(data => {
            const articlesList = document.getElementById('articles-list');
            articlesList.innerHTML = '';

            if (data.length === 0) {
                articlesList.innerHTML = '<div>No articles available</div>';
                return;
            }


            data.sort((a, b) => {
                if (sortOrder === 'newest') {
                    return new Date(b.created_at) - new Date(a.created_at);
                } else {
                    return new Date(a.created_at) - new Date(b.created_at);
                }
            });

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


document.getElementById('sort').addEventListener('change', function () {
    const sortOrder = this.value === 'date' ? 'newest' : 'oldest';
    loadArticles(sortOrder);
});

document.addEventListener('DOMContentLoaded', () => {
    showPage('artifacts');
    fetch_total();
});


function handleEdit(event) {
    const articleId = event.currentTarget.getAttribute('data-id');
    // Add logic to edit the article
    console.log(`Edit button clicked for article with ID: ${articleId}`);
    // Redirect to an edit page or open an edit modal, e.g.:
    // window.location.href = `/path/to/edit/page?id=${articleId}`;
}

function handleDelete(event) {
    const articleId = event.currentTarget.getAttribute('data-id');
    // Add logic to delete the article
    console.log(`Delete button clicked for article with ID: ${articleId}`);
    // Optionally, you might want to confirm deletion:
    // if (confirm('Are you sure you want to delete this article?')) {
    //     // Perform deletion logic here, e.g., send a delete request to the server
    // }
}
