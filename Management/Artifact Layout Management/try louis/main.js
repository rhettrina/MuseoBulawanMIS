// main.js

// Main JavaScript functionality
document.addEventListener("DOMContentLoaded", () => {
  // Symbol drawing functions are assumed to be loaded from drawing.js
  drawAllSymbols();

  const buttons = document.querySelectorAll(".toolbar button");
  const undoButton = document.getElementById("undo-button");
  const redoButton = document.getElementById("redo-button");
  const deleteButton = document.getElementById("delete-button");
  const textButton = document.getElementById("text-button");
  const propertiesPanel = document.getElementById("properties-panel");

  buttons.forEach((button) => {
    button.addEventListener("click", () => {
      buttons.forEach((btn) => btn.classList.remove("active"));
      if (button !== deleteButton) {
        button.classList.add("active");
      }
    });
  });

  const objects = document.querySelectorAll(".object");
  objects.forEach((object) => {
    object.addEventListener("dragstart", (e) => {
      e.dataTransfer.setData("text/plain", object.id);
    });
  });

  const canvasArea = document.getElementById("canvas-area");
  let isTextMode = false;
  let undoStack = [];
  let redoStack = [];

  canvasArea.addEventListener("dragover", (e) => {
    e.preventDefault();
  });

  canvasArea.addEventListener("drop", (e) => {
    e.preventDefault();
    const id = e.dataTransfer.getData("text/plain");
    const rect = canvasArea.getBoundingClientRect();
    const x = e.clientX - rect.left + canvasArea.scrollLeft;
    const y = e.clientY - rect.top + canvasArea.scrollTop;
    createSymbol(id, x, y);
    saveState();
  });

  function createSymbol(id, x, y) {
    const container = document.createElement("div");
    container.classList.add("symbol-container", "selectable");
    container.style.left = x + "px";
    container.style.top = y + "px";
    container.style.width = "40px";
    container.style.height = "40px";
    container.dataset.locked = "false";
    container.dataset.rotation = "0";

    const canvas = document.createElement("canvas");
    canvas.width = parseInt(container.style.width);
    canvas.height = parseInt(container.style.height);
    canvas.classList.add("symbol");

    const ctx = canvas.getContext("2d");
    drawSymbolByIdOnCanvas(id, ctx, canvas.width, canvas.height);

    container.setAttribute("data-symbol-id", id);
    container.appendChild(canvas);

    // Add resize handles (sides and corners) for objects
    const directions = ["n", "e", "s", "w", "ne", "nw", "se", "sw"];
    directions.forEach((dir) => {
      const handle = document.createElement("div");
      handle.classList.add("resize-handle", dir);
      container.appendChild(handle);
    });

    canvasArea.appendChild(container);
    initializeInteractJS(container);
  }

  function updateTransform(element) {
    const x = parseFloat(element.dataset.x) || 0;
    const y = parseFloat(element.dataset.y) || 0;
    const rotation = parseFloat(element.dataset.rotation) || 0;
    element.style.transform = `translate(${x}px, ${y}px) rotate(${rotation}deg)`;
  }

  // In main.js

function initializeInteractJS(element) {
  const isTextContainer = element.classList.contains("text-container");

  // Remove any existing InteractJS interactions to prevent duplicates
  interact(element).unset();

  const draggableOptions = {
    inertia: false,
    autoScroll: true,
    modifiers: [
      interact.modifiers.restrictRect({
        restriction: canvasArea,
        endOnly: false,
      }),
    ],
    listeners: {
      // Add the 'dragging' class when dragging starts
      start(event) {
        element.classList.add('dragging');
      },
      move: dragMoveListener,
      // Remove the 'dragging' class when dragging ends
      end(event) {
        element.classList.remove('dragging');
        saveState();
      },
    },
    ignoreFrom: '.resize-handle', // Ignore dragging from resize handles
    allowFrom: null, // Allow dragging from anywhere in the container
    enabled: !isElementLocked(element),
  };

  // Resizable options
  const resizableOptions = isTextContainer
    ? {
        edges: { bottom: ".resize-handle.se", right: ".resize-handle.se" },
        listeners: {
          move: resizeMoveListener,
          end(event) {
            saveState();
          },
        },
        modifiers: [
          interact.modifiers.restrictEdges({
            outer: canvasArea,
            endOnly: true,
          }),
          interact.modifiers.restrictSize({
            min: { width: 20, height: 20 },
            max: { width: 200, height: 200 },
          }),
        ],
        allowFrom: ".resize-handle.se",
        enabled: !isElementLocked(element),
      }
    : {
        edges: { top: true, left: true, bottom: true, right: true },
        listeners: {
          move: resizeMoveListener,
          end(event) {
            saveState();
          },
        },
        modifiers: [
          interact.modifiers.restrictEdges({
            outer: canvasArea,
            endOnly: true,
          }),
          interact.modifiers.restrictSize({
            min: { width: 20, height: 20 },
            max: { width: 800, height: 800 },
          }),
        ],
        allowFrom: ".resize-handle",
        enabled: !isElementLocked(element),
      };

  // Enable interactions
  interact(element).draggable(draggableOptions).resizable(resizableOptions);

  // Ensure the element can be clicked even when locked
  element.style.pointerEvents = "auto";

  element.addEventListener("mousedown", (e) => {
    e.stopPropagation();
    document
      .querySelectorAll(".symbol-container.selected, .text-container.selected")
      .forEach((el) => el.classList.remove("selected"));
    element.classList.add("selected");
    showPropertiesPanel(element);
  });
}

  function dragMoveListener(event) {
    var target = event.target;
    if (isElementLocked(target)) return;

    var x = (parseFloat(target.dataset.x) || 0) + event.dx;
    var y = (parseFloat(target.dataset.y) || 0) + event.dy;

    target.dataset.x = x;
    target.dataset.y = y;

    updateTransform(target);
  }

  function resizeMoveListener(event) {
    var target = event.target;
    if (isElementLocked(target)) return;

    // Calculate new width and height
    var newWidth = event.rect.width;
    var newHeight = event.rect.height;

    // Get minimum sizes from restrictSize modifier
    var minWidth = 10; // Replace with your minimum width
    var minHeight = 10; // Replace with your minimum height

    // Enforce minimum size
    if (newWidth < minWidth) {
      newWidth = minWidth;
    }
    if (newHeight < minHeight) {
      newHeight = minHeight;
    }

    // Update the element's style
    target.style.width = newWidth + "px";
    target.style.height = newHeight + "px";

    var angle = (parseFloat(target.dataset.rotation) || 0) * (Math.PI / 180);

    var deltaLeft = event.deltaRect.left;
    var deltaTop = event.deltaRect.top;

    var x = parseFloat(target.dataset.x) || 0;
    var y = parseFloat(target.dataset.y) || 0;

    var cos = Math.cos(angle);
    var sin = Math.sin(angle);

    x += deltaLeft * cos - deltaTop * sin;
    y += deltaLeft * sin + deltaTop * cos;

    target.dataset.x = x;
    target.dataset.y = y;

    updateTransform(target);

    // If it's a symbol container, update the canvas size
    const canvas = target.querySelector("canvas");
    if (canvas) {
      canvas.width = newWidth;
      canvas.height = newHeight;
      canvas.style.width = "100%";
      canvas.style.height = "100%";
      const ctx = canvas.getContext("2d");
      drawSymbolByIdOnCanvas(
        target.getAttribute("data-symbol-id"),
        ctx,
        canvas.width,
        canvas.height
      );
    }

    // If it's a text container, adjust the font size
    const textSpan = target.querySelector("span");
    if (textSpan) {
      // Adjust font size based on height
      const fontSize = newHeight * 0.8; // Adjust this factor as needed
      textSpan.style.fontSize = fontSize + "px";
      updateTextContainerSize(target, textSpan);
    }

    updatePropertiesPanel(target);
  }

  function isElementLocked(element) {
    return element.dataset.locked === "true";
  }

  document.addEventListener("keydown", (e) => {
    if (e.key === "Delete") {
      deleteSelectedElement();
    }
  });

  deleteButton.addEventListener("click", () => {
    deleteSelectedElement();
  });

  function deleteSelectedElement() {
    const selectedElement = document.querySelector(
      ".symbol-container.selected, .text-container.selected"
    );
    if (selectedElement) {
      selectedElement.remove();
      clearPropertiesPanel();
      saveState();
    }
  }

  canvasArea.addEventListener("mousedown", (e) => {
    if (
      !e.target.closest(
        ".symbol-container.selected, .text-container.selected"
      ) &&
      !e.target.classList.contains("selected")
    ) {
      document
        .querySelectorAll(
          ".symbol-container.selected, .text-container.selected"
        )
        .forEach((el) => el.classList.remove("selected"));
      clearPropertiesPanel();
    }
  });

  function showPropertiesPanel(element) {
    const objectProperties = document.getElementById("object-properties");
    const textProperties = document.getElementById("text-properties");
    const noSelection = document.getElementById("no-selection");

    noSelection.style.display = "none";

    if (element.classList.contains("symbol-container")) {
      // Show object properties, hide text properties
      objectProperties.style.display = "block";
      textProperties.style.display = "none";

      const widthInput = document.getElementById("width-input");
      const heightInput = document.getElementById("height-input");
      const rotationInput = document.getElementById("rotation-input");
      const widthValue = document.getElementById("width-value");
      const heightValue = document.getElementById("height-value");
      const rotationValue = document.getElementById("rotation-value");
      const lockButton = document.getElementById("lock-button");

      // Initialize values
      widthInput.value = parseInt(element.style.width);
      heightInput.value = parseInt(element.style.height);
      rotationInput.value =
        Math.round(parseFloat(element.dataset.rotation) || 0) % 360;
      widthValue.textContent = widthInput.value;
      heightValue.textContent = heightInput.value;
      rotationValue.textContent = `${rotationInput.value}°`;

      // Update lock button
      lockButton.innerHTML = `<span class="material-icons">${
        isElementLocked(element) ? "lock" : "lock_open"
      }</span> ${isElementLocked(element) ? "Lock" : "Unlock"}`;

      // Event listeners
      widthInput.oninput = () => {
        if (isElementLocked(element)) return;
        const width = parseInt(widthInput.value);
        widthValue.textContent = width;
        element.style.width = width + "px";
        const canvas = element.querySelector("canvas");
        if (canvas) {
          canvas.width = width;
          canvas.style.width = "100%";
          const ctx = canvas.getContext("2d");
          drawSymbolByIdOnCanvas(
            element.getAttribute("data-symbol-id"),
            ctx,
            canvas.width,
            canvas.height
          );
        }
        saveState();
      };

      heightInput.oninput = () => {
        if (isElementLocked(element)) return;
        const height = parseInt(heightInput.value);
        heightValue.textContent = height;
        element.style.height = height + "px";
        const canvas = element.querySelector("canvas");
        if (canvas) {
          canvas.height = height;
          canvas.style.height = "100%";
          const ctx = canvas.getContext("2d");
          drawSymbolByIdOnCanvas(
            element.getAttribute("data-symbol-id"),
            ctx,
            canvas.width,
            canvas.height
          );
        }
        saveState();
      };

      rotationInput.oninput = () => {
        if (isElementLocked(element)) return;
        const rotation = parseInt(rotationInput.value);
        rotationValue.textContent = `${rotation}°`;
        element.dataset.rotation = rotation;
        updateTransform(element);
        saveState();
      };

      lockButton.onclick = () => {
        const isLocked = isElementLocked(element);
        element.dataset.locked = (!isLocked).toString();
        lockButton.innerHTML = `<span class="material-icons">${
          !isLocked ? "lock" : "lock_open"
        }</span> ${!isLocked ? "Lock" : "Unlock"}`;

        // Update InteractJS
        interact(element).draggable({ enabled: !isElementLocked(element) });
        interact(element).resizable({ enabled: !isElementLocked(element) });

        saveState();
      };
    } else if (element.classList.contains("text-container")) {
      // Show text properties, hide object properties
      objectProperties.style.display = "none";
      textProperties.style.display = "block";

      const textContentInput = document.getElementById("text-content-input");
      const textColorInput = document.getElementById("text-color-input");
      const textSizeInput = document.getElementById("text-size-input");
      const textSizeValue = document.getElementById("text-size-value");
      const textRotationInput = document.getElementById("text-rotation-input");
      const textRotationValue = document.getElementById("text-rotation-value");
      const boldButton = document.getElementById("bold-button");
      const italicButton = document.getElementById("italic-button");
      const underlineButton = document.getElementById("underline-button");
      const lockButtonText = document.getElementById("lock-button-text");

      const textSpan = element.querySelector("span");

      // Initialize values
      textContentInput.value = textSpan.textContent;
      textColorInput.value = rgbToHex(window.getComputedStyle(textSpan).color);
      const fontSize = parseFloat(window.getComputedStyle(textSpan).fontSize);
      textSizeInput.value = fontSize;
      textSizeValue.textContent = fontSize;
      const textRotation =
        Math.round(parseFloat(element.dataset.rotation) || 0) % 360;
      textRotationInput.value = textRotation;
      textRotationValue.textContent = `${textRotation}°`;

      // Update text style buttons
      boldButton.classList.toggle(
        "active",
        textSpan.style.fontWeight === "bold"
      );
      italicButton.classList.toggle(
        "active",
        textSpan.style.fontStyle === "italic"
      );
      underlineButton.classList.toggle(
        "active",
        textSpan.style.textDecoration === "underline"
      );

      // Update lock button
      lockButtonText.innerHTML = `<span class="material-icons">${
        isElementLocked(element) ? "lock" : "lock_open"
      }</span> ${isElementLocked(element) ? "Lock" : "Unlock"}`;

      // Event listeners
      textContentInput.oninput = () => {
        if (isElementLocked(element)) return;
        textSpan.textContent = textContentInput.value || " ";
        updateTextContainerSize(element, textSpan);
        saveState();
      };

      textColorInput.oninput = () => {
        if (isElementLocked(element)) return;
        textSpan.style.color = textColorInput.value;
        saveState();
      };

      textSizeInput.oninput = () => {
        if (isElementLocked(element)) return;
        const size = parseInt(textSizeInput.value);
        textSizeValue.textContent = size;
        textSpan.style.fontSize = size + "px";
        updateTextContainerSize(element, textSpan);
        saveState();
      };

      textRotationInput.oninput = () => {
        if (isElementLocked(element)) return;
        const rotation = parseInt(textRotationInput.value);
        textRotationValue.textContent = `${rotation}°`;
        element.dataset.rotation = rotation;
        updateTransform(element);
        saveState();
      };

      boldButton.onclick = () => {
        if (isElementLocked(element)) return;
        const isBold = textSpan.style.fontWeight === "bold";
        textSpan.style.fontWeight = isBold ? "normal" : "bold";
        boldButton.classList.toggle("active", !isBold);
        updateTextContainerSize(element, textSpan);
        saveState();
      };

      italicButton.onclick = () => {
        if (isElementLocked(element)) return;
        const isItalic = textSpan.style.fontStyle === "italic";
        textSpan.style.fontStyle = isItalic ? "normal" : "italic";
        italicButton.classList.toggle("active", !isItalic);
        updateTextContainerSize(element, textSpan);
        saveState();
      };

      underlineButton.onclick = () => {
        if (isElementLocked(element)) return;
        const isUnderlined = textSpan.style.textDecoration === "underline";
        textSpan.style.textDecoration = isUnderlined ? "none" : "underline";
        underlineButton.classList.toggle("active", !isUnderlined);
        updateTextContainerSize(element, textSpan);
        saveState();
      };

      lockButtonText.onclick = () => {
        const isLocked = isElementLocked(element);
        element.dataset.locked = (!isLocked).toString();
        lockButtonText.innerHTML = `<span class="material-icons">${
          !isLocked ? "lock" : "lock_open"
        }</span> ${!isLocked ? "Lock" : "Unlock"}`;

        // Update InteractJS
        interact(element).draggable({ enabled: !isElementLocked(element) });
        interact(element).resizable({ enabled: !isElementLocked(element) });

        saveState();
      };
    }
  }

  function updateTextContainerSize(container, textSpan) {
    // Update the container size to match the text content
    container.style.width = textSpan.scrollWidth + "px";
    container.style.height = textSpan.scrollHeight + "px";
  }

  function updatePropertiesPanel(element) {
    if (
      element.classList.contains("symbol-container") ||
      element.classList.contains("text-container")
    ) {
      showPropertiesPanel(element);
    }
  }

  function clearPropertiesPanel() {
    const objectProperties = document.getElementById("object-properties");
    const textProperties = document.getElementById("text-properties");
    const noSelection = document.getElementById("no-selection");

    objectProperties.style.display = "none";
    textProperties.style.display = "none";
    noSelection.style.display = "block";
  }

  function rgbToHex(rgb) {
    const result = /^rgb\((\d+),\s*(\d+),\s*(\d+)\)$/.exec(rgb);
    if (!result) {
      return "#000000";
    }
    const r = parseInt(result[1]).toString(16).padStart(2, "0");
    const g = parseInt(result[2]).toString(16).padStart(2, "0");
    const b = parseInt(result[3]).toString(16).padStart(2, "0");
    return "#" + r + g + b;
  }

  canvasArea.addEventListener("click", (e) => {
    if (isTextMode && !e.target.closest(".text-container")) {
      const rect = canvasArea.getBoundingClientRect();
      const x = e.clientX - rect.left + canvasArea.scrollLeft;
      const y = e.clientY - rect.top + canvasArea.scrollTop;

      const input = document.createElement("input");
      input.type = "text";
      input.style.position = "absolute";
      input.style.left = e.clientX + "px";
      input.style.top = e.clientY + "px";
      input.style.fontSize = "16px";
      input.style.border = "1px dashed #000";
      input.style.background = "transparent";
      input.style.padding = "2px";
      input.style.zIndex = 1000;

      document.body.appendChild(input);
      input.focus();

      input.addEventListener("blur", () => {
        if (input.value.trim() !== "") {
          createTextElement(input.value.trim(), x, y);
          saveState();
        }
        input.remove();
      });

      input.addEventListener("keydown", (e) => {
        if (e.key === "Enter") {
          input.blur();
        }
      });
    }
  });

  function createTextElement(text, x, y) {
    const container = document.createElement("div");
    container.classList.add("text-container");
    container.style.left = x + "px";
    container.style.top = y + "px";
    container.dataset.locked = "false";
    container.dataset.rotation = "0";

    const textSpan = document.createElement("span");
    textSpan.textContent = text;
    textSpan.style.fontSize = "16px";
    textSpan.style.color = "#000000"; // Default text color
    container.appendChild(textSpan);

    // Set initial container size
    container.style.width = "auto";
    container.style.height = "20px";

    // Add only the bottom-right resize handle
    const handle = document.createElement("div");
    handle.classList.add("resize-handle", "se");
    container.appendChild(handle);

    canvasArea.appendChild(container);
    initializeInteractJS(container);
  }

  // Undo and Redo functionality
  function saveState() {
    undoStack.push(canvasArea.innerHTML);
    redoStack = [];
  }

  function restoreState(state) {
    canvasArea.innerHTML = state;
    const elements = canvasArea.querySelectorAll(
      ".symbol-container, .text-container"
    );
    elements.forEach((element) => {
      initializeInteractJS(element);
    });
  }

  undoButton.addEventListener("click", () => {
    if (undoStack.length > 1) {
      redoStack.push(undoStack.pop());
      const previousState = undoStack[undoStack.length - 1];
      restoreState(previousState);
    }
  });

  redoButton.addEventListener("click", () => {
    if (redoStack.length > 0) {
      const nextState = redoStack.pop();
      undoStack.push(nextState);
      restoreState(nextState);
    }
  });

  textButton.addEventListener("click", () => {
    isTextMode = !isTextMode;
    textButton.classList.toggle("active", isTextMode);
    canvasArea.style.cursor = isTextMode ? "text" : "default";
  });

  // Initialize the undo stack with the initial state
  saveState();
});
