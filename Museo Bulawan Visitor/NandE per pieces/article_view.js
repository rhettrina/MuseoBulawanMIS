// article_view.js
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

        // Handle image 1 (imgu1)
        const imagePath1 = article.imgu1 && article.imgu1.includes('uploads/')
            ? article.imgu1.split('uploads/')[1]
            : 'uploads/default-image.jpg';  // Fallback image if not found
        document.getElementById('img-container1').src = `../../Management/Article/uploads/${imagePath1}`;
        document.getElementById('img-dets').innerText = article.imgu1_details || 'No image details available';

        // Handle image 2 (imgu2)
        const imagePath2 = (article.imgu2 && article.imgu2.trim() !== '') && article.imgu2.includes('uploads/')
            ? article.imgu2.split('uploads/')[1]
            : 'uploads/default-image.jpg';  // Fallback image if not found or empty
        document.getElementById('img-container2').src = `../../Management/Article/uploads/${imagePath2}`;

        // Handle image 3 (imgu3)
        const imagePath3 = (article.imgu3 && article.imgu3.trim() !== '' && article.imgu3.includes('uploads/'))
            ? article.imgu3.split('uploads/')[1]
            : 'uploads/default-image.jpg';  // Fallback image if not found or empty
        document.getElementById('img-container3').src = `../../Management/Article/uploads/${imagePath3}`;

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
