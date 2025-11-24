document.addEventListener("DOMContentLoaded", function() {
    let addBtn = document.getElementById("add-item-btn");
    let itemsContainer = document.getElementById("items-container");
    let template = document.getElementById("order-item-template");
let items=document.querySelector('.item-num');    
let itemCount = 0;
if(addBtn){
    addBtn.addEventListener("click", function() {
        itemCount++;
        
        // Clone template
        let newItem = template.cloneNode(true);
        newItem.classList.remove("hidden");
        newItem.id = ""; // remove duplicate id
        newItem.querySelector(".item-number").innerText = itemCount;
       
        // Append to container
        itemsContainer.appendChild(newItem);
       items.innerText="Items : "+ itemCount;

    });
}
    // Add first item automatically
  if (addBtn) {
    addBtn.click();
}

    
document.querySelectorAll(".file-upload-input").forEach(input => {
        input.addEventListener("change", function () {
            const fileNameSpan = this.closest(".extraOutline").querySelector(".file-name");
            if (this.files.length > 0) {
                fileNameSpan.textContent = this.files[0].name;
                fileNameSpan.classList.remove("hidden");
            } else {
                fileNameSpan.textContent = "";
                fileNameSpan.classList.add("hidden");
            }
        });
    });
});