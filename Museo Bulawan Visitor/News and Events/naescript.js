function fetchArticles() {
    fetch('http://localhost/MuseoBulawanMIS/Museo%20Bulawan%20Visitor/News%20and%20Events/fetch_articles.php', {
        method: 'POST', // Change method to POST
        headers: {
            'Content-Type': 'application/json', // Set content type to JSON
            'X-Requested-With': 'XMLHttpRequest' // Custom header to indicate an AJAX request
        },
        body: JSON.stringify({
            action: 'fetch_articles' // Optionally send additional data to the server
        })
    })
    .then(response => response.json())
    .then(articles => {
        if (articles.error) {
            console.error('Error:', articles.error);
            return; // Stop further execution if there is an error
        }

        const articleContainer = document.querySelector('#articles-container');
        articleContainer.innerHTML = '';

        articles.forEach(article => {
            const imagePath = article.imgu1.includes('uploads/') ? article.imgu1.split('uploads/')[1] : article.imgu1;

            const articleDiv = document.createElement('div');
            articleDiv.classList.add('col-lg-3', 'col-md-4', 'col-sm-6', 'feature-box');
            articleDiv.innerHTML = `
                <div class="article-preview" data-id="${article.id}">
                    <img src="../../Management/Article/uploads/${imagePath}" alt="${article.article_title}" class="img-fluid">
                    <h5>${article.article_type}</h5>
                    <h2>${article.article_title}</h2>
                    <h4>${article.author}</h4>
                </div>
            `;

            articleContainer.appendChild(articleDiv);

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

        // Handle img-container1, img-container2, and img-container3
        const imagePath1 = article.imgu1 && article.imgu1.includes('uploads/') ? article.imgu1.split('uploads/')[1] : 'uploads/default-image.jpg';
        const imagePath2 = article.imgu2 && article.imgu2.includes('uploads/') ? article.imgu2.split('uploads/')[1] : 'uploads/default-image.jpg';
        const imagePath3 = article.imgu3 && article.imgu3.includes('uploads/') ? article.imgu3.split('uploads/')[1] : 'uploads/default-image.jpg';

        document.getElementById('img-container1').src = `../../Management/Article/uploads/${imagePath1}`;
        document.getElementById('img-container2').src = `../../Management/Article/uploads/${imagePath2}`;
        document.getElementById('img-container3').src = `../../Management/Article/uploads/${imagePath3}`;

        document.getElementById('img-dets').innerText = article.imgu1_details || 'No image details available';
        document.getElementById('text-left').innerText = article.p1box_left || 'No left text available';
        document.getElementById('text-right').innerText = article.p1box_right || 'No right text available';
        document.getElementById('last-modified').innerText = article.updated_date || 'Not modified';
    }

    // Fetch articles when the page loads
    fetchArticles();
