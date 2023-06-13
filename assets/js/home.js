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

document.querySelectorAll(".table-sortable th:not(:first-child):not(:last-child)").forEach(headerCell => {
    headerCell.addEventListener("click", () => {
        const tableElement = headerCell.closest("table");
        const headerIndex = Array.from(headerCell.parentNode.children).indexOf(headerCell);
        const currentIsAscending = headerCell.classList.contains("th-sort-asc");

        sortTableByColumn(tableElement, headerIndex, !currentIsAscending);
    });
});

// PreventDefault on tBody(row), MenuContent Overlay show/hide on tBody(row)

const menuContent = {

    show:function(e) {
        e.preventDefault();

        let menu = document.getElementById("menuContent");
        menu.style.left = e.clientX -240 + "px";  // sidebar 240
        menu.style.top = e.clientY -50 + "px";   // nav 50
        menu.classList.remove("hidden");
    },
    hide:function() {
        let menu = document.getElementById("menuContent");
        menu.classList.add("hidden");
    },

};

window.addEventListener("click", function() {
    menuContent.hide();
});