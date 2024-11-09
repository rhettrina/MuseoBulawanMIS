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


function save(){

}


