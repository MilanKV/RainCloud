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

    ROWS: [],

    // Function to handle row selection
    select:function(e) 
    {
        const item = e.target.closest('tr'); 
        const checkbox = item.querySelector('.select');
        const isChecked = checkbox.checked;

        // Handle row selection
        if (item && e.target.tagName !== 'INPUT') 
        {
            const selectedRow = document.querySelector('.row');
            // Deselect the row if it's already selected
            if (selectedRow && selectedRow === item) 
            {
              selectedRow.classList.remove('row');
              checkbox.checked = false;
              file_details.hide();
            } else {
                // Select the clicked row and show file details
                if(selectedRow) 
                {
                    selectedRow.classList.remove('row');
                    selectedRow.querySelector('.select').checked = false;
                }
                item.classList.add('row');
                checkbox.checked = true;
                let id = item.getAttribute('id').replace("tr_", "");
                file_details.show(id);
            }
        } 
        // Handle checkbox interaction
        else if(item && e.target.tagName === 'INPUT')
        {
            checkbox.checked = isChecked;

            // Toggle row selection and show/hide file details based on checkbox state
            if(checkbox.checked)
            {
                item.classList.add('row');
                let id = item.getAttribute('id').replace("tr_", "");
                file_details.show(id);
            } else {
                item.classList.remove('row');
                file_details.hide();
            }

            // Uncheck other rows if unchecking the checkbox
            const isUnchecking = isChecked && !checkbox.checked;

            if (isUnchecking) {
                const selectedRow = document.querySelector('.row');
                if (selectedRow === item) {
                    file_details.hide();
                }
                selectedRow.classList.remove('row');
                selectedRow.querySelector('.select').checked = false;
            }
        }
    },

    toggleAll:function(e) 
    {
        // Toggle the checked state of all checkboxes and corresponding row styling
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
                        table.ROWS = obj.rows;
            
                        // Generate table rows dynamically
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

var file_details = {
    
    show:function(id) 
    {
        document.querySelector(".no-file-checked").classList.add("hidden");
        let row = table.ROWS[id];
        
        let file_details_panel = document.querySelector(".body-container");
        
        // Update the file details in the panel
        file_details_panel.querySelector(".file_name").textContent = row.file_name;
        file_details_panel.querySelector(".size").textContent = row.file_size;
        file_details_panel.querySelector(".type").textContent = row.file_type;
        file_details_panel.querySelector(".date_created").textContent = row.date_created;
        file_details_panel.querySelector(".date_updated").textContent = row.date_updated;

        file_details_panel.classList.remove("hidden");
    },

    hide:function()
    {
        document.querySelector(".no-file-checked").classList.remove("hidden");
        document.querySelector(".body-container").classList.add("hidden");
    },
};

table.refresh();

window.addEventListener("click", function() 
{
    menuContent.hide();
});