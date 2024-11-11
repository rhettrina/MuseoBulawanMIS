// img1 button input JS
function img1() {
    const imgu1_input = document.getElementById("imgu1");
    const imgu1_button = document.getElementById("imgu1-button");
    const imgu1_text = document.getElementById("imgu1-text");

    imgu1_button.addEventListener("click", function() {
        imgu1_input.click();
    });

    imgu1_input.addEventListener("change", function() {
        if (imgu1_input.value) {
            imgu1_text.innerHTML = imgu1_input.value.match(/[\/\\]([\w\d\s\.\-\(\)]+)$/)[1];
        } else {
            imgu1_text.innerHTML = "No image inserted, yet";
        }
    });
}

// img2 button input JS
function img2() {
    const imgu2_input = document.getElementById("imgu2");
    const imgu2_button = document.getElementById("imgu2-button");
    const imgu2_text = document.getElementById("imgu2-text");

    imgu2_button.addEventListener("click", function() {
        imgu2_input.click();
    });

    imgu2_input.addEventListener("change", function() {
        if (imgu2_input.value) {
            imgu2_text.innerHTML = imgu2_input.value.match(/[\/\\]([\w\d\s\.\-\(\)]+)$/)[1];
        } else {
            imgu2_text.innerHTML = "No image inserted, yet";
        }
    });
}

// img3 button input JS
function img3() {
    const imgu3_input = document.getElementById("imgu3");
    const imgu3_button = document.getElementById("imgu3-button");
    const imgu3_text = document.getElementById("imgu3-text");

    imgu3_button.addEventListener("click", function() {
        imgu3_input.click();
    });

    imgu3_input.addEventListener("change", function() {
        if (imgu3_input.value) {
            imgu3_text.innerHTML = imgu3_input.value.match(/[\/\\]([\w\d\s\.\-\(\)]+)$/)[1];
        } else {
            imgu3_text.innerHTML = "No image inserted, yet";
        }
    });
}

function save() {
    // Create a new FormData object to send form data
    const formData = new FormData();

    // Get all the input data
    const articleTitle = document.getElementById("article-title").value;
    const articleType = document.getElementById("article-type").value;
    const location = document.getElementById("location").value;
    const author = document.getElementById("author").value;
    const imgu1_details = document.getElementById("imgu1-details").value; // This is the imgu1 details field
    const p1boxLeft = document.getElementById("p1box-left").value;
    const p1boxRight = document.getElementById("p1box-right").value;
    const p2box = document.getElementById("p2box").value;
    const p3box = document.getElementById("p3box").value;
    
    // Add text fields to FormData
    formData.append("article_title", articleTitle);
    formData.append("article_type", articleType);
    formData.append("location", location);
    formData.append("author", author);
    formData.append("imgu1_details", imgu1_details); // Add the imgu1-details field here
    formData.append("p1box_left", p1boxLeft);
    formData.append("p1box_right", p1boxRight);
    formData.append("p2box", p2box);
    formData.append("p3box", p3box);

    // Add image files to FormData
    const imgu1 = document.getElementById("imgu1").files[0];
    const imgu2 = document.getElementById("imgu2").files[0];
    const imgu3 = document.getElementById("imgu3").files[0];

    formData.append("imgu1", imgu1);
    formData.append("imgu2", imgu2);
    formData.append("imgu3", imgu3);

    // Log the form data
    console.log("Form data being sent:");
    for (let pair of formData.entries()) {
        console.log(pair[0]+ ': ' + pair[1]);
    }

    // Send the data using fetch
    fetch('http://localhost/MuseoBulawanMIS/Management/Article/builder_DBconnection.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        alert(data.message);
        if (data.success) {
            clear(); // Clear the form if successful
        }
    })
    .catch(error => console.error('Error:', error));
}


// Clear input fields
function clear() {
    document.getElementById("article-title").value = '';
    document.getElementById("article-type").value = 'placeholder';
    document.getElementById("location").value = '';
    document.getElementById("author").value = '';
    document.getElementById("imgu1-details").value = '';
    document.getElementById("p1box-left").value = '';
    document.getElementById("p1box-right").value = '';
    document.getElementById("p2box").value = '';
    document.getElementById("p3box").value = '';
}
