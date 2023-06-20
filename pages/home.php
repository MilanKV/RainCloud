<!-- breadcrumb -->
<div class="breadcrumb">
    <ul>
        <li class="breadcrumb_item">
            <a href="#" class="breadcrumb_link">Home</a>
        </li>
        <li class="breadcrumb_item">
            <a href="#" class="breadcrumb_link">file1</a>
        </li>
        <li class="breadcrumb_item">
            <a href="#" class="breadcrumb_link">file2</a>
        </li>
        <li class="breadcrumb_item">
            <a href="#" class="breadcrumb_link">file3</a>
        </li>
    </ul>
</div>
<!-- Home  -->
<div class="home-content">
    <!-- ActionToolbar  -->
    <div class="nav_toolbar">
        <div class="action_toolbar">
            <div class="select">
                <button class="btn" onclick="upload.uploadBtn()">Upload</button>
                <input onchange="upload.send(this.files)" type="file" id="file-upload" class="hidden" multiple>
            </div>
            <div class="select">
                <button class="btn">Create <i class='bx bx-chevron-down'></i></button>
                <ul class="sub-menu hidden">
                    <li>
                        <i class='bx bx-folder' ></i>
                        <a href="#">Folder</a>
                    </li>
                    <li>
                        <i class='bx bx-file-blank'></i>
                        <a href="#">File</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <!-- Drag and drop  -->
    <div class="drop-upload" ondrop="upload.drop(event)" ondragover="upload.dragOver(event)" ondragleave="upload.dropZone.removeHighlight()">
        <div class="drop-zone">
            <div class="drop-text">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" style="fill: rgba(115, 108, 100, 1);">
                    <path d="M13 19v-4h3l-4-5-4 5h3v4z"></path>
                    <path d="M7 19h2v-2H7c-1.654 0-3-1.346-3-3 0-1.404 1.199-2.756 2.673-3.015l.581-.102.192-.558C8.149 8.274 9.895 7 12 7c2.757 0 5 2.243 5 5v1h1c1.103 0 2 .897 2 2s-.897 2-2 2h-3v2h3c2.206 0 4-1.794 4-4a4.01 4.01 0 0 0-3.056-3.888C18.507 7.67 15.56 5 12 5 9.244 5 6.85 6.611 5.757 9.15 3.609 9.792 2 11.82 2 14c0 2.757 2.243 5 5 5z"></path>
                </svg>
                <span class="text">
                    <strong>Drop files here to upload,</strong> or use the 'Upload' button
                </span>
            </div>
        </div>
    </div>
    <!-- TableContent  -->
    <div class="table-content">
        <div class="selectable-list">
            <table class="table-sortable">
                <thead>
                    <tr>
                        <th><label for="SelectAll"><input type="checkbox" class="select"></label></th>
                        <th>
                            <span>Name</span>
                            <i class='bx bx-up-arrow-alt arrow'></i>
                        </th>
                        <th>
                            File Size
                            <i class='bx bx-up-arrow-alt arrow'></i>
                        </th>
                        <th>
                            Modified
                            <i class='bx bx-up-arrow-alt arrow'></i>
                        </th>
                        <th></th>
                    </tr>
                </thead>
                <tbody id="table-body" class="table-body" oncontextmenu="menuContent.show(event)" onclick="table.select(event)" ondrop="upload.drop(event)" ondragover="upload.dragOver(event)" ondragleave="upload.dropZone.removeHighlight()">
                    <!-- <tr>
                        <td><input type="checkbox" class="select"></td>
                        <td>a</td>
                        <td>123</td>
                        <td>01/24/2018</td>
                        <td>
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" transform="rotate(90)" style="fill: rgba(0, 0, 0, 1);">
                                <path d="M12 10c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0-6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 12c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"></path>
                            </svg>
                        </td>
                    </tr> -->
                </tbody>
            </table>
        </div>
    </div>
</div>
<!-- Menu Content -->
<div id="menuContent" class="menu-content hidden" oncontextmenu="event.preventDefault()">
    <div class="menu-container">
        <div class="menu-heading">
            <div class="menu-item">
                <span class="title-name">
                    Folder
                </span>
            </div>
        </div>
        <div class="menu-body">
            <div class="menu-item">
                <div class="menu-row">
                    <div class="icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" style="fill: rgba(0, 0, 0, 1);">
                            <path d="m12 16 4-5h-3V4h-2v7H8z"></path><path d="M20 18H4v-7H2v7c0 1.103.897 2 2 2h16c1.103 0 2-.897 2-2v-7h-2v7z"></path>
                        </svg>
                    </div>
                    <div class="title">
                        Download
                    </div>
                </div>
                <div class="menu-row">
                    <div class="icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" style="fill: rgba(0, 0, 0, 1);">
                            <path d="M11 7.05V4a1 1 0 0 0-1-1 1 1 0 0 0-.7.29l-7 7a1 1 0 0 0 0 1.42l7 7A1 1 0 0 0 11 18v-3.1h.85a10.89 10.89 0 0 1 8.36 3.72 1 1 0 0 0 1.11.35A1 1 0 0 0 22 18c0-9.12-8.08-10.68-11-10.95zm.85 5.83a14.74 14.74 0 0 0-2 .13A1 1 0 0 0 9 14v1.59L4.42 11 9 6.41V8a1 1 0 0 0 1 1c.91 0 8.11.2 9.67 6.43a13.07 13.07 0 0 0-7.82-2.55z"></path>
                        </svg>
                    </div>
                    <div class="title">
                        Share
                    </div>
                </div>
                <div class="menu-row">
                    <div class="icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" style="fill: rgba(0, 0, 0, 1);">
                            <path d="M18 2H6c-1.103 0-2 .897-2 2v18l8-4.572L20 22V4c0-1.103-.897-2-2-2zm0 16.553-6-3.428-6 3.428V4h12v14.553z"></path>
                        </svg>
                    </div>
                    <div class="title">
                        Add to Favorites
                    </div>
                </div>
            </div>
            <div class="menu-item">
                <div class="menu-row">
                    <div class="icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" style="fill: rgba(0, 0, 0, 1);">
                            <path d="M18 11h-5V6h3l-4-4-4 4h3v5H6V8l-4 4 4 4v-3h5v5H8l4 4 4-4h-3v-5h5v3l4-4-4-4z"></path>
                        </svg>
                    </div>
                    <div class="title">
                        Move
                    </div>
                </div>
                <div class="menu-row">
                    <div class="icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" style="fill: rgba(0, 0, 0, 1);">
                            <path d="m7 17.013 4.413-.015 9.632-9.54c.378-.378.586-.88.586-1.414s-.208-1.036-.586-1.414l-1.586-1.586c-.756-.756-2.075-.752-2.825-.003L7 12.583v4.43zM18.045 4.458l1.589 1.583-1.597 1.582-1.586-1.585 1.594-1.58zM9 13.417l6.03-5.973 1.586 1.586-6.029 5.971L9 15.006v-1.589z"></path><path d="M5 21h14c1.103 0 2-.897 2-2v-8.668l-2 2V19H8.158c-.026 0-.053.01-.079.01-.033 0-.066-.009-.1-.01H5V5h6.847l2-2H5c-1.103 0-2 .897-2 2v14c0 1.103.897 2 2 2z"></path>
                        </svg>
                    </div>
                    <div class="title">
                        Edit
                    </div>
                </div>
                <div class="menu-row">
                    <div class="icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" style="fill: rgba(0, 0, 0, 1);">
                            <path d="M15 2H9c-1.103 0-2 .897-2 2v2H3v2h2v12c0 1.103.897 2 2 2h10c1.103 0 2-.897 2-2V8h2V6h-4V4c0-1.103-.897-2-2-2zM9 4h6v2H9V4zm8 16H7V8h10v12z"></path>
                        </svg>
                    </div>
                    <div class="title">
                        Delete
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>