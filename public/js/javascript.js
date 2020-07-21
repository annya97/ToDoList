// Execute only in all_tasks page
if (window.location.pathname === '/public/all_tasks') {
    // Check if there is any task to display
    let tasksContainer = document.getElementById('all-tasks-container');
    if (!tasksContainer.firstElementChild) {
        tasksContainer.innerHTML = '<div id="no-tasks"><p>Nav nevienas darāmās lietas.</p><p>Pamēģini pievienot jaunu! :)</p></div>';
    }
    // Check if task has description
    let titleAndDesc = document.querySelectorAll('.title-and-desc');
    let desc = document.querySelectorAll('.description');
    for (let i = 0; i < titleAndDesc.length; i++) {
        if (desc[i].innerHTML === "") {
            desc[i].remove();
            titleAndDesc[i].classList.add("title-only");
        }
    }
}