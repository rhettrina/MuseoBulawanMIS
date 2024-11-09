//imgu1 button input js


function img1(){
    const imgu1_input = document.getElementById("imgu1")
    const imgu1_button = document.getElementById("imgu1-button")
    const imgu1_text = document.getElementById("imgu1-text")

    imgu1_button.addEventListener("click", function() {
        imgu1_input.click();
    });

    imgu1_input.addEventListener("change", function() {
        if(imgu1_input.value){
            imgu1_text.innerHTML = imgu1_input.value.match( /[\/\\]([\w\d\s\.\-\(\)]+)$/)[1];
        }
        else{
            imgu1_text.innerHTML = "No image inserted, yet";
        }
    });

}

function img2(){
    const imgu2_input = document.getElementById("imgu2")
    const imgu2_button = document.getElementById("imgu2-button")
    const imgu2_text = document.getElementById("imgu2-text")

    imgu2_button.addEventListener("click", function() {
        imgu2_input.click();
    });

    imgu2_input.addEventListener("change", function() {
        if(imgu2_input.value){
            imgu2_text.innerHTML = imgu2_input.value.match( /[\/\\]([\w\d\s\.\-\(\)]+)$/)[1];
        }
        else{
            imgu2_text.innerHTML = "No image inserted, yet";
        }
    });

}

function img3(){
    const imgu3_input = document.getElementById("imgu3")
    const imgu3_button = document.getElementById("imgu3-button")
    const imgu3_text = document.getElementById("imgu3-text")

    imgu3_button.addEventListener("click", function() {
        imgu3_input.click();
    });

    imgu3_input.addEventListener("change", function() {
        if(imgu3_input.value){
            imgu3_text.innerHTML = imgu3_input.value.match( /[\/\\]([\w\d\s\.\-\(\)]+)$/)[1];
        }
        else{
            imgu3_text.innerHTML = "No image inserted, yet";
        }
    });

}


function save() {
    // Collect form data
    const title = document.getElementById('article-title').value;
    const articleType = document.getElementById('article-type').value;
    const location = document.getElementById('location').value;
    const author = document.getElementById('author').value;
    const p1Left = document.getElementById('p1box-left').value;
    const p1Right = document.getElementById('p1box-right').value;
    const p2 = document.getElementById('p2box').value;
    const p3 = document.getElementById('p3box').value;
    const img1Details = document.getElementById('imgu1-details').value;

    // Collect file inputs
    const img1 = document.getElementById('imgu1').files[0];
    const img2 = document.getElementById('imgu2').files[0];
    const img3 = document.getElementById('imgu3').files[0];

    // FormData object to hold all data
    const formData = new FormData();
    formData.append('title', title);
    formData.append('articleType', articleType);
    formData.append('location', location);
    formData.append('author', author);
    formData.append('p1Left', p1Left);
    formData.append('p1Right', p1Right);
    formData.append('p2', p2);
    formData.append('p3', p3);
    formData.append('img1Details', img1Details);

    // Append images if they exist
    if (img1) formData.append('img1', img1);
    if (img2) formData.append('img2', img2);
    if (img3) formData.append('img3', img3);

    // Send data to the PHP script using POST method
    fetch('Management/Article/save2db.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(result => {
        console.log('Save successful:', result);
        alert('Article saved successfully!');
    })
    .catch(error => {
        console.error('Error saving article:', error);
        alert('Failed to save article.');
    });
}
