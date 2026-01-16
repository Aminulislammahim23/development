function showSection(sectionId, event) {
    document.querySelectorAll('.section').forEach(sec => {
        sec.classList.remove('active');
    });

    document.getElementById(sectionId).classList.add('active');

    document.querySelectorAll('.menu li').forEach(li => {
        li.classList.remove('active');
    });

    if (event && event.target) {
        let clickedElement = event.target;
        if (clickedElement.tagName !== 'LI') {
            clickedElement = clickedElement.closest('li');
        }
        if (clickedElement) {
            clickedElement.classList.add('active');
        }
    }
}

// Also add a default active state when page loads
window.addEventListener('DOMContentLoaded', function() {
    // Set the first menu item as active by default
    const firstMenuItem = document.querySelector('.menu li');
    if (firstMenuItem && !document.querySelector('.menu li.active')) {
        firstMenuItem.classList.add('active');
    }
});