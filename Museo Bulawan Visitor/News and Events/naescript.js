function fetchArticles() {
    fetch('https://lightpink-dogfish-795437.hostingersite.com/Museo%20Bulawan%20Visitor/News%20and%20Events/fetch_articles.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
        },
        body: JSON.stringify({
            action: 'fetch_articles' // Request to fetch articles
        })
    })
    .then(response => response.json())
    .then(articles => {
        if (articles.error) {
            console.error('Error:', articles.error);
            return; // Stop further execution if there is an error
        }

        const articleContainer = document.querySelector('#articles-container');
        articleContainer.innerHTML = ''; // Clear existing articles

        if (articles.length === 0) {
            articleContainer.innerHTML = '<p>No articles available at this time.</p>'; // Display message if no articles
        }

        articles.forEach(article => {
            // Handle images with proper fallback
            const imagePath1 = article.imgu1 ? article.imgu1 : 'https://lightpink-dogfish-795437.hostingersite.com/admin_mis/src/uploads/articlesUploads/default-image.jpg';
            const articleDiv = document.createElement('div');
            articleDiv.classList.add('col-lg-3', 'col-md-4', 'col-sm-6', 'feature-box');
            articleDiv.innerHTML = `
                <div class="article-preview" data-id="${article.id}">
                    <img src="${imagePath1}" alt="${article.article_title}" class="img-fluid">
                    <h5>${article.article_type}</h5>
                    <h2>${article.article_title}</h2>
                    <h4>${article.author}</h4>
                </div>
            `;

            articleContainer.appendChild(articleDiv);

            // Handle article click to save data to localStorage and navigate
            articleDiv.querySelector('.article-preview').addEventListener('click', () => {
                localStorage.setItem('selectedArticle', JSON.stringify(article));
                window.location.href = '../NandE%20per%20pieces/article_view.html';
            });
        });
    })
    .catch(error => console.error('Error fetching articles:', error));
}

function populateArticleTemplate(article) {
    document.getElementById('title').innerText = article.article_title || 'Untitled Article';
    document.getElementById('date').innerText = article.created_at || 'Unknown Date';
    document.getElementById('author').innerText = article.author || 'Unknown Author';
    document.getElementById('location').innerText = article.location || 'Unknown Location';
    document.getElementById('type').innerText = article.article_type || 'Unknown Type';

    // Image path handling for the three image containers
    const imagePath1 = article.imgu1 ? article.imgu1 : 'https://lightpink-dogfish-795437.hostingersite.com/admin_mis/src/uploads/articlesUploads/default-image.jpg';
    const imagePath2 = article.imgu2 ? article.imgu2 : 'https://lightpink-dogfish-795437.hostingersite.com/admin_mis/src/uploads/articlesUploads/default-image.jpg';
    const imagePath3 = article.imgu3 ? article.imgu3 : 'https://lightpink-dogfish-795437.hostingersite.com/admin_mis/src/uploads/articlesUploads/default-image.jpg';

    document.getElementById('img-container1').src = imagePath1;
    document.getElementById('img-container2').src = imagePath2;
    document.getElementById('img-container3').src = imagePath3;

    document.getElementById('img-dets').innerText = article.imgu1_details || 'No image details available';
    document.getElementById('text-left').innerText = article.p1box_left || 'No left text available';
    document.getElementById('text-right').innerText = article.p1box_right || 'No right text available';
    document.getElementById('last-modified').innerText = article.updated_date || 'Not modified';
}

// Fetch articles when the page loads
fetchArticles();
