// Profile btn  
const profileButton = document.getElementById("util-btn");
const profileMenu = document.getElementById("prof-Menu");

profileButton.addEventListener("click", toggleMenu.bind(null, profileMenu));
window.addEventListener("click", handleWindowCLick);

function toggleMenu(menu) {
    menu.classList.toggle("hidden");
}

function handleWindowCLick(event) {
    hideMenu(profileMenu, profileButton, event);
}

function hideMenu(menu, button, event) {
    if(!button.contains(event.target) && !menu.contains(event.target)) {
        menu.classList.add("hidden")
    }
}

function logout() 
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
}

function formatBytes(bytes) {
    if (bytes == null) {
        return '0.00 B';
    }else if (bytes <1024) {
        return bytes + ' B';
    } else if (bytes < 1024 * 1024) {
        return (bytes / 1024).toFixed(2) + " KB";   
    } else if (bytes < 1024 * 1024 * 1024) {
        return (bytes / (1024 * 1024)).toFixed(2) + " MB";
    } else {
        return (bytes / (1024 * 1024 * 1024)).toFixed(2) + " GB";
        }
}

function updateSpaceInfo(obj) {

    let space_total_bytes = obj.space_total * (1024 * 1024 * 1024); 
    let space_occupied_formatted = formatBytes(obj.space_occupied);
    let space_percent = Math.round((obj.space_occupied / space_total_bytes) * 100);

    SPACE_OCCUPIED = obj.space_occupied;
    SPACE_TOTAL = obj.space_total;

    document.querySelector(".user-space").innerHTML = `Your account has ${obj.space_total} GB storage`;
    document.querySelector(".rain_space").innerHTML = `${space_occupied_formatted} of ${obj.space_total} GB used`;
    document.querySelector(".space_percent").style.width = `${space_percent}%`;
}
    
window.updateSpaceInfo = updateSpaceInfo;