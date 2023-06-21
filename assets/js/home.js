/**
 *  Sorts a HTML table.
 * 
 * @param {HTMLTableElement} table Table to sort
 * @param {number} column Index of the column to sort
 * @param {boolean} asc Determines if the sorting will be in ascending order
 */
function sortTableByColumn(table, column, asc = true) 
{
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

    show:function(e) 
    {
        e.preventDefault();

        let menu = document.getElementById("menuContent"); 
        menu.style.left = e.clientX -240 + "px";  // sidebar 240 Horizontal position
        menu.style.top = e.clientY -50 + "px";   // nav 50  Vertical position
        menu.classList.remove("hidden");

        table.select(e);
    },
    hide:function() 
    {
        let menu = document.getElementById("menuContent");
        menu.classList.add("hidden");
    },
};

const table = {

    select:function(e) 
    {
        let item = e.target; 

        // Traverse up the DOM tree to find the nearest "tr" or "body" element
        while(item.tagName != 'TR' && item.tagName != 'BODY') 
        {
            item = item.parentNode;
        }

        // If a "tr" element is found, select it by adding the "row" class
        if(item.tagName == 'TR') 
        {
            let checkbox = item.querySelector('.select');
            checkbox.checked = !checkbox.checked;

            if(checkbox.checked)
            {
                item.classList.add("row");
            } else {
                item.classList.remove("row");
            }
        }    
    },

    toggleAll:function(e) 
    {
        let checkboxes = document.getElementsByClassName("select");
        for(var i = 0; i < checkboxes.length; i++)
        {
            checkboxes[i].checked = e.target.checked;
            let item = checkboxes[i].parentNode.parentNode;
            if(e.target.checked)
            {
                item.classList.add("row");
            } else {
                item.classList.remove("row");
            }
        }
    },

    refresh: function() 
    {
        let myform = new FormData();
        myform.append('data_type', 'get_files');

        let xm = new XMLHttpRequest();
        xm.addEventListener('readystatechange', function() 
        {
            if(xm.readyState == 4)
            {
                if(xm.status == 200)
                {
                    // Recreate table
                    let tbody = document.querySelector(".table-body");
                    tbody.innerHTML = "";

                    let obj = JSON.parse(xm.responseText);
                    if(obj.success && obj.data_type == "get_files")
                    {
                        for(var i = 0; i < obj.rows.length; i++)
                        {
                            let tr = document.createElement('tr');
                            tr.setAttribute('id','tr_'+i);

                            tr.innerHTML = `
                                <td><input type="checkbox" class="select" onchange="table.select(event)"></td>
                                <td>${obj.rows[i].file_name}</td>
                                <td>${obj.rows[i].file_size}</td>
                                <td>${obj.rows[i].date_updated}</td>
                                <td>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" transform="rotate(90)" style="fill: rgba(0, 0, 0, 1);">
                                        <path d="M12 10c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0-6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 12c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"></path>
                                    </svg>
                                </td>
                            `;
                            tbody.appendChild(tr);
                        }

                    } else {
                        tbody.innerHTML = `<tr><td colspan="10" style="text-align:center">No files found!</td></tr>`;
                    }

                } else {
                    console.log(xm.responseText);
                }
            }    
        });

        // Open a POST request api.php and send the FormData
        xm.open('post', 'api.php', true);
        xm.send(myform);
    },
};

const upload = {

    // Function to trigger the file upload
    uploadBtn: function() 
    {
        document.getElementById("file-upload").click();
    },

    // Function to handle the file upload process
    send: function(files) 
    {

        if(upload.uploading) 
        {
            alert("Please wait for the upload to complete!");
            return;
        }

        // Upload multiple files using FormData
        upload.uploading = true;

        let myform = new FormData();

        myform.append('data_type', 'upload_files');
        for(var i = 0; i < files.length; i++) 
        {
            
            myform.append('file'+i, files[i]);
        }

        let xm = new XMLHttpRequest();
        
        xm.addEventListener('error', function(e) 
        {
            alert("An error occured! Please check your connection");

        });

        // Handle changes in the request state
        xm.addEventListener('readystatechange', function() 
        {
            if(xm.readyState == 4)
            {
                if(xm.status == 200)
                {
                    let obj = JSON.parse(xm.responseText);
                    if(obj.success)
                    {
                        alert("Upload complete!");
                        table.refresh();
                    } else {
                        alert("Could not complete upload!");
                    }
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
    dropZone: 
    {
        highlight: function() 
        {
            document.querySelector(".drop-upload").classList.add("drop-zone-highlight");
            document.querySelector(".table-body").classList.add("drop-zone-highlight");
        },
        removeHighlight: function() 
        {
            document.querySelector(".drop-upload").classList.remove("drop-zone-highlight");
            document.querySelector(".table-body").classList.remove("drop-zone-highlight");
        }
    },
 
    // Handle the drop event for the dropzone
    drop: function(e) 
    {
        e.preventDefault();
        upload.dropZone.removeHighlight();
        upload.send(e.dataTransfer.files);
    },

    // Handle the dragover event for the dropzone
    dragOver: function(e) 
    {
        e.preventDefault();
        upload.dropZone.highlight();
    },
}

table.refresh();

window.addEventListener("click", function() 
{
    menuContent.hide();
});