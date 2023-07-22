var LOGGED_IN = false;
var USERNAME = false;
var SPACE_TOTAL = 0;
var SPACE_OCCUPIED = 0;
var FOLDER_ID = 0;

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

    refresh: function() 
    {
        let data = new FormData();
        data.append('data_type', 'get_files');
        data.append('folder_id', FOLDER_ID);

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

                    // Create the Home breadcrumb item
                    const homeItem = createBreadcrumbItem('Home', 0);
                    bcrumbs.appendChild(homeItem);

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

                        table.ROWS = obj.rows;
            
                        // Generate table rows dynamically
                        for(var i = 0; i < obj.rows.length; i++)
                        {
                            let tr = document.createElement('tr');
                            tr.setAttribute('id','tr_'+i);
                            
                            if(obj.rows[i].file_type == 'folder') {
                                tr.setAttribute('type','folder');
                            } else {
                                tr.setAttribute('type','file');
                            }

                            if(obj.rows[i].file_type == 'folder')
                            tr.setAttribute('folder_id',+ obj.rows[i].id);

                            tr.innerHTML = `
                                <td><input type="checkbox" class="select" onchange="table.select(event)"></td>
                                <td>${obj.rows[i].icon}</td>
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
        xm.open('post', '../api.php', true);
        xm.send(data);
    },

    logout: function() 
    {
        let data = new FormData();
        data.append('data_type', 'user_signout');

        let xm = new XMLHttpRequest();
			xm.addEventListener('readystatechange',function()
            {
				if(xm.readyState == 4)
				{
					if(xm.status == 200)
					{
						//console.log(xm.responseText);
 						window.location.href = '../view/auth/login.php';
 
					}else{
						console.log(xm.responseText);
					}

				}
			});

			xm.open('post', '../api.php', true);
			xm.send(data);
    },
};

const xhrArray = [];
const upload = {
    
    uploading: false,
    // Function to trigger the file upload
    uploadBtn: function() {
        // Upload-Main 
        document.getElementById("upload-btn").addEventListener("click", function() {
            document.getElementById("file-upload").click();
        });
        // Upload-More 
        document.getElementById("upload-more-btn").addEventListener("click", function() {
            document.getElementById("file-upload").click();
        });
    },

    // Function to handle the file upload process
    send: function(files) 
    {
        if(upload.uploading) 
        {
            // alert("Please wait for the upload to complete!");
            return;
        }

        // Upload multiple files using FormData
        upload.uploading = true;

        let data = new FormData();
        data.append('data_type', 'upload_files');
        data.append('folder_id', FOLDER_ID);

        let file_size = 0;

        for(var i = 0; i < files.length; i++) 
        {
            file_size += files[i].size;
            data.append('files[]', files[i]);
        }

        if(parseInt(SPACE_OCCUPIED) + parseInt(file_size) > (SPACE_TOTAL * (1024 * 1024 * 1024)))
        {
            alert("There is not enough space");
            upload.uploading = false;
            return;
        }
        for (let i = 0; i < files.length; i++) {
            const fileIndex = i;
            const file = files[i];
            let dataForFile = new FormData();
            
            dataForFile.append('data_type', 'upload_files');
            dataForFile.append('folder_id', FOLDER_ID);
            dataForFile.append('files[]', files[i]);

            // Create XMLHttpRequest for each file
            let xm = new XMLHttpRequest();
            xhrArray.push(xm);

            xm.addEventListener('error', function(e) 
            {
                alert("An error occured! Please check your connection");

            });

            // Handle changes in the request state
            xm.addEventListener('readystatechange', function() 
            {
                if(xm.readyState == 4) {
                    if(xm.status == 200) {
                        let obj = JSON.parse(xm.responseText);
                        if(obj.success)
                        {
                            // alert("Upload complete!");
                            table.refresh();
                        } else {
                            alert("Could not complete upload!");
                        }
                    } else {
                        console.log(xm.responseText);
                        // alert("An error occured! Please try again later");
                    }

                    upload.uploading = false;
                }    
            });
            // Open a POST request api.php and send the FormData
            xm.open('post', '../api.php', true);

            // Add the progress event listener to track the progress
            xm.upload.addEventListener('progress', function (event) {
                if (event.lengthComputable) {
                    const progressPercentage = (event.loaded / event.total) * 100;
                    upload.displayProgress(fileIndex, progressPercentage, file); // Update progress for the first file
                }
            });
            xm.send(dataForFile);
        }
    },

    // Function to display uploading progress for each file
    displayProgress: function(fileIndex, progressPercentage, file) {
        const progressContainer = document.querySelector('.item-uploading');
        let fileItem = progressContainer.querySelector(`[data-file-index="${fileIndex}"]`);
        
        // If file item doesn't exist, create a new one
        if (!fileItem) {
        const fileItemTemplate = document.createElement('div');
        fileItemTemplate.classList.add('upload-file-row');
        fileItemTemplate.setAttribute('data-file-index', fileIndex);
        fileItemTemplate.innerHTML = `
            <span class="icon-message success">
                <i class="fa-regular fa-circle-check"></i>
            </span>
            <div class="file-content">
                <div class="file-row-name">
                    <span class="file-name">${truncateString(file.name, 30)}</span>
                    <div class="uploading-info">
                        <span id="Uploading-process" class="file-mess">Uploading... 0%</span>
                        <span id="dataTransfer" class="file-mess"></span>
                        <span id="timeLeft" class="file-mess"></span>
                    </div>
                </div>
            </div>
            <button class="row-btn-cancel btn-icon">
                <span class="btn-content">
                    <i class="fa-regular fa-x fa-lg"></i>
                </span>
            </button>
        `;
        progressContainer.appendChild(fileItemTemplate);
    
        const progressBarTemplate = document.createElement('div');
        progressBarTemplate.classList.add('upload-progress');
        progressBarTemplate.innerHTML = '<div class="progress-uploading"></div>';
        progressContainer.appendChild(progressBarTemplate);

        fileItem = fileItemTemplate;
        }
    
        const progressBar = fileItem.nextElementSibling.querySelector('.progress-uploading');
        progressBar.style.width = `${progressPercentage}%`;
        fileItem.querySelector('.file-mess').textContent = `Uploading... ${Math.floor(progressPercentage)}%`;

        // Get the cancel button for the current file
        const cancelButton = fileItem.querySelector('.row-btn-cancel');
        // Add an event listener to the cancel button
        cancelButton.addEventListener('click', function () {
            upload.cancelFileUpload(fileIndex);
        });
    },  
    cancelFileUpload: function(fileIndex) {
        if (xhrArray[fileIndex]) {
            xhrArray[fileIndex].abort(); // Abort the corresponding XMLHttpRequest

            const progressContainer = document.querySelector('.item-uploading');
            const fileItem = progressContainer.querySelector(`[data-file-index="${fileIndex}"]`);
            if (fileItem) {
                const progressBar = fileItem.nextElementSibling;
                progressContainer.removeChild(fileItem);
                progressContainer.removeChild(progressBar);
            }

            xhrArray[fileIndex] = null; // Clear the reference from the array
        }
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
upload.uploadBtn();

var file_details = {
    
    show:function(id) 
    {
        document.querySelector(".no-file-checked").classList.add("hidden");
        let row = table.ROWS[id];
        
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
        // Update the file details in the panel
        file_details_panel.querySelector(".file_name").textContent = truncatedFileName;
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

// Create buttons

// Elements
const createButton = document.getElementById("createButton");
const createMenu = document.getElementById("createMenu");
const overlay = document.getElementById('overlay');
const createInput = document.querySelector('#overlay input');
const createButtonSubmit = document.getElementById('btn-create');
const arrowDrawer = document.querySelector('#arrowdrawer');

// Create Modal
const createModal = {

    uploading: false,

    // Show Create New folder Modal
    showCreateModal: function() {
        createMenu.classList.add('hidden');
        overlay.classList.remove('hidden');
        createInput.value = "";
        createInput.focus();
    },
    // Hide Create New folder Modal
    hideCreateModal: function() {
        overlay.classList.add('hidden');
    },
    // Check input if empty disable create folder button
    checkInput: function() {
        createButtonSubmit.disabled = createInput.value.trim() === '';
    },
    
    new_folder: function() {
        let text = createInput.value.trim();
        overlay.classList.add('hidden');

        let obj = {};
        obj.data_type = 'new_folder';
        obj.name = text;
        obj.folder_id = FOLDER_ID;

        createModal.send(obj);
    },

    send: function(obj) {
        if(createModal.uploading) 
        {
            alert("Please wait for the upload to complete!");
            return;
        }

        createModal.uploading = true;

        let data = new FormData();

        for(key in obj) 
        {
            data.append(key, obj[key]);
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
                        table.refresh();
                    } else {
                        alert("Could not complete operation!");
                    }
                } else {
                    console.log(xm.responseText);
                    alert("An error occured! Please try again later");
                }

                createModal.uploading = false;
            }    
        });

        // Open a POST request api.php and send the FormData
        xm.open('post', '../api.php', true);
        xm.send(data);

        table.refresh();
    },
}

// Event Listeners
createButton.addEventListener("click", toggleMenu.bind(null, createMenu));
createInput.addEventListener("input", createModal.checkInput);
createButtonSubmit.addEventListener("click", createModal.new_folder);
window.addEventListener("click", handleWindowCLick);

function toggleMenu(menu) {
    menu.classList.toggle("hidden");
}

function handleWindowCLick(event) {
    if(![createButton, createMenu, overlay].some(element => element.contains(event.target))) {
        createMenu.classList.add("hidden");
        menuContent.hide();
        createModal.hideCreateModal();  
    }
}

function drawerCollapse() {
    const drawerBody = document.querySelector('.drawer-body-footer');
    if (drawerBody.style.display === '' || drawerBody.style.display === 'flex') {
        drawerBody.style.display = 'none';
    } else {
        drawerBody.style.display = 'flex';
    }
    arrowDrawer.classList.toggle("rotate");
}

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

table.refresh();