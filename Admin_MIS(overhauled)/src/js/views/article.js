document.addEventListener("DOMContentLoaded", () => {
  console.log("Article view loaded!");

  getData();

  
});
getData(sortBy = "created_at", sortOrder = "desc");

async function getData(sortBy = "created_at", sortOrder = "desc") {
  const url = `http://localhost/src/api/fetchArticles.php?sort_by=${sortBy}&sort_order=${sortOrder}`;
  try {
      const response = await fetch(url);
      if (!response.ok) {
          throw new Error(`Response status: ${response.status}`);
      }

      const articles = await response.json();

      if (articles.success) {
          // Display the total count of articles
          document.getElementById('total-articles').innerText = articles.total_count;

          // Iterate through the articles array safely
          if (Array.isArray(articles.data)) {
              articles.data.forEach(article => {
                  if (article.updated_date === null) {
                      article.updated_date = "Not updated";
                  }
              });

              // Populate the articles
              populateArticles(articles.data);
          } else {
              console.error("Data is not an array.");
          }

      } else {
          console.error(articles.message || "Failed to fetch articles.");
      }
  } catch (error) {
      console.error("Error fetching data:", error.message);
  }
}

  



    function populateArticles(articleData) {
        const container = document.querySelector(".article-table");
    
        container.innerHTML = '';
    
        articleData.forEach(article => {



            const contextContainer = document.createElement("div");
            const articleDiv = document.createElement("div");
            articleDiv.className = "w-full max-h-10 bg-white border border-black border-[1px] rounded-md flex items-center hover:bg-orange-200 hover:border-orange-300 cursor-pointer mb-2 min-w-[300px]";


            contextContainer.setAttribute("class", `class${article.id}`);

            articleDiv.setAttribute("id", `article_${article.id}`);
            


            const titleDiv = document.createElement("div");
            titleDiv.className = "flex basis-[20%] py-2 justify-center items-center text-center";
            titleDiv.setAttribute("onClick", `handleEvent('preview' ,${article.id})`);
            const titleP = document.createElement("p");
            titleP.className = "truncate h-6 w-[150px]";
            titleP.textContent = article.article_title; 
            


            const authorDiv = document.createElement("div");
            authorDiv.className = "flex basis-[20%] py-2 justify-center items-center text-center";
            authorDiv.setAttribute("onClick", `handleEvent('preview' ,${article.id})`);
            const authorP = document.createElement("p");
            authorP.className = "truncate h-6 w-[150px]";
            authorP.textContent = article.author;

            const typeDiv = document.createElement("div");
            typeDiv.className = "flex basis-[20%] py-2 justify-center items-center text-center";
            typeDiv.setAttribute("onClick", `handleEvent('preview' ,${article.id})`);
            const typeP = document.createElement("p");
            typeP.className = "truncate h-6 w-[150px]";
            typeP.textContent = article.article_type;          
            
            const createdDiv = document.createElement("div");
            createdDiv.className = "flex basis-[20%] py-2 justify-center items-center text-center";
            createdDiv.setAttribute("onClick", `handleEvent('preview' ,${article.id})`);
            const createdP = document.createElement("p");
            createdP.className = "truncate h-6 w-[150px]";
            createdP.textContent = article.created_at;

            const updatedDiv = document.createElement("div");
            updatedDiv.className = "flex basis-[20%] py-2 justify-center items-center text-center justify-between";
            const space = document.createElement("div");
            space.className = "px-4";
            const updateP = document.createElement("p");
            updateP.className = "truncate h-6 w-[150px]";
            updateP.textContent = article.updated_date;
            updateP.setAttribute("onClick", `handleEvent('preview' ,${article.id})`);
            const contextB = document.createElement("div");
            contextB.className = "px-4 hover:bg-orange-200 hover:border-orange-300 hover:border-[1px] rounded-lg cursor-pointer";
            contextB.setAttribute("onClick", `contextModal(${article.id})`);
            const contentI = document.createElement("i");
            contentI.className = "fa-solid fa-ellipsis-vertical";
            
       
            
            articleDiv.appendChild(titleDiv);
            titleDiv.appendChild(titleP);

            articleDiv.appendChild(authorDiv);
            authorDiv.appendChild(authorP);

            articleDiv.appendChild(typeDiv);
            typeDiv.appendChild(typeP);

            articleDiv.appendChild(createdDiv);
            createdDiv.appendChild(createdP);

            articleDiv.appendChild(updatedDiv);
            updatedDiv.appendChild(space);
            updatedDiv.appendChild(updateP);
            updatedDiv.appendChild(contextB);
            contextB.appendChild(contentI);

            contextContainer.appendChild(articleDiv);
            container.appendChild(contextContainer);
        });
    }

    function confirmationModal(message, callback) {
      const body = document.querySelector("body");
  
      const shade = document.createElement("div");
      shade.className = "fixed inset-0 bg-gray-900 bg-opacity-50 z-50 flex justify-center items-center";
  
      const modal = document.createElement("div");
      modal.className = "bg-white w-[90%] max-w-sm rounded-lg shadow-lg p-6 z-60";
  
      const title = document.createElement("h2");
      title.className = "text-lg font-bold text-gray-800 mb-4";
      title.textContent = "Confirmation";
  
      const messageEl = document.createElement("p");
      messageEl.className = "text-gray-600 mb-6";
      messageEl.textContent = message;
  
      const buttonContainer = document.createElement("div");
      buttonContainer.className = "flex justify-end gap-4";
  
      const confirmButton = document.createElement("button");
      confirmButton.className = "bg-orange-500 text-white px-4 py-2 rounded-lg hover:bg-orange-600 focus:ring-2 focus:ring-orange-300 focus:outline-none";
      confirmButton.textContent = "Confirm";
      confirmButton.onclick = () => {
          callback(true);
          shade.remove();
      };
  
      const cancelButton = document.createElement("button");
      cancelButton.className = "bg-gray-200 text-gray-800 px-4 py-2 rounded-lg hover:bg-gray-300 focus:ring-2 focus:ring-gray-400 focus:outline-none";
      cancelButton.textContent = "Cancel";
      cancelButton.onclick = () => {
          callback(false);
          shade.remove();
      };
      buttonContainer.append(cancelButton, confirmButton);
      modal.append(title, messageEl, buttonContainer);
      shade.append(modal);
      body.append(shade);
  }
  



    function contextModal(articleID) {


      const position = ".class" + articleID;
      const contextContainer = document.querySelector(position);
  
      if (!contextContainer) {
          console.error(`Context container not found for articleID: ${articleID}`);
          return;
      }
  
      // Check if a context menu already exists in the container
      const existingContextMenu = contextContainer.querySelector('.context-menu');
  
      if (existingContextMenu) {
          console.log("Context menu already exists, not creating a new one.");
          return; 
      }
  
      const contextMenu = document.createElement("div");
      contextMenu.className = "context-menu w-full flex justify-end min-h-10  mb-2 "; 
  
      const menu = document.createElement("div");
      menu.className = "w-[150px] h-fit p-2 border-md border-[1px] border-black bg-white rounded-md flex flex-col gap-y-2"

      const headCont = document.createElement("div");
      headCont.className = "w-full flex justify-between";

      const close = document.createElement("i");
      close.className = "text-xs hover:cursor-pointer fa-solid fa-x";
      close.setAttribute("onClick", `handleEvent('close' ,${articleID})`);

      const head = document.createElement("p");
      head.className = "text-gray-600 text-center text-xs";
      head.textContent = "Action Menu";
      
      

      const editB = document.createElement("button");
      editB.className = "border-[1px] bg-orange-100 rounded-md text-xs px-2 py-[5px] hover:bg-orange-200 hover:border-orange-300 hover:cursor-pointer";
      editB.setAttribute("onClick", `handleEvent('edit' ,${articleID})`);
      editB.textContent = "Edit";

      const deleteB = document.createElement("button");
      deleteB.className = "border-[1px] bg-orange-100 rounded-md text-xs px-2 py-[5px] hover:bg-orange-200 hover:border-orange-300 hover:cursor-pointer hover:cursor-pointer";
      deleteB.setAttribute("onClick", `handleEvent('delete' ,${articleID})`);
      deleteB.textContent = "Delete";
      
      menu.appendChild(headCont);
      menu.appendChild(editB);
      menu.appendChild(deleteB);
      headCont.appendChild(head);
      headCont.appendChild(close);


      contextContainer.appendChild(contextMenu);
      contextMenu.appendChild(menu);
      console.log("Context menu created for articleID:", articleID);

    
  }


  
  
  function hideContextMenu() {
      const contextMenu = document.querySelector('.context-menu');

      if (contextMenu) {
        contextMenu.remove(); 
      }
  }






function handleEvent(type, articleID){

    switch(type){
        case "preview":
        //alert(type +" for " + articleID);


        confirmationModal("Are you sure you want to preview this item?", (confirmed) => {
          if (confirmed) {
                console.log("Action confirmed! ID:" + articleID + " has ben viewed ");
                // Proceed with the preview logic
            } else {
                console.log("Action canceled!");
            }
        });

        break;
        case "edit":
        //alert(type +" for " + articleID);

        confirmationModal("Are you sure you want to edit this item?", (confirmed) => {
          if (confirmed) {
                console.log("Action confirmed! ID:"  + articleID + " has ben edited ");
                // Proceed with the preview logic

                hideContextMenu();
            } else {
                console.log("Action canceled!");

                hideContextMenu();
            }
        });
        break;
        case "delete":
        //alert(type +" for " + articleID);
        confirmationModal("Are you sure you want to delete this item?", (confirmed) => {
          if (confirmed) {
                console.log("Action confirmed! ID:"  + articleID + " has ben deleted ");
                // Proceed with the preview logic

                hideContextMenu();
            } else {
                console.log("Action canceled!");

                hideContextMenu();
            }
        });


        hideContextMenu();
        break;
        case "close":

        hideContextMenu();
        break;
    }
   
}


function handleSortChange() {
  const sortOption = document.getElementById("sort").value;

  // Define sorting criteria based on selected value
  let sortBy = "created_at"; // Default column
  let sortOrder = "desc"; // Default order

  switch (sortOption) {
      case "new":
          sortBy = "created_at";
          sortOrder = "desc"; // Latest first
          break;
      case "old":
          sortBy = "created_at";
          sortOrder = "asc"; // Oldest first
          break;
      case "title_asc":
          sortBy = "article_title";
          sortOrder = "asc"; // Alphabetical (A-Z)
          break;
      case "title_desc":
          sortBy = "article_title";
          sortOrder = "desc"; // Reverse Alphabetical (Z-A)
          break;
  }

  getData(sortBy, sortOrder);
}


handleSortChange();


