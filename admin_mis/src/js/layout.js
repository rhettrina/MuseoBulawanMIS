(() => {
  let currentActiveCard = null; // Scoped to layout.js

  function init() {
    console.log("Initializing layout.js...");
    fetchAndDisplayFloorplans(); // Initial fetch of floorplans
    setupModal();
    setupPreviewModal(); // Set up the preview modal

    // Attach sort change event listener
    document.getElementById("sort").addEventListener("change", (event) => {
      const sortOption = event.target.value; // Get selected sort option
      fetchAndDisplayFloorplans(sortOption); // Fetch and display floorplans with the selected sorting
    });
  }

  function cleanup() {
    console.log("Cleaning up layout.js...");
    currentActiveCard = null;
    // Remove other event listeners or reset state if needed
  }

  // Function to toggle the active status color and limit to one active
  function toggleActiveStatus(activeStatusElement) {
    if (currentActiveCard) {
      currentActiveCard.classList.remove("bg-green-700");
      currentActiveCard.classList.add("bg-transparent");
    }

    activeStatusElement.classList.remove("bg-transparent");
    activeStatusElement.classList.add("bg-green-700");

    currentActiveCard = activeStatusElement;
  }

  // Expose necessary functions to the global scope
  window.init = init;
  window.cleanup = cleanup;
  window.toggleActiveStatus = toggleActiveStatus;

  // Add updateTotalLayoutsCount to the global scope
  window.updateTotalLayoutsCount = function (count) {
    console.log("Updating total layouts count:", count);
    const totalLayoutsElement = document.getElementById("total-layouts");
    if (!totalLayoutsElement) {
      console.error("Element with id 'total-layouts' not found!");
    } else {
      totalLayoutsElement.textContent = count; // Update the span with the total count
    }
  };
})();

async function fetchAndDisplayFloorplans(sort = "newest") {
  try {
    const response = await fetch(
      `https://museobulawan.online/development/admin_mis/src/php/fetchLayouts.php?sort=${sort}`
    );
    const data = await response.json();

    if (data.status === "success" && Array.isArray(data.data)) {
      const floorplans = data.data;

      const container = document.querySelector(
        ".w-full.h-full.flex.flex-wrap.gap-4"
      );
      container.innerHTML = ""; // Clear existing cards

      // Dynamically create and append cards
      floorplans.forEach((floorplan) => {
        const { unique_id, name, image_path, created_at, active } = floorplan;

        // Ensure unique_id is provided
        if (!unique_id) {
          console.error("Unique ID not provided for a floorplan:", floorplan);
          return; // Skip rendering this floorplan
        }

        // Handle full and relative paths for images
        const fullImagePath = image_path.startsWith("http")
          ? image_path
          : `https://museobulawan.online/development/admin_mis/src/${image_path}`;

        const card = document.createElement("div");
        card.className =
          "bg-orange-200 shadow-md rounded-lg overflow-hidden w-[300px] h-[350px] border-[1px] border-black flex flex-col";

        card.innerHTML = `
          <div class="flex-1">
            <img id="layout-source" src="${fullImagePath}" alt="${name}" class="rounded-lg w-full h-[200px] object-cover">
          </div>
          <div class="m-[5px] rounded-lg p-2 bg-orange-100">
            <h3 class="text-sm font-semibold text-black truncate" id="layout-title">Layout name: ${name}</h3>
            <p class="text-black my-2 text-sm">Creation Date: <span id="created-date">${created_at}</span></p>
            <div class="flex items-center justify-between">
              <button class="set-layout-btn flex items-center text-black text-sm p-2 rounded hover:bg-orange-300">
                <i class="fas fa-layer-group mr-2"></i>
              </button>
              <button class="view-layout-btn flex items-center text-black text-sm p-2 rounded hover:bg-orange-300">
                <i class="fas fa-eye mr-2"></i>
              </button>
              <button class="delete-layout-btn flex items-center text-black text-sm p-2 rounded hover:bg-orange-300">
                <i class="fas fa-trash mr-2"></i>
              </button>
            </div>
          </div>
          <div id="active-status-${unique_id}" class="w-full h-[10px] ${
          active === 1 ? "bg-green-700" : "bg-transparent"
        }"></div>
        `;

        container.appendChild(card);

        // Attach event listeners to the buttons for each card
        const setButton = card.querySelector(".set-layout-btn");
        const viewButton = card.querySelector(".view-layout-btn");
        const deleteButton = card.querySelector(".delete-layout-btn");
        const activeStatus = card.querySelector(`#active-status-${unique_id}`);

        setButton.addEventListener("click", () =>
          handleFunctions("set", { id: unique_id, name, activeStatus })
        );
        viewButton.addEventListener("click", () =>
          openPreviewModal(fullImagePath, name) // Preview triggered on "view" button click
        );
        deleteButton.addEventListener("click", () =>
          handleFunctions("delete", { id: unique_id, name })
        );
      });

      // Update the total layouts count
      updateTotalLayoutsCount(floorplans.length);
    } else {
      console.error("No floorplans found or invalid API response.");
      document.querySelector(
        ".w-full.h-full.flex.flex-wrap.gap-4"
      ).innerHTML = `<p class="text-center w-full">No layouts available.</p>`;
    }
  } catch (error) {
    console.error("An unexpected error occurred:", error);
    document.querySelector(
      ".w-full.h-full.flex.flex-wrap.gap-4"
    ).innerHTML = `<p class="text-center w-full text-red-500">Error loading layouts. Please try again.</p>`;
  }
}

function handleFunctions(action, payload) {
  const { id, name, activeStatus } = payload;

  switch (action) {
    case "set":
      openModal(`Are you sure you want to set layout "${name}" as active?`, () => {
        console.log(id);
        toggleActiveStatus(activeStatus);
        updateActive(id);
        
      });
      break;

    case "delete":
      openModal(`Are you sure you want to delete layout "${name}"?`, async () => {
        try {
          const response = await fetch(
            `https://museobulawan.online/development/admin_mis/src/php/deleteLayout.php`,
            {
              method: "POST",
              headers: {
                "Content-Type": "application/json",
              },
              body: JSON.stringify({ id }),
            }
          );

          const result = await response.json();
          if (result.status === "success") {
            console.log(`Layout with ID: ${id} deleted successfully.`);
            fetchAndDisplayFloorplans(); // Reload all the cards dynamically
          } else {
            console.error("Error deleting layout:", result.message);
          }
        } catch (error) {
          console.error("An unexpected error occurred during deletion:", error);
        }
      });
      break;

    default:
      console.error("Unknown action:", action);
  }
}
function updateActive(uniqueId) {
  if (!uniqueId) {
    console.error("Unique ID is required to update active layout!");
    return;
  }

  const requestBody = { unique_id: uniqueId };
  console.log("Sending request with body:", requestBody);

  fetch('https://museobulawan.online/development/admin_mis/src/php/update_active.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
    },
    body: JSON.stringify(requestBody), // Convert the payload to JSON
  })
    .then(response => {
      console.log("Response status:", response.status);
      return response.json();
    })
    .then(data => {
      console.log("Server response:", data);
      if (data.success) {
        console.log(data.message);

        // Refresh the floorplans to reflect the updated status
        fetchAndDisplayFloorplans();
      } else {
        console.error(data.message);
      }
    })
    .catch(error => console.error('Error:', error));
}

function closeModal() {
  const modalOverlay = document.getElementById("modal-overlay");
  modalOverlay.classList.add("hidden");
}

// Preview modal setup
function setupPreviewModal() {
  const previewModalHtml = `
    <div id="preview-modal-overlay" class="fixed inset-0 bg-black bg-opacity-70 flex justify-center items-center hidden z-50">
      <div id="preview-modal" class="bg-white rounded-lg shadow-lg w-4/5 h-4/5 relative p-4 flex flex-col">
        <button id="close-preview" class="absolute top-2 right-2 bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600">Close</button>
        <img id="preview-image" src="" alt="Preview" class="w-full h-full object-contain">
      </div>
    </div>
  `;
  document.body.insertAdjacentHTML("beforeend", previewModalHtml);

  const closePreviewButton = document.getElementById("close-preview");
  closePreviewButton.addEventListener("click", closePreviewModal);
}

function openPreviewModal(imageSrc, title) {
  const previewOverlay = document.getElementById("preview-modal-overlay");
  const previewImage = document.getElementById("preview-image");

  previewImage.src = imageSrc;
  previewImage.alt = title;

  previewOverlay.classList.remove("hidden");
}

function closePreviewModal() {
  const previewOverlay = document.getElementById("preview-modal-overlay");
  previewOverlay.classList.add("hidden");
}



// Modal setup and handling
function setupModal() {
  const modalHtml = `
        <div id="modal-overlay" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center hidden">
            <div class="bg-white rounded-lg shadow-lg p-6 max-w-md w-full">
                <p id="modal-message" class="text-black mb-4"></p>
                <div class="flex justify-end">
                    <button id="modal-cancel-btn" class="bg-gray-300 text-black px-4 py-2 rounded hover:bg-gray-400 mr-2">Cancel</button>
                    <button id="modal-confirm-btn" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">Confirm</button>
                </div>
            </div>
        </div>
    `;

  document.body.insertAdjacentHTML("beforeend", modalHtml);

  document
    .getElementById("modal-cancel-btn")
    .addEventListener("click", closeModal);
  document.getElementById("modal-overlay").addEventListener("click", (event) => {
    if (event.target.id === "modal-overlay") closeModal();
  });
}

// Expose openModal to global scope
window.openModal = function (message, onConfirm) {
  const modalOverlay = document.getElementById("modal-overlay");
  const modalMessage = document.getElementById("modal-message");
  const confirmButton = document.getElementById("modal-confirm-btn");

  modalMessage.textContent = message;
  modalOverlay.classList.remove("hidden");

  const newConfirmButton = confirmButton.cloneNode(true);
  confirmButton.parentNode.replaceChild(newConfirmButton, confirmButton);

  newConfirmButton.addEventListener("click", () => {
    onConfirm();
    closeModal();
  });
};

// Expose closeModal to global scope
window.closeModal = function () {
  const modalOverlay = document.getElementById("modal-overlay");
  modalOverlay.classList.add("hidden");
};



// Initialize the application when the page loads
window.onload = init;
