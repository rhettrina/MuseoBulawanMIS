function init(){
    //call the display functions here

}

// Drawing functions for symbols
function drawAllSymbols() {
    drawWallArtifactSymbol();
    drawArtifactSymbol();
    drawContainerSymbol();
    drawDoorSymbol();
    drawRestrictedSymbol();
    drawInformationDeskSymbol();
    drawStatueSymbol();
    drawPanelsSymbol();
    drawWallPictureSymbol();
    drawRestAreaSymbol();
}

function drawWallArtifactSymbol() {
    const canvas = document.getElementById("wall-artifact-symbol");
    const ctx = canvas.getContext("2d");
    drawWallArtifactSymbolOnCanvas(ctx, canvas.width, canvas.height);
}

function drawArtifactSymbol() {
    const canvas = document.getElementById("artifacts-artworks-symbol");
    const ctx = canvas.getContext("2d");
    drawArtifactSymbolOnCanvas(ctx, canvas.width, canvas.height);
}

function drawContainerSymbol() {
    const canvas = document.getElementById("container-symbol");
    const ctx = canvas.getContext("2d");
    drawContainerSymbolOnCanvas(ctx, canvas.width, canvas.height);
}

function drawDoorSymbol() {
    const canvas = document.getElementById("door-symbol");
    const ctx = canvas.getContext("2d");
    drawDoorSymbolOnCanvas(ctx, canvas.width, canvas.height);
}

function drawRestrictedSymbol() {
    const canvas = document.getElementById("restricted-symbol");
    const ctx = canvas.getContext("2d");
    drawRestrictedSymbolOnCanvas(ctx, canvas.width, canvas.height);
}

function drawInformationDeskSymbol() {
    const canvas = document.getElementById("information-desk-symbol");
    const ctx = canvas.getContext("2d");
    drawInformationDeskSymbolOnCanvas(ctx, canvas.width, canvas.height);
}

function drawStatueSymbol() {
    const canvas = document.getElementById("statue-symbol");
    const ctx = canvas.getContext("2d");
    drawStatueSymbolOnCanvas(ctx, canvas.width, canvas.height);
}

function drawPanelsSymbol() {
    const canvas = document.getElementById("panels-symbol");
    const ctx = canvas.getContext("2d");
    drawPanelsSymbolOnCanvas(ctx, canvas.width, canvas.height);
}

function drawWallPictureSymbol() {
    const canvas = document.getElementById("wall-picture-symbol");
    const ctx = canvas.getContext("2d");
    drawWallPictureSymbolOnCanvas(ctx, canvas.width, canvas.height);
}

function drawRestAreaSymbol() {
    const canvas = document.getElementById("rest-area-symbol");
    const ctx = canvas.getContext("2d");
    drawRestAreaSymbolOnCanvas(ctx, canvas.width, canvas.height);
}

// Symbol drawing implementations
function drawSymbolByIdOnCanvas(id, ctx, width, height) {
    switch (id) {
        case "wall-artifact":
            drawWallArtifactSymbolOnCanvas(ctx, width, height);
            break;
        case "artifacts-artworks":
            drawArtifactSymbolOnCanvas(ctx, width, height);
            break;
        case "container":
            drawContainerSymbolOnCanvas(ctx, width, height);
            break;
        case "door":
            drawDoorSymbolOnCanvas(ctx, width, height);
            break;
        case "restricted":
            drawRestrictedSymbolOnCanvas(ctx, width, height);
            break;
        case "information-desk":
            drawInformationDeskSymbolOnCanvas(ctx, width, height);
            break;
        case "statue":
            drawStatueSymbolOnCanvas(ctx, width, height);
            break;
        case "panels":
            drawPanelsSymbolOnCanvas(ctx, width, height);
            break;
        case "wall-picture":
            drawWallPictureSymbolOnCanvas(ctx, width, height);
            break;
        case "rest-area":
            drawRestAreaSymbolOnCanvas(ctx, width, height);
            break;
        default:
            break;
    }
}

function drawWallArtifactSymbolOnCanvas(ctx, width, height) {
    ctx.imageSmoothingEnabled = false; // Disable image smoothing
    ctx.clearRect(0, 0, width, height);

    // Set the fill style for the rectangle
    ctx.fillStyle = "#8B4513"; // Brown color similar to the SVG

    // Draw the rectangle
    const rectX = width * 0.25; // 25% of the canvas width
    const rectY = height * 0.4167; // 25px from the top in a 60px height canvas
    const rectWidth = width * 0.5; // 50% of the canvas width
    const rectHeight = height * 0.1667; // 10px in a 60px height canvas
    ctx.fillRect(rectX, rectY, rectWidth, rectHeight);

    // Draw the rectangle border
    ctx.strokeStyle = "#000"; // Black border
    ctx.lineWidth = 1;
    ctx.strokeRect(rectX, rectY, rectWidth, rectHeight);

    // Draw the curly line
    ctx.beginPath();
    ctx.moveTo(width * 0.25, height * 0.5); // Start at (15, 30)
    ctx.bezierCurveTo(width * 0.3333, height * 0.25, width * 0.6667, height * 0.75, width * 0.75, height * 0.5); // Control points and end point
    ctx.stroke();
}

function drawArtifactSymbolOnCanvas(ctx, width, height) {
    ctx.fillStyle = "#d2b48c";
    ctx.clearRect(0, 0, width, height);
    ctx.beginPath();
    ctx.arc(width / 2, height / 2, Math.min(width, height) * 0.4, 0, Math.PI * 2);
    ctx.fill();
}

function drawContainerSymbolOnCanvas(ctx, width, height) {
    ctx.strokeStyle = "#8b5a2b";
    ctx.lineWidth = Math.min(width, height) * 0.05;
    ctx.clearRect(0, 0, width, height);
    ctx.strokeRect(width * 0.1, height * 0.1, width * 0.8, height * 0.8);
}

function drawDoorSymbolOnCanvas(ctx, width, height) {
    ctx.clearRect(0, 0, width, height);
    ctx.fillStyle = "#8b5a2b";
    ctx.beginPath();
    ctx.moveTo(width * 0.2, height * 0.8);
    ctx.quadraticCurveTo(width * 0.5, height * 0.2, width * 0.8, height * 0.8);
    ctx.closePath();
    ctx.fill();
    ctx.strokeStyle = "#000";
    ctx.lineWidth = 2;
    ctx.stroke();
}

function drawRestrictedSymbolOnCanvas(ctx, width, height) {
    ctx.fillStyle = "#444";
    ctx.clearRect(0, 0, width, height);
    ctx.fillRect(0, 0, width, height);
    ctx.strokeStyle = "#fff";
    ctx.lineWidth = Math.min(width, height) * 0.05;
    ctx.beginPath();
    ctx.moveTo(width * 0.2, height * 0.2);
    ctx.lineTo(width * 0.8, height * 0.8);
    ctx.moveTo(width * 0.8, height * 0.2);
    ctx.lineTo(width * 0.2, height * 0.8);
    ctx.stroke();
}

function drawInformationDeskSymbolOnCanvas(ctx, width, height) {
    ctx.clearRect(0, 0, width, height); // Clear the canvas

    // Draw the rectangle (desk base)
    ctx.fillStyle = "#4682B4"; // Steel Blue color
    const rectX = width * 0.25; // 15px in a 60px width canvas
    const rectY = height * 0.4167; // 25px from the top in a 60px height canvas
    const rectWidth = width * 0.5; // 30px in a 60px width canvas
    const rectHeight = height * 0.1667; // 10px in a 60px height canvas
    ctx.fillRect(rectX, rectY, rectWidth, rectHeight);
    ctx.strokeStyle = "#000"; // Black stroke
    ctx.lineWidth = 2;
    ctx.strokeRect(rectX, rectY, rectWidth, rectHeight);

    // Draw the circle (information symbol)
    ctx.fillStyle = "#FFFFFF"; // White color for the circle
    ctx.beginPath();
    const circleRadius = Math.min(width, height) * 0.0833; // 5px in a 60px size canvas
    ctx.arc(
        width / 2,                // Center x
        height * 0.4167,          // Center y (25px from the top in a 60px height canvas)
        circleRadius,
        0,
        Math.PI * 2
    );
    ctx.fill();
    ctx.strokeStyle = "#000"; // Black stroke for the circle
    ctx.lineWidth = 2;
    ctx.stroke();
}

function drawStatueSymbolOnCanvas(ctx, width, height) {
    ctx.clearRect(0, 0, width, height); // Clear the canvas

    // Set the fill style for both the circle and the rectangle
    ctx.fillStyle = "#708090"; // Slate Gray color

    // Draw the circle (head of the statue)
    ctx.beginPath();
    const circleRadius = Math.min(width, height) * 0.1667; // 10px in a 60px size canvas
    ctx.arc(
        width / 2,                // Center x
        height * 0.25,            // Center y (15px from the top in a 60px height canvas)
        circleRadius,             // Radius
        0,
        Math.PI * 2
    );
    ctx.fill();
    ctx.strokeStyle = "#000"; // Black stroke
    ctx.lineWidth = 2;
    ctx.stroke();

    // Draw the rectangle (body of the statue)
    const rectX = width * 0.4167;  // 25px in a 60px width canvas
    const rectY = height * 0.4167; // 25px from the top in a 60px height canvas
    const rectWidth = width * 0.1667; // 10px in a 60px width canvas
    const rectHeight = height * 0.3333; // 20px in a 60px height canvas
    ctx.fillRect(rectX, rectY, rectWidth, rectHeight);
    ctx.strokeRect(rectX, rectY, rectWidth, rectHeight);

}

function drawPanelsSymbolOnCanvas(ctx, width, height) {
    ctx.fillStyle = "#D3D3D3";
    ctx.clearRect(0, 0, width, height);
    ctx.fillRect(width * 0.1, height * 0.4, width * 0.8, height * 0.2);
    ctx.strokeStyle = "#000";
    ctx.lineWidth = 1;
    ctx.strokeRect(width * 0.1, height * 0.4, width * 0.8, height * 0.2);
}

function drawWallPictureSymbolOnCanvas(ctx, width, height) {
    ctx.fillStyle = "#000";
    ctx.clearRect(0, 0, width, height);
    ctx.fillRect(width * 0.1, height * 0.4, width * 0.8, height * 0.2);
    ctx.strokeStyle = "#FFFFFF";
    ctx.lineWidth = 1;
    ctx.beginPath();
    ctx.moveTo(width * 0.1, height * 0.5);
    ctx.lineTo(width * 0.9, height * 0.5);
    ctx.stroke();
}

function drawRestAreaSymbolOnCanvas(ctx, width, height) {
    ctx.clearRect(0, 0, width, height); // Clear the canvas

    // Draw the rectangle (rest area base)
    ctx.fillStyle = "#32CD32"; // Lime Green color
    const rectX = width * 0.25; // 15px in a 60px width canvas
    const rectY = height * 0.4167; // 25px from the top in a 60px height canvas
    const rectWidth = width * 0.5; // 30px in a 60px width canvas
    const rectHeight = height * 0.1667; // 10px in a 60px height canvas
    ctx.fillRect(rectX, rectY, rectWidth, rectHeight);
    ctx.strokeStyle = "#000"; // Black stroke
    ctx.lineWidth = 2;
    ctx.strokeRect(rectX, rectY, rectWidth, rectHeight);

    // Draw the left circle
    ctx.fillStyle = "#FFFFFF"; // White color for circles
    ctx.beginPath();
    const circleRadius = Math.min(width, height) * 0.05; // 3px in a 60px size canvas
    ctx.arc(
        width * 0.3667, // 22px in a 60px width canvas
        height * 0.4167, // 25px from the top in a 60px height canvas
        circleRadius,
        0,
        Math.PI * 2
    );
    ctx.fill();
    ctx.strokeStyle = "#000"; // Black stroke for circles
    ctx.lineWidth = 1;
    ctx.stroke();

    // Draw the right circle
    ctx.beginPath();
    ctx.arc(
        width * 0.6333, // 38px in a 60px width canvas
        height * 0.4167, // 25px from the top in a 60px height canvas
        circleRadius,
        0,
        Math.PI * 2
    );
    ctx.fill();
    ctx.stroke();

}
