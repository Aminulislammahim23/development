function showSection(section) {

    hideAllSections();

    document.querySelectorAll(".menu li").forEach(li => {
        li.classList.remove("active");
    });

    if (section === "dashboard") {
        document.getElementById("dashboardSection").style.display = "grid";
        document.querySelector(".menu li:nth-child(1)").classList.add("active");
    }

    if (section === "courses") {
        document.getElementById("courseSection").style.display = "block";
        document.querySelector(".menu li:nth-child(2)").classList.add("active");
    }

    if (section === "addcourses") {
        document.getElementById("addcoursesSection").style.display = "block";
        document.querySelector(".menu li:nth-child(2)").classList.add("active");
        resetForms();
    }

    if (section === "updateUsersSection") {
        document.getElementById("updateUsersSection").style.display = "block";
        document.querySelector(".menu li:nth-child(2)").classList.add("active");
        resetForms();
    }

    if (section === "terminateUsersSection") {
        document.getElementById("terminateUsersSection").style.display = "block";
        document.querySelector(".menu li:nth-child(2)").classList.add("active");
        resetForms();
    }

    if (section === "profile") {
        document.getElementById("profileSection").style.display = "block";
        document.querySelector(".menu li:nth-child(3)").classList.add("active");
    }

    if (section === "settings") {
        document.getElementById("settingsSection").style.display = "block";
        document.querySelector(".menu li:nth-child(4)").classList.add("active");
    }

    if (section === "logout") {
        logoutAdmin();
    }
}

function hideAllSections() {
    document.querySelectorAll(".section").forEach(sec => {
        sec.style.display = "none";
    });
}

function resetForms() {
    document.querySelectorAll("form").forEach(f => f.reset());
}
hideAllSections();
showSection("dashboard");


function logoutAdmin() {
    if (confirm("Are you sure you want to logout?")) {
        window.location.href = "../controller/logout.php";
    }
}

function addCourse() {
    window.location.href = "../../controllers/courseController/addCourse.php";
}

function updateCourse() {
    window.location.href = "../../controllers/courseController/updateCourse.php";
}

function deleteCourse() {
    window.location.href = "../../controllers/courseController/deleteCourse.php";
}