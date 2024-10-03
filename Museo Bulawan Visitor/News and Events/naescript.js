// Sample data array with an additional 'category' property
const featureData = [
    {
        imageSrc: '../source/backgroundimg.png',
        pieceName: 'Piece Name 1',
        artistName: 'Artist Name 1',
        category: 'Painting',
        link: '../NandE per pieces/collctionpagelight.html'
    },
    {
        imageSrc: '../source/bgsearchimg.png',
        pieceName: 'Piece Name 2',
        artistName: 'Artist Name 2',
        category: 'Sculpture',
        link: '../NandE per pieces/collctionpagelight.html'
    },
    {
        imageSrc: '../source/backgroundimg.png',
        pieceName: 'Piece Name 3',
        artistName: 'Artist Name 3',
        category: 'Photography',
        link: '../NandE per pieces/collctionpagelight.html'
    },
    {
        imageSrc: '../source/backgroundimg.png',
        pieceName: 'Piece Name 4',
        artistName: 'Artist Name 4',
        category: 'Digital Art',
        link: '../NandE per pieces/collctionpagelight.html'
    },
    {
        imageSrc: '../source/backgroundimg.png',
        pieceName: 'Piece Name 4',
        artistName: 'Artist Name 4',
        category: 'Digital Art',
        link: '../NandE per pieces/collctionpagelight.html'
    },
    {
        imageSrc: '../source/backgroundimg.png',
        pieceName: 'Piece Name 4',
        artistName: 'Artist Name 4',
        category: 'Digital Art',
        link: '../NandE per pieces/collctionpagelight.html'
    },
    {
        imageSrc: '../source/backgroundimg.png',
        pieceName: 'Piece Name 4',
        artistName: 'Artist Name 4',
        category: 'Digital Art',
        link: '../NandE per pieces/collctionpagelight.html'
    },
    {
        imageSrc: '../source/backgroundimg.png',
        pieceName: 'Piece Name 4',
        artistName: 'Artist Name 4',
        category: 'Digital Art',
        link: '../NandE per pieces/collctionpagelight.html'
    },
    {
        imageSrc: '../source/backgroundimg.png',
        pieceName: 'Piece Name 4',
        artistName: 'Artist Name 4',
        category: 'Digital Art',
        link: '../NandE per pieces/collctionpagelight.html'
    },
    {
        imageSrc: '../source/backgroundimg.png',
        pieceName: 'Piece Name 4',
        artistName: 'Artist Name 4',
        category: 'Digital Art',
        link: '../NandE per pieces/collctionpagelight.html'
    },
    {
        imageSrc: '../source/backgroundimg.png',
        pieceName: 'Piece Name 4',
        artistName: 'Artist Name 4',
        category: 'Digital Art',
        link: '../NandE per pieces/collctionpagelight.html'
    },
    {
        imageSrc: '../source/backgroundimg.png',
        pieceName: 'Piece Name 4',
        artistName: 'Artist Name 4',
        category: 'Digital Art',
        link: '../NandE per pieces/collctionpagelight.html'
    },
    {
        imageSrc: '../source/backgroundimg.png',
        pieceName: 'Piece Name 4',
        artistName: 'Artist Name 4',
        category: 'Digital Art',
        link: '../NandE per pieces/collctionpagelight.html'
    }
];

function createFeatureBox(imageSrc, pieceName, artistName, category, link) {
    const featureBox = document.createElement('div');
    featureBox.className = 'feature-box col-lg-3 col-md-4 col-sm-6';

    const anchor = document.createElement('a'); // Create an anchor tag
    anchor.href = link; // Set the href to the link from the data
    anchor.target = '_blank'; // Optional: open in a new tab

    const img = document.createElement('img');
    img.src = imageSrc;
    img.alt = pieceName;

    const textarea = document.createElement('div');
    textarea.className = 'textarea';

    const h5 = document.createElement('h5');
    h5.textContent = category;

    const h2 = document.createElement('h2');
    h2.textContent = pieceName;

    const h4 = document.createElement('h4');
    h4.textContent = artistName;

    textarea.appendChild(h5);
    textarea.appendChild(h2);
    textarea.appendChild(h4);

    anchor.appendChild(img); // Wrap the image in the anchor
    featureBox.appendChild(anchor); // Append the anchor to the feature box
    featureBox.appendChild(textarea);

    return featureBox;
}


function populateFeatureBoxes(dataArray) {
    const row = document.querySelector('.row');
    row.innerHTML = ''; // Clear existing content

    dataArray.forEach(data => {
        const newFeatureBox = createFeatureBox(data.imageSrc, data.pieceName, data.artistName, data.category, data.link);
        row.appendChild(newFeatureBox);
    });
}

// Populate the feature boxes with the sample data
populateFeatureBoxes(featureData);


// end of data function for collection