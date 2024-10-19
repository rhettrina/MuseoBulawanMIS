let startX = 0, startY = 0, newX = 0, newY = 0;
let cardCount = 0;
let newCard = null;
let isDragging = false;

const container = document.getElementById('container');
const addCardButton = document.getElementById('addCardButton');
const trashBin = document.getElementById('trashBin');

// Listen for mousedown on the button to start generating a new card
addCardButton.addEventListener('mousedown', (e) => {
    isDragging = true;

    // Create the new card when the button is clicked
    newCard = document.createElement('div');
    newCard.classList.add('card');
    newCard.innerHTML = `<h1>Card ${++cardCount}</h1>`;

    // Append the card to the container but not yet positioned
    container.appendChild(newCard);

    // Track the starting mouse position
    startX = e.clientX;
    startY = e.clientY;

    // Attach mousemove and mouseup listeners to handle dragging and releasing
    document.addEventListener('mousemove', mouseMoveForNewCard);
    document.addEventListener('mouseup', () => mouseUpForNewCard(newCard), { once: true });
});

function mouseMoveForNewCard(e) {
    if (!isDragging) return;

    // Update the card's position to follow the mouse during the drag
    newX = e.clientX - startX;
    newY = e.clientY - startY;

    newCard.style.left = `${e.clientX - 200}px`;  // Center the card relative to the mouse
    newCard.style.top = `${e.clientY - 200}px`;
}

function mouseUpForNewCard(card) {
    if (!isDragging) return;

    isDragging = false;

    // Position the card where the mouse is released
    card.style.left = `${event.clientX - 200}px`;
    card.style.top = `${event.clientY - 200}px`;

    // Remove the event listeners when mouse is released
    document.removeEventListener('mousemove', mouseMoveForNewCard);

    // Make this new card draggable after it's been created
    addDragFunctionality(card);
}

// Function to add drag functionality to any card
function addDragFunctionality(card) {
    card.addEventListener('mousedown', function (e) {
        isDragging = true;

        // Get the current position of the mouse
        startX = e.clientX;
        startY = e.clientY;

        // Listen for mouse movements to drag the card
        document.addEventListener('mousemove', mouseMoveForExistingCard);
        document.addEventListener('mouseup', () => mouseUpForExistingCard(card), { once: true });

        function mouseMoveForExistingCard(e) {
            if (!isDragging) return;

            // Update card's position as it moves with the mouse
            newX = e.clientX - startX;
            newY = e.clientY - startY;

            startX = e.clientX;
            startY = e.clientY;

            card.style.left = (card.offsetLeft + newX) + 'px';
            card.style.top = (card.offsetTop + newY) + 'px';

            // Check if card is over the trash bin
            checkIfOverTrashBin(card, e.clientX, e.clientY);
        }

        function mouseUpForExistingCard(card) {
            // Finalize the card drag and check if it's over the trash bin for deletion
            const trashRect = trashBin.getBoundingClientRect();
            const cardRect = card.getBoundingClientRect();

            // Check if the card was dropped inside the trash bin
            if (
                cardRect.left < trashRect.right &&
                cardRect.right > trashRect.left &&
                cardRect.top < trashRect.bottom &&
                cardRect.bottom > trashRect.top
            ) {
                // Remove the card
                card.remove();
            }

            isDragging = false;
            document.removeEventListener('mousemove', mouseMoveForExistingCard);
        }
    });
}

// Function to check if the card is over the trash bin
function checkIfOverTrashBin(card, mouseX, mouseY) {
    const trashRect = trashBin.getBoundingClientRect();
    
    if (
        mouseX > trashRect.left &&
        mouseX < trashRect.right &&
        mouseY > trashRect.top &&
        mouseY < trashRect.bottom
    ) {
        card.style.backgroundColor = 'red'; // Indicate it's over the trash bin
    } else {
        card.style.backgroundColor = 'antiquewhite'; // Reset color
    }
}
