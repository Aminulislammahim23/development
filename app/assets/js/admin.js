function showSection(section) {

    hideAllSections();

    document.querySelectorAll(".menu li").forEach(li => {
        li.classList.remove("active");
    });

    if (section === "dashboard") {
        document.getElementById("dashboardSection").style.display = "grid";
        document.querySelector(".menu li:nth-child(1)").classList.add("active");
    }

    if (section === "users") {
        document.getElementById("usersSection").style.display = "block";
        document.querySelector(".menu li:nth-child(2)").classList.add("active");
    }

    if (section === "courses") {
        document.getElementById("courseSection").style.display = "block";
        document.querySelector(".menu li:nth-child(2)").classList.add("active");
    }

    if (section === "addusers") {
        document.getElementById("addUsersSection").style.display = "block";
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

document.getElementById("searchBtn").addEventListener("click", function () {

    const query = document.getElementById("searchUser").value.trim();

    if (query === "") {
        alert("Please enter email or name");
        return;
    }

    fetch("../../controllers/searchUser.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },
        body: "query=" + encodeURIComponent(query)
    })
    .then(response => {
        if (!response.ok) throw new Error('Network response was not ok (' + response.status + ')');
        // try to parse JSON, if that fails include raw text in the error
        return response.json().catch(err => response.text().then(txt => { throw new Error('Invalid JSON response:\n' + txt); }));
    })
    .then(data => {
        console.log('Parsed JSON:', data);

        if (data.status === "found") {
            const setIf = (id, val) => { const el = document.getElementById(id); if (el) el.value = (val !== undefined && val !== null) ? val : ''; else console.warn('Missing element:', id); };

            setIf("user_id", data.user.id);
            setIf("full_name", data.user.full_name);
            setIf("email", data.user.email);
            setIf("role", data.user.role);
            setIf("password", "");

        } else {
            alert("User not found");
            // clear fields safely
            ['user_id','full_name','email','password'].forEach(id => {
                const el = document.getElementById(id);
                if (el) el.value = '';
            });
            const roleEl = document.getElementById('role'); if (roleEl) roleEl.value = 'student';
        }
    })
    .catch(error => {
        console.error("Search error:", error);
        alert('Search failed: ' + error.message);
    });
});




