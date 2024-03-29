var LOGGED_IN = false;
var USERNAME = false;
var SPACE_TOTAL = 0;
var SPACE_OCCUPIED = 0;
var FOLDER_ID = 0;

const table = {
    ROWS: [],
    lastSelectedRow: null,
    // Function to handle row selection
    select:function(e) 
    {
        const item = e.target.closest('tr'); 
        const checkbox = item.querySelector('.select');
        const isChecked = checkbox.checked;
        // Update the selectAll checkbox state based on the number of checked checkboxes
        const tbodyCheckboxes  = document.querySelectorAll('.table-body .select');
        const selectAllCheckbox = document.getElementById("selectAll");
        // Check if right-click (context menu) event and the item is already selected
        const isRightClick = e.type === 'contextmenu';
        const isSameRowSelected = this.lastSelectedRow === item;
        // Check if the row contains the class "no-files-found"
        if (item.classList.contains('no-files-found')) {
            // Prevent any selection or checkbox interaction
            e.preventDefault();
            return;
        }
        if (isRightClick && isSameRowSelected) {
            // Prevent deselection with right-click
            e.preventDefault();
            return;
        }
        // Handle checkbox interaction
        if (e.target.classList.contains('select') || e.target.tagName === 'LABEL') {
            checkbox.checked = !isChecked;

            // Toggle row selection based on checkbox state
            if (checkbox.checked) {
                item.classList.add('row');
                let tr_id = item.getAttribute('tr_id').replace("tr_", "");
                file_details.show(tr_id);
            } else {
                item.classList.remove('row');
                file_details.hide();
            }
        } else {
            const selectedRow = document.querySelector('.row');
            // Deselect the row if it's already selected
            if (selectedRow && selectedRow === item) 
            {
              selectedRow.classList.remove('row');
              const checkboxes = document.querySelectorAll('.table-body .select');
                for (const cb of checkboxes) {
                    cb.checked = false;
                }
              file_details.hide();
            } else {
                // Deselect all previously selected checkboxes and rows
                const selectedRows = document.querySelectorAll('.row');
                for (const row of selectedRows) {
                    row.classList.remove('row');
                }
                const checkboxes = document.querySelectorAll('.table-body .select');
                for (const cb of checkboxes) {
                    cb.checked = false;
                }
                item.classList.add('row');
                checkbox.checked = true;
                let tr_id = item.getAttribute('tr_id').replace("tr_", "");
                file_details.show(tr_id);
            }
        }
        // Update lastSelectedRow
        if (checkbox.checked) {
            table.lastSelectedRow = item;
        } else {
            table.lastSelectedRow = null;
        }
        // Check if all checkboxes in the tbody are checked and update selectAllCheckbox
        const allChecked = Array.from(tbodyCheckboxes).every((cb) => !cb.checked);
        selectAllCheckbox.checked = !allChecked;
    },
    // Function to get the last selected row
    getLastSelectedRow: function () {
        return table.lastSelectedRow;
    },
    // SelectAll Checkbox
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

    navigateFolder_id: function(folder_id) {
        FOLDER_ID = parseInt(folder_id);
        table.refresh();
    },

    navigateFolder: function(e) {

        let item = e.target;

        while(item.tagName != 'TR' && item.tagName != 'BODY') {
            item = item.parentNode;
        }
        
        if(item.tagName == 'TR') {
            let folder_id = item.getAttribute("folder_id");
            
            if(folder_id) {
                FOLDER_ID = parseInt(folder_id);
                table.refresh();
            }
        }
    },

    // Function to handle hard delete
    hard_delete: function () {
        const selectedRows  = document.querySelectorAll('.row:not(#selectAll)');
        if (selectedRows.length === 0) {
            alert('No items selected for hard delete.');
            return;
        }
        // console.log(`Hard deleting row with id: ${id} and type: ${type}`);
        const confirmDelete = confirm('Are you sure you want to delete the selected items?');
        if (!confirmDelete) {
            return;
        }

        const data = new FormData();
        data.append('data_type', 'hard_delete');

        for (const row of selectedRows) {
            let type = row.getAttribute('type');
            let id = row.getAttribute('id');
            console.log("Debug: ID:", id, "File Type:", type);
            data.append('id[]', id);
            data.append('file_type[]', type);
        }

        const xm = new XMLHttpRequest();
        xm.addEventListener('readystatechange', function () {
            if (xm.readyState === 4) {
                // console.log(xm.responseText);
                if (xm.status === 200) {
                    const response = JSON.parse(xm.responseText);
                    console.log("Response Status:", xm.status);
                    console.log("API Response:", response);
                    if (response.success) {
                        table.refresh();
                    } else {
                        alert('Failed to hard delete the selected items.');
                    }
                } else {
                    alert('Failed to communicate with the server.');
                }
            }
        });
        // Open a POST request api.php and send the FormData
        xm.open('post', '../api.php', true);
        xm.send(data);
    },

    refresh: function() 
    {
        let data = new FormData();
        data.append('data_type', 'get_files');
        data.append('folder_id', FOLDER_ID);
        data.append('selected_page', 'deleted');

        let xm = new XMLHttpRequest();
        xm.addEventListener('readystatechange', function() 
        {
            if(xm.readyState == 4)
            {
                if(xm.status == 200)
                {
                    // console.log(JSON.stringify(JSON.parse(xm.responseText), null, 2));
                    // Recreate table
                    let tbody = document.querySelector(".table-body");
                    tbody.innerHTML = "";

                    let obj = JSON.parse(xm.responseText);

                    // Display Name
                    if(!USERNAME) 
                    {
                        USERNAME = obj.name;
                        document.querySelector(".user_name").innerHTML = obj.name;
                    }
                    // Display Email
                    if(obj.email) {
                        document.querySelector(".user_email").innerHTML = obj.email;
                    }
                    // Check if user is logged-in
                    LOGGED_IN = obj.LOGGED_IN;
                    if(!LOGGED_IN) {

                        window.location.href = 'auth/login.php';
                    }
                    // Update breadcrumbs
                    const bcrumbs = document.querySelector('#breadcrumbs ul');
                    bcrumbs.innerHTML = ''; // Clear existing breadcrumb items

                    // Create the Deleted Files breadcrumb item
                    const deletedItem = createBreadcrumbItem('Deleted Files', 0);
                    bcrumbs.appendChild(deletedItem);

                    // Add the remaining breadcrumb items from the 'obj.breadcrumbs' array in reverse order
                    for (let i = obj.breadcrumbs.length - 1; i >= 0; i--) {
                    const breadcrumbItem = createBreadcrumbItem(obj.breadcrumbs[i].name, obj.breadcrumbs[i].id);
                    bcrumbs.appendChild(breadcrumbItem);
                    }

                    // Function to create a breadcrumb item
                    function createBreadcrumbItem(name, id) {
                    const breadcrumbItem = document.createElement('li');
                    breadcrumbItem.classList.add('breadcrumb_item');
                    const breadcrumbLink = document.createElement('a');
                    breadcrumbLink.href = '#';
                    breadcrumbLink.onclick = function() {
                        table.navigateFolder_id(id);
                    };
                    breadcrumbLink.classList.add('breadcrumb_link');
                    breadcrumbLink.textContent = name;
                    breadcrumbItem.appendChild(breadcrumbLink);
                    return breadcrumbItem;
                    }
                    // Update Space
                    window.updateSpaceInfo(obj);

                    if(obj.success && obj.data_type == "get_files")
                    {
                        const selectAll = document.getElementsByClassName('selectAll')[0];
                        selectAll.classList.remove("hidden");
                        table.ROWS = obj.rows;
            
                        // Generate table rows dynamically
                        for(var i = 0; i < obj.rows.length; i++)
                        {
                            let tr = document.createElement('tr');
                            tr.setAttribute('tr_id', 'tr_' + i);
                            tr.setAttribute('id', obj.rows[i].id);

                            if(obj.rows[i].file_type === 'folder') {
                                tr.setAttribute('type','folder');
                            } else {
                                tr.setAttribute('type','file');
                            }
                            if(obj.rows[i].file_type == 'folder') {
                                tr.setAttribute('folder_id',+ obj.rows[i].id);
                                // Format folder size using the formatFolderSize function for folders
                                folderSizeCell = `<td>${formatSize(obj.rows[i].file_size)}</td>`;
                            } else {
                                // Keep the original file_size for files
                                folderSizeCell = `<td>${formatSize(obj.rows[i].file_size)}</td>`;
                            }
                            tr.innerHTML = `
                                <td>
                                    <input type="checkbox" class="select custom-checkbox" onchange="table.select(event)">
                                    <label></label>
                                </td>
                                <td>${obj.rows[i].icon}</td>
                                <td>${obj.rows[i].file_name}</td>
                                ${folderSizeCell}
                                <td>${obj.rows[i].date_updated}</td>
                                <td></td>
                            `;
                            tbody.appendChild(tr);
                        }
                    } else {
                        // If there are no files, create the "No files found!" row
                        const noFilesRow = document.createElement('tr');
                        noFilesRow.innerHTML = `<td colspan="10" style="text-align:center" class="no-files-row">No files found!</td>`;
                        tbody.appendChild(noFilesRow);
                        
                        const selectAll = document.getElementsByClassName('selectAll')[0];
                        selectAll.classList.add("hidden");
                    }
                } else {
                    console.log(xm.responseText);
                }
            }    
        });

        // Open a POST request api.php and send the FormData
        xm.open('post', '../api.php', true);
        xm.send(data);
    },
}

var file_details = {
    
    show:function(tr_id) 
    {
        document.querySelector(".no-file-checked").classList.add("hidden");
        let row = table.ROWS[tr_id];
        
        let file_details_panel = document.querySelector(".body-container");
        const fileImage = document.getElementById("file_image");
        const fileIcon = document.getElementById("file_icon");

        // Check the file type and display the appropriate content
        if (row.file_type.startsWith("image/jpeg")) {
            // It's an image file, show the image element
            fileImage.style.display = "block";
            fileIcon.style.display = "none";
            fileImage.src = "../" +  row.file_path;
        } else {
            // It's not an image file, show the icon element
            fileImage.style.display = "none";
            fileIcon.style.display = "block";
            fileIcon.innerHTML = row.icon;
        }

        // Get the truncated file name (maximum 20 characters)
        let truncatedFileName = truncateString(row.file_name, 20);
        let formatFileSize = formatSize(row.file_size);
        // Update the file details in the panel
        file_details_panel.querySelector(".file_name").textContent = truncatedFileName;
        file_details_panel.querySelector(".size").textContent = formatFileSize;
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

// Function to truncate a string to a specified length
function truncateString(str, maxLength) {
    if (str.length <= maxLength) {
        return str;
    } else {
        const firstPartLength = Math.floor((maxLength - 3) / 2);
        const lastPartLength = maxLength - 3 - firstPartLength;
        return str.substring(0, firstPartLength) + '...' + str.substring(str.length - lastPartLength);
    }
}
// convert the file size B,KB,MB,GB
function formatSize(bytes) {
    if (bytes >= 1024 * 1024 * 1024) {
        return (bytes / (1024 * 1024 * 1024)).toFixed(2) + ' GB';
    } else if (bytes >= 1024 * 1024) {
        return (bytes / (1024 * 1024)).toFixed(2) + ' MB';
    } else if (bytes >= 1024) {
        return (bytes / 1024).toFixed(2) + ' KB';
    } else {
        return '';
    }
}

table.refresh();