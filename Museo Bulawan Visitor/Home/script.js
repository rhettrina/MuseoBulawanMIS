function collectFormData() {
    // Collecting form data
    const formData = new FormData();
    console.debug("Collecting form data...");

    // Text Inputs
    const firstName = document.getElementById('firstName').value;
    formData.append('firstName', firstName);
    console.debug("First Name: ", firstName);

    const lastName = document.getElementById('lastName').value;
    formData.append('lastName', lastName);
    console.debug("Last Name: ", lastName);

    const age = document.getElementById('age').value;
    formData.append('age', age);
    console.debug("Age: ", age);

    const sex = document.querySelector('input[name="sex"]:checked') ? document.querySelector('input[name="sex"]:checked').value : '';
    formData.append('sex', sex);
    console.debug("Sex: ", sex);

    const email = document.getElementById('email').value;
    formData.append('email', email);
    console.debug("Email: ", email);

    const phone = document.getElementById('phone').value;
    formData.append('phone', phone);
    console.debug("Phone: ", phone);

    const organization = document.getElementById('organization').value;
    formData.append('organization', organization);
    console.debug("Organization: ", organization);

    const province = document.getElementById('province').value;
    formData.append('province', province);
    console.debug("Province: ", province);

    const city = document.getElementById('city').value;
    formData.append('city', city);
    console.debug("City: ", city);

    const barangay = document.getElementById('barangay').value;
    formData.append('barangay', barangay);
    console.debug("Barangay: ", barangay);

    const street = document.getElementById('street').value;
    formData.append('street', street);
    console.debug("Street: ", street);

    const artifactTitle = document.getElementById('artifactTitle').value;
    formData.append('artifactTitle', artifactTitle);
    console.debug("Artifact Title: ", artifactTitle);

    const artifactDescription = document.getElementById('artifactDescription').value;
    formData.append('artifactDescription', artifactDescription);
    console.debug("Artifact Description: ", artifactDescription);

    const acquisition = document.getElementById('acquisition').value;
    formData.append('acquisition', acquisition);
    console.debug("Acquisition: ", acquisition);

    const additionalInfo = document.getElementById('additionalInfo').value;
    formData.append('additionalInfo', additionalInfo);
    console.debug("Additional Info: ", additionalInfo);

    const narrative = document.getElementById('narrative').value;
    formData.append('narrative', narrative);
    console.debug("Narrative: ", narrative);

    // Handling file inputs
    handleFileInput('artifact_img', 'artifactFiles', formData);
    handleFileInput('documentation_img', 'documentationFiles', formData);
    handleFileInput('related_img', 'relatedFiles', formData);

    // Handle URL image inputs and download them (with validation)
    if (validateImageInput('artifactImages', 'artifactFiles')) {
        handleImageUrl('artifactImages', formData); // For artifact images
    } else {
        alert('Please provide either a URL or a file for Artifact Images, not both.');
        return;
    }

    if (validateImageInput('documentation', 'documentationFiles')) {
        handleImageUrl('documentation', formData); // For documentation
    } else {
        alert('Please provide either a URL or a file for Documentation, not both.');
        return;
    }

    if (validateImageInput('relatedImages', 'relatedFiles')) {
        handleImageUrl('relatedImages', formData); // For related images
    } else {
        alert('Please provide either a URL or a file for Related Images, not both.');
        return;
    }

    // Submit the form using AJAX
    submitFormData(formData);
}

// Function to handle file input fields
function handleFileInput(fileInputId, fileFieldId, formData) {
    const fileInput = document.getElementById(fileFieldId);
    const files = fileInput.files;
    console.debug(`${fileFieldId} files: `, files);

    if (files.length > 0) {
        for (let i = 0; i < files.length; i++) {
            formData.append(fileInputId + "[]", files[i]);
            console.debug(`File ${i + 1}: `, files[i]);
        }
    }
}

// Function to handle URL image inputs (download and forward the image to PHP)
function handleImageUrl(inputId, formData) {
    const imageUrlInput = document.getElementById(inputId);
    const imageUrl = imageUrlInput.value;
    console.debug(`${inputId} URL: `, imageUrl);

    if (imageUrl && (imageUrl.startsWith('http://') || imageUrl.startsWith('https://'))) {
        fetch(imageUrl)
            .then(response => response.blob())
            .then(blob => {
                const file = new File([blob], 'downloaded_image.jpg', { type: blob.type });
                formData.append(inputId + "[]", file);
                console.debug(`Downloaded image for ${inputId}: `, file);
            })
            .catch(error => console.error("Error downloading image: ", error));
    }
}

// Function to validate if either a URL or file is provided, but not both
function validateImageInput(urlInputId, fileInputId) {
    const imageUrl = document.getElementById(urlInputId).value;
    const files = document.getElementById(fileInputId).files;

    console.debug(`Validating input for ${urlInputId} and ${fileInputId}: URL = `, imageUrl, " Files = ", files.length);

    // Check if both URL and file are provided
    if (imageUrl && files.length > 0) {
        console.debug("Both URL and file are provided for " + urlInputId);
        return false; // If both URL and file are provided, return false (invalid)
    }

    return true; // Otherwise, return true (valid)
}

// Function to submit form data using AJAX
function submitFormData(formData) {
    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'process_donation.php', true);

    // Show loading or processing status
    xhr.onload = function() {
        if (xhr.status === 200) {
            console.log("Form submitted successfully");
            // You can redirect or show success message here
        } else {
            console.error("Form submission failed");
        }
    };

    xhr.send(formData);
}
