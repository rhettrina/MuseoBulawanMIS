function init(){
    //call the display functions here
    loadFloorPlans();
}

// Function to load floor plans from the server
function loadFloorPlans() {
    fetch('fetch_floorplans.php')  // Replace with the correct API path
      .then(response => response.json())
      .then(floorPlans => {
        const container = document.getElementById('floorPlansContainer');
        container.innerHTML = ''; // Clear existing content
  
        floorPlans.forEach(floorPlan => {
          // Create the card element
          const card = document.createElement('div');
          card.classList.add('card', 'bg-white', 'shadow-lg', 'rounded-lg', 'overflow-hidden');
          
          // Create and append the image to the card
          const img = document.createElement('img');
          img.src = floorPlan.image_url; // URL of the image stored in the database
          img.alt = floorPlan.name;
          img.classList.add('w-full', 'h-48', 'object-cover');
          
          // Create the content section for name and created_at
          const content = document.createElement('div');
          content.classList.add('p-4');
          
          const name = document.createElement('h3');
          name.classList.add('text-lg', 'font-semibold', 'text-center');
          name.textContent = floorPlan.name;
          
          const createdAt = document.createElement('p');
          createdAt.classList.add('text-sm', 'text-gray-500', 'text-center');
          createdAt.textContent = `Created at: ${floorPlan.created_at}`;
  
          // Append the name and created_at to the content
          content.appendChild(name);
          content.appendChild(createdAt);
          
          // Append the image and content to the card
          card.appendChild(img);
          card.appendChild(content);
  
          // Add the card to the container
          container.appendChild(card);
        });
      })
      .catch(error => {
        console.error('Error fetching floor plans:', error);
        const container = document.getElementById('floorPlansContainer');
        container.innerHTML = '<p>Failed to load floor plans.</p>';
      });
  }
  
  // Load floor plans when the page is ready
  document.addEventListener('DOMContentLoaded', loadFloorPlans);
  