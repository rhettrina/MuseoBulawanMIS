//imgu1 button input js
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