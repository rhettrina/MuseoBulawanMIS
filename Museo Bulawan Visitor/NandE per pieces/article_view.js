document.addEventListener('DOMContentLoaded', function() {
    // Retrieve article data from localStorage
    const article = JSON.parse(localStorage.getItem('selectedArticle'));

    if (article) {
        // Populate the template with article data
        document.getElementById('title').innerText = article.article_title || 'Untitled Article';
        document.getElementById('date').innerText = article.created_at || 'Unknown Date';
        document.getElementById('author').innerText = article.author || 'Unknown Author';
        document.getElementById('location').innerText = article.location || 'Unknown Location';
        document.getElementById('type').innerText = article.article_type || 'Unknown Type';

        // Define base URL for images
        const imageBaseUrl = 'https://lightpink-dogfish-795437.hostingersite.com/admin_mis/src/uploads/articlesUploads/';

        // Handle image 1 (imgu1)
        const imagePath1 = article.imgu1 
            ? imageBaseUrl + article.imgu1 
            : 'https://lightpink-dogfish-795437.hostingersite.com/admin_mis/src/uploads/articlesUploads/default-image.jpg';  // Fallback image if not found
        document.getElementById('img-container1').src = imagePath1;
        document.getElementById('img-dets').innerText = article.imgu1_details || 'No image details available';

        // Handle image 2 (imgu2)
        const imagePath2 = article.imgu2 && article.imgu2.trim() !== ''
            ? imageBaseUrl + article.imgu2
            : 'https://lightpink-dogfish-795437.hostingersite.com/admin_mis/src/uploads/articlesUploads/default-image.jpg';  // Fallback image if not found or empty
        document.getElementById('img-container2').src = imagePath2;

        // Handle image 3 (imgu3)
        const imagePath3 = article.imgu3 && article.imgu3.trim() !== ''
            ? imageBaseUrl + article.imgu3
            : 'https://lightpink-dogfish-795437.hostingersite.com/admin_mis/src/uploads/articlesUploads/default-image.jpg';  // Fallback image if not found or empty
        document.getElementById('img-container3').src = imagePath3;

        // Handle text content for left and right
        document.getElementById('text-left').innerText = article.p1box_left || 'No left text available';
        document.getElementById('text-right').innerText = article.p1box_right || 'No right text available';

        // Handle last modified date
        document.getElementById('last-modified').innerText = article.updated_date || 'Not modified';

        // Optionally log the article object for debugging
        console.log(article);
    } else {
        console.error('No article data found in localStorage.');
    }
});
