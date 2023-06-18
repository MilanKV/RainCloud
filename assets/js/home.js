/**
 *  Sorts a HTML table.
 * 
 * @param {HTMLTableElement} table Table to sort
 * @param {number} column Index of the column to sort
 * @param {boolean} asc Determines if the sorting will be in ascending order
 */
function sortTableByColumn(table, column, asc = true) {
    const direction  = asc ? 1 : -1;
    const tBody = table.tBodies[0];
    const rows = tBody.querySelectorAll("tr");

    // Sort each row 
    const sortedRows = Array.from(rows).sort((a, b) => {
        const selector = `td:nth-child(${column + 1})`;
        const aColText = a.querySelector(selector).textContent.trim();
        const bColText = b.querySelector(selector).textContent.trim();

        return aColText.localeCompare(bColText) * direction;
    });
    
    // Remove all TRs from the table
    tBody.innerHTML = '';

    //Re-add the new sorted rows
    tBody.append(...sortedRows);

    // Remember how the column is currently sorted
    const headerCell = table.querySelector(`th:nth-child(${column + 1})`);
    table.querySelectorAll("th").forEach(th => th.classList.remove("th-sort-asc", "th-sort-desc"));
    headerCell.classList.toggle("th-sort-asc", asc);
    headerCell.classList.toggle("th-sort-desc", !asc);
}

// Select all table header cells that are sortable (excluding the first and last columns)
document.querySelectorAll(".table-sortable th:not(:first-child):not(:last-child)").forEach(headerCell => {
    headerCell.addEventListener("click", () => {
        const tableElement = headerCell.closest("table");
        const headerIndex = Array.from(headerCell.parentNode.children).indexOf(headerCell);
        const currentIsAscending = headerCell.classList.contains("th-sort-asc");

        sortTableByColumn(tableElement, headerIndex, !currentIsAscending);
    });
});

const menuContent = {

    show:function(e) {
        e.preventDefault();

        let menu = document.getElementById("menuContent"); 
        menu.style.left = e.clientX -240 + "px";  // sidebar 240 Horizontal position
        menu.style.top = e.clientY -50 + "px";   // nav 50  Vertical position
        menu.classList.remove("hidden");

        table.select(e);
    },
    hide:function() {
        let menu = document.getElementById("menuContent");
        menu.classList.add("hidden");
    },
};

const table = {

    selected: null, // Currently selected table row

    select:function(e) {

        table.selected = null; // Reset the selected row

        // Remove "row" class from all children elements of the table row container
        for (var i = 0; i < e.currentTarget.children.length; i++) {
            e.currentTarget.children[i].classList.remove("row");
        }

        let item = e.target;

        // Traverse up the DOM tree to find the nearest "tr" or "body" element
        while(item.tagName != 'TR' && item.tagName != 'BODY') {
            item = item.parentNode;
        }

        // If a "tr" element is found, select it by adding the "row" class
        if(item.tagName == 'TR') {
            table.selected = item;
            table.selected.classList.add("row");
        }    
    },
};

const upload = {

    // Function to trigger the file upload
    uploadBtn: function() {
        document.getElementById("file-upload").click();
    },

    // Function to handle the file upload process
    send: function(files) {

        if(upload.uploading) {
            alert("Please wait for the upload to complete!");
            return;
        }

        // Upload multiple files using FormData
        upload.uploading = true;

        let myform = new FormData();

        for(var i = 0; i < files.length; i++) {
            
            myform.append('file'+i, files[i]);
        }

        let xm = new XMLHttpRequest();
        
        xm.addEventListener('error', function(e) {
            alert("An error occured! Please check your connection");

        });

        // Handle changes in the request state
        xm.addEventListener('readystatechange', function() {
            if(xm.readyState == 4)
            {
                if(xm.status == 200)
                {
                    alert(xm.responseText);
                } else {
                    console.log(xm.responseText);
                    alert("An error occured! Please try again later");
                }

                upload.uploading = false;
            }    
        });

        // Open a POST request api.php and send the FormData
        xm.open('post', 'api.php', true);
        xm.send(myform);
    },

    // Dropzone highlight functionality
    dropZone: {
        highlight: function() {
            document.querySelector(".drop-upload").classList.add("drop-zone-highlight");
        },
        removeHighlight: function() {
            document.querySelector(".drop-upload").classList.remove("drop-zone-highlight");
        }
    },
 
    // Handle the drop event for the dropzone
    drop: function(e) {
        e.preventDefault();
        upload.dropZone.removeHighlight();
        upload.send(e.dataTransfer.files);
    },

    // Handle the dragover event for the dropzone
    dragOver: function(e) {
        e.preventDefault();
        upload.dropZone.highlight();
    },
}

window.addEventListener("click", function() {
    menuContent.hide();
});