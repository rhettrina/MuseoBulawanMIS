// Sample data array with an additional 'category' property
const featureData = [
    {
        imageSrc: '../source/backgroundimg.png',
        pieceName: 'Piece Name 1',
        artistName: 'Artist Name 1',
        category: 'Painting'
    },
    {
        imageSrc: '../source/bgsearchimg.png',
        pieceName: 'Piece Name 2',
        artistName: 'Artist Name 2',
        category: 'Sculpture'
    },
    {
        imageSrc: '../source/backgroundimg.png',
        pieceName: 'Piece Name 3',
        artistName: 'Artist Name 3',
        category: 'Photography'
    },
    {
        imageSrc: '../source/backgroundimg.png',
        pieceName: 'Piece Name 4',
        artistName: 'Artist Name 4',
        category: 'Digital Art'
    },
    {
        imageSrc: '../source/backgroundimg.png',
        pieceName: 'Piece Name 4',
        artistName: 'Artist Name 4',
        category: 'Digital Art'
    },
    {
        imageSrc: '../source/backgroundimg.png',
        pieceName: 'Piece Name 4',
        artistName: 'Artist Name 4',
        category: 'Digital Art'
    },
    {
        imageSrc: '../source/backgroundimg.png',
        pieceName: 'Piece Name 4',
        artistName: 'Artist Name 4',
        category: 'Digital Art'
    },
    {
        imageSrc: '../source/backgroundimg.png',
        pieceName: 'Piece Name 4',
        artistName: 'Artist Name 4',
        category: 'Digital Art'
    },
    {
        imageSrc: '../source/backgroundimg.png',
        pieceName: 'Piece Name 4',
        artistName: 'Artist Name 4',
        category: 'Digital Art'
    },
    {
        imageSrc: '../source/backgroundimg.png',
        pieceName: 'Piece Name 4',
        artistName: 'Artist Name 4',
        category: 'Digital Art'
    },
    {
        imageSrc: '../source/backgroundimg.png',
        pieceName: 'Piece Name 4',
        artistName: 'Artist Name 4',
        category: 'Digital Art'
    },
    {
        imageSrc: '../source/backgroundimg.png',
        pieceName: 'Piece Name 4',
        artistName: 'Artist Name 4',
        category: 'Digital Art'
    },
    {
        imageSrc: '../source/backgroundimg.png',
        pieceName: 'Piece Name 4',
        artistName: 'Artist Name 4',
        category: 'Digital Art'
    }
];

function createFeatureBox(imageSrc, pieceName, artistName, category) {
    const featureBox = document.createElement('div');
    featureBox.className = 'feature-box col-lg-3 col-md-4 col-sm-6';

    const img = document.createElement('img');
    img.src = imageSrc;
    img.alt = pieceName;

    const textarea = document.createElement('div');
    textarea.className = 'textarea';

    const h5 = document.createElement('h5');
    h5.textContent = category; // Use the category from the data

    const h2 = document.createElement('h2');
    h2.textContent = pieceName;

    const h4 = document.createElement('h4');
    h4.textContent = artistName;

    textarea.appendChild(h5);
    textarea.appendChild(h2);
    textarea.appendChild(h4);

    featureBox.appendChild(img);
    featureBox.appendChild(textarea);

    return featureBox;
}

function populateFeatureBoxes(dataArray) {
    const row = document.querySelector('.row');
    row.innerHTML = ''; // Clear existing content

    dataArray.forEach(data => {
        const newFeatureBox = createFeatureBox(data.imageSrc, data.pieceName, data.artistName, data.category);
        row.appendChild(newFeatureBox);
    });
}

// Populate the feature boxes with the sample data
populateFeatureBoxes(featureData);


// end of data function for collection