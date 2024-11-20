
function init(){
    fetchTotalArticles();
    fetchArticles();
}

function fetchTotalArticles() {
    fetch('https://lightpink-dogfish-795437.hostingersite.com/admin_mis/src/php/fetchTotalArticles.php')
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok: ' + response.statusText);
            }
            return response.json();
        })
        .then(data => {
            if (data.error) {
                console.error('Error from PHP:', data.error);
                document.getElementById('total-articles').innerText = "Error fetching data";
            } else {
                document.getElementById('total-articles').innerText = data.total_articles;
            }
        })
        .catch(error => {
            console.error('Error fetching total articles:', error);
            document.getElementById('total-articles').innerText = "Error fetching data";
        });
}

// Fetch and populate the articles table
function fetchArticles(sort = 'newest') {
    fetch(`https://lightpink-dogfish-795437.hostingersite.com/admin_mis/src/php/fetchArticles.php?sort=${sort}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok: ' + response.statusText);
            }
            return response.json();
        })
        .then(data => {
            if (data.error) {
                console.error(data.error);
                displayNoDataMessage();
            } else {
                populateTable(data);
            }
        })
        .catch(error => {
            console.error('Error fetching articles:', error);
            displayNoDataMessage();
        });
}

function populateTable(articles) {
    const tableBody = document.getElementById('articles-table').querySelector('tbody');
    tableBody.innerHTML = ''; // Clear existing rows

    if (articles.length === 0) {
        displayNoDataMessage();
        return;
    }

    articles.forEach(article => {
        const row = document.createElement('tr');
        row.classList.add('border-t', 'border-gray-300', 'text-center');

        // Create cells
        const dateCell = document.createElement('td');
        dateCell.classList.add('px-4', 'py-2');
        dateCell.textContent = article.created_at;

        const titleCell = document.createElement('td');
        titleCell.classList.add('px-4', 'py-2');
        titleCell.textContent = article.article_title;

        const typeCell = document.createElement('td');
        typeCell.classList.add('px-4', 'py-2');
        typeCell.textContent = article.article_type;

        const updatedDateCell = document.createElement('td');
        updatedDateCell.classList.add('px-4', 'py-2');
        updatedDateCell.textContent = article.updated_date === "Not Edited" || !article.updated_date
            ? "Not Edited"
            : article.updated_date;

        const actionCell = document.createElement('td');
        actionCell.classList.add('px-4', 'py-2', 'flex', 'justify-center', 'space-x-2');

        // Add buttons with event listeners
        const previewButton = document.createElement('button');
        previewButton.classList.add('bg-orange-400', 'text-white' , 'p-2', 'rounded', 'hover:bg-orange-300');
        previewButton.innerHTML = `<i class="fas fa-eye"></i>`;
        previewButton.addEventListener('click', () => handleAction('preview', article.id));

        const editButton = document.createElement('button');
        editButton.classList.add('bg-orange-400', 'text-white', 'p-2', 'rounded', 'hover:bg-orange-300');
        editButton.innerHTML = `<i class="fas fa-edit"></i>`;
        editButton.addEventListener('click', () => handleAction('edit', article.id));

        const deleteButton = document.createElement('button');
        deleteButton.classList.add('bg-orange-400', 'text-white', 'p-2', 'rounded', 'hover:bg-orange-300');
        deleteButton.innerHTML = `<i class="fas fa-trash"></i>`;
        deleteButton.addEventListener('click', () => handleAction('delete', article.id));


        actionCell.appendChild(previewButton);
        actionCell.appendChild(editButton);
        actionCell.appendChild(deleteButton);

        row.appendChild(dateCell);
        row.appendChild(titleCell);
        row.appendChild(typeCell);
        row.appendChild(updatedDateCell);
        row.appendChild(actionCell);

        tableBody.appendChild(row);
    });
}


function handleAction(action, articleId) {
    switch (action) {
        case 'preview':
            console.log(`Preview article with ID: ${articleId}`);
            // Implement preview functionality here
            break;
        case 'edit':
            console.log(`Edit article with ID: ${articleId}`);
            // Implement edit functionality here
            break;
        case 'delete':
            // Show confirmation modal before deleting
            openDeleteModal((response) => {
                if (response) {
                    console.log(`Article with ID ${articleId} deleted.`);
                    // Implement delete functionality here
                    // For example: deleteArticle(articleId);
                } else {
                    console.log("Delete action canceled.");
                }
            });
            break;
        default:
            console.error('Unknown action:', action);
    }
}



function displayNoDataMessage() {
    const tableBody = document.querySelector('tbody');
    tableBody.innerHTML = `
        <tr>
            <td colspan="5" class="text-center py-4">No articles found or an error occurred.</td>
        </tr>
    `;
}


document.getElementById("sort").addEventListener("change", function () {
    const sortOption = this.value;
    fetchArticles(sortOption);
});











function previewImage(event, previewId) {
    const file = event.target.files[0];
    const preview = document.getElementById(previewId);
  
    function resetPreview() {
      preview.style.backgroundImage = 'none';
      const placeholder = preview.querySelector('span');
      if (placeholder) {
        placeholder.style.display = 'block';
        placeholder.textContent = 'Choose Image';
      }
      event.target.value = ''; 
    }
  
    if (file) {
      if (file.size > 3 * 1024 * 1024) {
        alert('File size exceeds 3MB. Please choose a smaller file.');
        resetPreview();
        return;
      }
  
      const reader = new FileReader();
      reader.onload = function (e) {
        preview.style.backgroundImage = `url(${e.target.result})`;
        preview.style.backgroundSize = 'cover';
        preview.style.backgroundPosition = 'center';
  
        const placeholder = preview.querySelector('span');
        if (placeholder) {
          placeholder.style.display = 'none';
        }
      };
      reader.readAsDataURL(file);
    } else {
      resetPreview();
    }
  }
  
  



  


// Function to open the confirmation modal
function openConfirmationModal(callback) {
    const modal = document.getElementById("confirmation-modal");
    modal.classList.remove("hidden");
  
    // Handling button clicks
    document.getElementById("confirm-button").onclick = () => {
      callback(true);  // Return 'true' if 'Yes' is clicked
      closeModal("confirmation-modal");
    };
  
    document.getElementById("cancel-button").onclick = () => {
      callback(false);  // Return 'false' if 'No' is clicked
      closeModal("confirmation-modal");
    };
  }
  
  // Function to open the delete confirmation modal
  function openDeleteModal(callback) {
    const modal = document.getElementById("delete-modal");
    modal.classList.remove("hidden");
  
    // Handling button clicks
    document.getElementById("delete-confirm-button").onclick = () => {
      callback(true);  // Return 'true' if 'Delete' is clicked
      closeModal("delete-modal");
    };
  
    document.getElementById("delete-cancel-button").onclick = () => {
      callback(false);  // Return 'false' if 'Cancel' is clicked
      closeModal("delete-modal");
    };
  }
  
  // Function to close the modal
  function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    modal.classList.add("hidden");
  }
  


  // Function to open the "Create Article" modal
  function openCreateArticleModal() {
    const modal = document.getElementById("create-article-modal");
    modal.classList.remove("hidden"); 

    const form = document.getElementById("create-article-form");

    // Handle the Cancel button click
    const cancelButton = document.getElementById("create-article-cancel-button");
    cancelButton.onclick = () => {
        openConfirmationModal((confirm) => {
            if (confirm) {
                closeModal("create-article-modal");
                form.reset(); // Reset form fields
            }
        });
    };

    // Handle the Save button click
    const saveButton = modal.querySelector('button[type="submit"]');
    saveButton.onclick = (event) => {
        event.preventDefault(); // Prevent default form submission

        // Validate required fields
        const requiredFields = form.querySelectorAll("[required]");
        let isValid = true;

        requiredFields.forEach((field) => {
            if (!field.value.trim()) {
                isValid = false;
                field.classList.add("border-red-500"); // Highlight field with red border
                field.nextElementSibling?.classList?.remove("hidden"); // Show error message (if any)
            } else {
                field.classList.remove("border-red-500");
                field.nextElementSibling?.classList?.add("hidden"); // Hide error message (if any)
            }
        });

        if (isValid) {
            openConfirmationModal((confirm) => {
                if (confirm) {
                    closeModal("create-article-modal");
                    console.log("Article saved successfully!");
                    form.reset(); // Reset form fields
                }
            });
        } else {
            console.log("Form validation failed. Please fill in all required fields.");
        }
    };
}

// Add event listener to the "Create Article" button
document.getElementById("create-article-button").addEventListener("click", () => {
    openCreateArticleModal();
});

// Function to open the confirmation modal
function openConfirmationModal(callback) {
    const modal = document.getElementById("confirmation-modal");
    modal.classList.remove("hidden"); // Show the confirmation modal

    // Handle Yes button click
    document.getElementById("confirm-button").onclick = () => {
        callback(true); // Return 'true' if confirmed
        closeModal("confirmation-modal");
    };

    // Handle No button click
    document.getElementById("cancel-button").onclick = () => {
        callback(false); // Return 'false' if canceled
        closeModal("confirmation-modal");
    };
}

// Function to close any modal
function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.add("hidden"); // Add the 'hidden' class to hide the modal
    } else {
        console.error(`Modal with ID "${modalId}" not found.`);
    }
}
