// Function to get the full image URL (from PHP response)
function getFullImageUrl(baseUrl, imagePath) {
    // Ensure the path doesn't start with a slash
    imagePath = imagePath ? imagePath.replace(/^\/+/, '') : '';

    // If the image path is empty, return the default image URL
    if (!imagePath) {
        return `${baseUrl}/default-image.jpg`;
    }

    // If the image path is already a valid URL, return it as is
    if (imagePath.startsWith('http://') || imagePath.startsWith('https://')) {
        return imagePath;
    }

    // Otherwise, append the base URL to the image path
    return `${baseUrl}/${imagePath}`;
}

// Function to fetch articles
function fetchArticles() {
    fetch('https://museobulawan.online/Museo%20Bulawan%20Visitor/News%20and%20Events/fetch_articles.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
        },
        body: JSON.stringify({
            action: 'fetch_articles'
        })
    })
    .then(response => response.json())
    .then(articles => {
        if (articles.error) {
            console.error('Error:', articles.error);
            return;
        }

        const articleContainer = document.querySelector('#articles-container');
        articleContainer.innerHTML = ''; // Clear existing articles

        if (articles.length === 0) {
            articleContainer.innerHTML = '<p>No articles available at this time.</p>';
            return;
        }

        articles.forEach(article => {
            // Define the base URL for images
            const imageBaseUrl = 'https://museobulawan.online/admin_mis/src/uploads/articlesUploads/';

            // Get the full image URL using the function
            const imagePath1 = article.imgu1 ? getFullImageUrl(imageBaseUrl, article.imgu1) : `${imageBaseUrl}default-image.jpg`;

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

// Call the function to fetch articles
fetchArticles();
    