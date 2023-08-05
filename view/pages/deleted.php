<!-- breadcrumb -->
<div id="breadcrumbs" class="breadcrumb">
    <ul>
        <li class="breadcrumb_item">
            <a href="#" onclick="table.navigateFolder_id(0)" class="breadcrumb_link"></a>
        </li>
    </ul>
</div>

<!-- Home  -->
<div class="home-content">
    <!-- ActionToolbar  -->
    <div class="nav_toolbar">
        <div class="action_toolbar">
            <div class="brws-buttons">
                <button class="btn">Restore</button>
            </div>
            <div class="brws-buttons">
                <button class="btn" onclick="table.hard_delete()">Delete</button>
            </div>
        </div>
    </div>
    <!-- TableContent  -->
    <div class="table-content">
        <div class="selectable-list">
            <table class="table-sortable">
                <thead>
                    <tr>
                        <th>
                            <input type="checkbox" id="selectAll" class="select custom-checkbox" onchange="table.toggleAll(event)">
                            <label class="selectAll hidden" for="selectAll"></label>
                        </th>
                        <th></th>
                        <th>
                            <span>Name</span>
                        </th>
                        <th>
                            File Size
                        </th>
                        <th>
                            Modified
                        </th>
                        <th></th>
                    </tr>
                </thead>
                <tbody id="table-body" class="table-body" onclick="table.select(event)" ondblclick="table.navigateFolder(event)">
                    <!-- <tr>
                        <td><input type="checkbox" class="select"></td>
                        <td>a</td>
                        <td>01/24/2018</td>
                    </tr> -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="../assets/js/delete.js"></script>