
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
        dateCell.classList.add('px-4', 'py-2','bg-white', 'border-black' , 'rounded-l-[15px]', 'border-t-2', 'border-b-2', 'border-l-2');
        dateCell.textContent = article.created_at;

        const titleCell = document.createElement('td');
        titleCell.classList.add('px-4', 'py-2','bg-white', 'border-black' , 'border-t-2', 'border-b-2');
        titleCell.textContent = article.article_title;

        const typeCell = document.createElement('td');
        typeCell.classList.add('px-4', 'py-2','bg-white', 'border-black' , 'border-t-2', 'border-b-2');
        typeCell.textContent = article.article_type;

        const updatedDateCell = document.createElement('td');
        updatedDateCell.classList.add('px-4', 'py-2','bg-white', 'border-black' , 'border-t-2', 'border-b-2');
        updatedDateCell.textContent = article.updated_date === "Not Edited" || !article.updated_date
            ? "Not Edited"
            : article.updated_date;

        const actionCell = document.createElement('td');
        actionCell.classList.add('px-4', 'py-2', 'flex', 'justify-center', 'space-x-2', 'bg-white', 'border-black' , 'rounded-r-[15px]', 'border-t-2', 'border-b-2', 'border-r-2');

        // Add buttons with event listeners
        const previewButton = document.createElement('button');
        previewButton.classList.add('bg-transparent', 'text-black' , 'p-2', 'rounded', 'hover:bg-orange-300');
        previewButton.innerHTML = `<i class="fas fa-eye"></i>`;
        previewButton.addEventListener('click', () => handleAction('preview', article.id));

        const editButton = document.createElement('button');
        editButton.classList.add('bg-transparent', 'text-black', 'p-2', 'rounded', 'hover:bg-orange-300');
        editButton.innerHTML = `<i class="fas fa-edit"></i>`;
        editButton.addEventListener('click', () => handleAction('edit', article.id));

        const deleteButton = document.createElement('button');
        deleteButton.classList.add('bg-transparent', 'text-black', 'p-2', 'rounded', 'hover:bg-orange-300');
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
            fetchArticleDetails(articleId);
            break;
        case 'edit':
            updateArticle(articleId);
            console.log(`Edit article with ID: ${articleId}`);
            // Implement edit functionality here
            
            init();
            break;
        case 'delete':
            openDeleteModal((response) => {
                if (response) {
                    deleteArticle(articleId);
                    console.log(`Article with ID ${articleId} deleted.`);
                    // Implement delete functionality here
                    init();
                } else {
                    console.log("Delete action canceled.");
                }
            });
            break;
        default:
            console.error('Unknown action:', action);
    }
}

function fetchArticleDetails(articleId) {
    fetch(`https://lightpink-dogfish-795437.hostingersite.com/admin_mis/src/php/previewArticle.php?id=${articleId}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.error) {
                console.error('Error fetching article:', data.error);
            } else {
                populateModal(data);
            }
        })
        .catch(error => {
            console.error('Error fetching article details:', error);
        });
}

function populateModal(article) {
    const basePath = 'https://lightpink-dogfish-795437.hostingersite.com/admin_mis/src/uploads/articlesUploads/';

    // Ensure all elements exist before setting their properties
    const titleElement = document.getElementById('article-title-preview');
    const dateElement = document.getElementById('article-date-preview');
    const locationElement = document.getElementById('article-location-preview');
    const typeElement = document.getElementById('article-type-preview');
    const authorElement = document.getElementById('article-author-preview');
    const image1Element = document.getElementById('article-image-1');
    const image1DetailsElement = document.getElementById('article-image-1-details');
    const leftContentElement = document.getElementById('article-left');
    const rightContentElement = document.getElementById('article-right');
    const image2Element = document.getElementById('article-image-2');
    const contentRight2Element = document.getElementById('content-right-2');
    const image3Element = document.getElementById('article-image-3');
    const contentRight3Element = document.getElementById('content-right-3');

    if (titleElement) titleElement.textContent = article.article_title || 'N/A';
    if (dateElement) dateElement.textContent = article.created_at || 'N/A';
    if (locationElement) locationElement.textContent = article.location || 'N/A';
    if (typeElement) typeElement.textContent = article.article_type || 'N/A';
    if (authorElement) authorElement.textContent = article.author || 'N/A';

    // Extract filename from the path and construct the full image URL
    const getFileName = (path) => path ? path.split('/').pop() : '';

    if (image1Element) image1Element.src = article.imgu1 ? basePath + getFileName(article.imgu1) : '';
    if (image1DetailsElement) image1DetailsElement.textContent = article.imgu1_details || '';
    if (leftContentElement) leftContentElement.value = article.p1box_left || '';
    if (rightContentElement) rightContentElement.value = article.p1box_right || '';
    if (image2Element) image2Element.src = article.imgu2 ? basePath + getFileName(article.imgu2) : '';
    if (contentRight2Element) contentRight2Element.value = article.p2box || '';
    if (image3Element) image3Element.src = article.imgu3 ? basePath + getFileName(article.imgu3) : '';
    if (contentRight3Element) contentRight3Element.value = article.p3box || '';

    // Show the modal
    const previewModal = document.getElementById('preview-modal');
    if (previewModal) {
        previewModal.classList.remove('hidden');
    } else {
        console.error('Preview modal not found.');
    }
}


function togglePreview() {
    const closePreviw = document.getElementById("preview-modal");
    closePreviw.classList.toggle("hidden");
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


// Image Preview Function
function previewImage(event, previewId) {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function (e) {
            const preview = document.getElementById(previewId);
            preview.style.backgroundImage = `url(${e.target.result})`;
            preview.classList.remove("hidden"); // Show the preview
        };
        reader.readAsDataURL(file);
    }
}

// Dummy Confirmation Modal for Save
function openConfirmationModal(callback) {
    
    callback(confirm);
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
    const saveButton = document.getElementById("save-article-button"); // Changed to target by ID
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
                    saveArticle();
                    console.log("Article saved successfully!");
                    form.reset();
                }
            });
        } else {
            console.log("Form validation failed. Please fill in all required fields.");
        }
    };
}

// Open the modal on button click
document.getElementById("create-article-button").addEventListener("click", () => {
    openCreateArticleModal();
});

// Close modal function
function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.add("hidden"); // Add the 'hidden' class to hide the modal
    } else {
        console.error(`Modal with ID "${modalId}" not found.`);
    }
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
    const saveButton = document.getElementById("save-article-button"); // Ensure correct ID
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
                    saveArticle();
                    console.log("Article saved successfully!");
                    form.reset();
                }
            });
        } else {
            console.log("Form validation failed. Please fill in all required fields.");
        }
    };
}

// Open the modal on button click
document.getElementById("create-article-button").addEventListener("click", () => {
    openCreateArticleModal();
});

// Close modal function
function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.add("hidden"); // Add the 'hidden' class to hide the modal
    } else {
        console.error(`Modal with ID "${modalId}" not found.`);
    }
}

// Function to save the article
function saveArticle() {
    const form = document.getElementById("create-article-form");
    const formData = new FormData(form); // Collect form data

    // Get file data (Images)
    const image1 = document.getElementById("image-1-input").files[0];
    const image2 = document.getElementById("image-2-input").files[0];
    const image3 = document.getElementById("image-3-input").files[0];

    // Only append the images if they are selected
    if (image1) formData.append("image-1", image1);
    if (image2) formData.append("image-2", image2);
    if (image3) formData.append("image-3", image3);

    // Collect other form fields
    formData.append("article_title", document.getElementById("article-title").value);
    formData.append("article_author", document.getElementById("article-author").value);
    formData.append("location", document.getElementById("update-article-location").value);
    formData.append("article_type", document.getElementById("article-type").value);
    formData.append("content-left", document.getElementById("content-left").value);
    formData.append("content-right", document.getElementById("content-right").value);
    formData.append("content-image2", document.getElementById("content-image2").value);
    formData.append("content-image3", document.getElementById("content-image3").value);

    // Disable the save button while submitting
    const saveButton = document.getElementById("save-article-button");
    saveButton.disabled = true;

    // Send form data to server
    fetch('https://lightpink-dogfish-795437.hostingersite.com/admin_mis/src/php/saveArticle.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())  // Get response as text first
    .then(text => {
        try {
            const data = JSON.parse(text);  // Try to parse it as JSON
            if (data.success) {
                console.log('Article saved successfully');
                closeModal('create-article-modal');
                form.reset(); // Reset the form after successful submission
            } else {
                console.error('Error saving article:', data.error);
                alert('Failed to save the article. Please try again.');
            }
        } catch (e) {
            console.error('Invalid JSON response:', text);
            alert('An error occurred. Please try again.');
        }
    })
    .catch(error => {
        console.error('Error during fetch request:', error);
        alert('An error occurred. Please try again.');
    })
    .finally(() => {
        saveButton.disabled = false; // Re-enable the save button after the request completes
    });
}

// Confirmation Modal for Save
function openConfirmationModal(callback) {

    callback(confirm);
}



// Function to delete an article by ID
function deleteArticle(articleId) {
    // Send a DELETE request to the server
    fetch(`https://lightpink-dogfish-795437.hostingersite.com/admin_mis/src/php/deleteArticle.php?id=${articleId}`, {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json()) // Parse the JSON response
    .then(data => {
        if (data.success) {
            console.log(`Article with ID ${articleId} deleted successfully.`);
            // Remove the deleted article's row from the table
            const row = document.querySelector(`tr[data-article-id="${articleId}"]`);
            if (row) {
                row.remove();
            }
           
            
        } else {
            console.error(`Error deleting article: ${data.error}`);
            alert(`Failed to delete article: ${data.error}`);
        }
    })
    .catch(error => {
        console.error('Error during deletion:', error);
        alert('An error occurred while deleting the article. Please try again.');
    });
}
function setValue(elementId, value) {
    const element = document.getElementById(elementId);
    if (element) {
        element.value = value || ''; // Safely set value or use empty string if null/undefined
    } else {
        console.warn(`Element with ID ${elementId} not found.`);
    }
}

function previewImageU(event, id) {
    const input = event.target; // The file input element
    const previewElement = document.getElementById(id); // The div for preview

    if (!previewElement) {
        console.error(`Preview element with ID "${id}" not found.`);
        return;
    }

    if (input.files && input.files[0]) {
        const file = input.files[0];

        // Validate if the file is an image
        if (!file.type.startsWith('image/')) {
            console.error('Selected file is not an image.');
            alert('Please select a valid image file.');
            return;
        }

        // Use FileReader to read the file and update the preview
        const reader = new FileReader();
        reader.onload = (e) => {
            previewElement.style.backgroundImage = `url(${e.target.result})`;
            previewElement.style.backgroundSize = 'cover';
            previewElement.style.backgroundPosition = 'center';
            previewElement.classList.remove('hidden');
        };
        reader.readAsDataURL(file);
    } else {
        previewElement.classList.add('hidden'); // Hide preview if no file selected
    }
}




function updateArticle(articleId) {
    const modal = document.getElementById("update-article-modal");
    if (!modal) {
        console.error("Update article modal not found.");
        return;
    }
    modal.classList.remove("hidden");

    // Fetch existing article data to populate the modal
    fetch(`https://lightpink-dogfish-795437.hostingersite.com/admin_mis/src/php/previewArticle.php?id=${articleId}`)
        .then(response => response.json())
        .then(data => {
            if (data) {
                // Populate form fields with existing data
                setValue("update-article-title", data.article_title);
                setValue("update-article-author", data.author);
                setValue("update-article-location", data.location);
                setValue("update-article-type", data.article_type);
                setValue("update-content-left", data.p1box_left);
                setValue("update-content-right", data.p1box_right);
                setValue("update-content-box2", data.p2box);
                setValue("update-content-box3", data.p3box);
                setValue("update-image-details", data.imgu1_details);
                setValue("update-article-created-at", data.created_at);
                setValue("update-article-updated-at", data.updated_date);

                const baseUrl = "https://lightpink-dogfish-795437.hostingersite.com/admin_mis/src/uploads/articlesUploads/";

                // Helper function to adjust image URL
                const adjustImageUrl = (filePath) => filePath ? baseUrl + filePath.split('/').pop() : '';

                // Set image previews using the URLs from the database
                setImagePreview("update-image-1", adjustImageUrl(data.imgu1));
                setImagePreview("update-image-2", adjustImageUrl(data.imgu2));
                setImagePreview("update-image-3", adjustImageUrl(data.imgu3));
            } else {
                console.error('Error fetching article details:', data.error);
                alert('Failed to fetch article details. Please try again.');
            }
        })
        .catch(error => {
            console.error('Error during fetch request:', error);
            alert('An error occurred. Please try again.');
        });

    // Handle the Save button click
    const saveButton = document.getElementById("update-article-save-button");
    if (saveButton) {
        saveButton.onclick = () => {
            const formData = new FormData();

            // Collect updated fields
            formData.append("id", articleId);
            formData.append("article_title", document.getElementById("update-article-title").value);
            formData.append("article_author", document.getElementById("update-article-author").value);
            formData.append("location", document.getElementById("update-article-location").value);
            formData.append("article_type", document.getElementById("update-article-type").value);
            formData.append("content_left", document.getElementById("update-content-left").value);
            formData.append("content_right", document.getElementById("update-content-right").value);
            formData.append("image_details", document.getElementById("update-image-details").value);
            formData.append("content_box2", document.getElementById("update-content-box2").value);
            formData.append("content_box3", document.getElementById("update-content-box3").value);

            // Add new images if selected
            const image1 = document.getElementById("update-image-1-input").files[0];
            const image2 = document.getElementById("update-image-2-input").files[0];  // Check file input for img2
            const image3 = document.getElementById("update-image-3-input").files[0];  // Check file input for img3

            if (image1) formData.append("imgu1", image1);
            if (image2) formData.append("imgu2", image2);  // Ensure imgu2 is added
            if (image3) formData.append("imgu3", image3);  // Ensure imgu3 is added

            // Log FormData to check if files are being appended
            for (let pair of formData.entries()) {
                console.log(pair[0] + ": " + pair[1]);
            }

            fetch('https://lightpink-dogfish-795437.hostingersite.com/admin_mis/src/php/updateArticle.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Article updated successfully!');
                    modal.classList.add("hidden");  // Close modal
                } else {
                    alert('Error: ' + data.error);
                }
            })
            .catch(error => {
                console.error('Error during fetch:', error);
                alert('An error occurred. Please try again.');
            });
        }
    }
}

// Set image preview
function setImagePreview(id, src) {
    const previewElement = document.getElementById(id);
    if (previewElement && src) {
        previewElement.style.backgroundImage = `url(${src})`;
        previewElement.style.backgroundSize = 'cover';
        previewElement.style.backgroundPosition = 'center';
        previewElement.classList.remove('hidden');
    } else {
        previewElement.classList.add('hidden');
    }
}

function setValue(id, value) {
    const element = document.getElementById(id);
    if (!element) return;

    if (element.tagName === "SELECT") {
        element.value = value || element.options[0]?.value;
    } else if (element.tagName === "TEXTAREA" || element.tagName === "INPUT") {
        element.value = value || "";
    }
}
