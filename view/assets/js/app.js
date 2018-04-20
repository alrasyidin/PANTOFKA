function updateUser(user_id, new_height) {
    var request = new XMLHttpRequest();
    request.open("post", "controller/updateProfileController.php");
    request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    request.onreadystatechange = function (ev) {
        if(this.readyState == 4 && this.status == 200){
            if(this.responseText == "error"){
                alert("Ops, error in server. Call krasi.");
            }
            else
            if(this.responseText == "success"){
                alert("Data changed successfully!");
            }
            else{
                alert(this.responseText);
            }

        }
    }
    request.send("user_id=" + user_id + "&height=" + new_height);
}

function loadPageLinks(entries, gender, grade) {
    //get number of all users
    var request = new XMLHttpRequest();
    request.open("get", "controller/countUsersController.php?gender="+gender+"&grade="+grade);
    request.onreadystatechange = function (ev) {
        if(this.readyState == 4 && this.status == 200){
            var allUsers = this.responseText;
            var pages = Math.ceil(allUsers/entries);
            var div = document.getElementById("page-links");
            div.innerHTML = "";
            for(var i = 0; i < pages; i++){
                var button = document.createElement("button");
                button.innerHTML = i+1;
                button.addEventListener("click", function () {

                    var genderSelect = document.getElementById("genders");
                    var gender = genderSelect.options[genderSelect.selectedIndex].value;
                    var gradeSelect = document.getElementById("grades");
                    var grade = gradeSelect.options[gradeSelect.selectedIndex].value;
                    filterUsers(this.innerHTML, entries, gender, grade);
                });
                div.appendChild(button);
            }
        }
    };
    request.send();
    //create link objects and add them to a div below the table
    //set click to each link object to refresh the table invoking loadTableWithUsers
}

function loadGenders() {
    //get number of all users
    var request = new XMLHttpRequest();
    request.open("get", "controller/genderController.php");
    request.onreadystatechange = function (ev) {
        if(this.readyState == 4 && this.status == 200){
            var allGenders = JSON.parse(this.responseText);
            var select = document.getElementById("genders");
            for(var i = 0; i < allGenders.length; i++){
                var gender = allGenders[i];
                select.options[select.options.length] = new Option(gender, gender);
            }
        }
    };
    request.send();
}

function loadGrades() {
    //get number of all users
    var request = new XMLHttpRequest();
    request.open("get", "controller/gradesController.php");
    request.onreadystatechange = function (ev) {
        if(this.readyState == 4 && this.status == 200){
            var allGrades = JSON.parse(this.responseText);
            var select = document.getElementById("grades");
            for(var i = 0; i < allGrades.length; i++){
                var grade = allGrades[i];
                select.options[select.options.length] = new Option(grade, grade);
            }
        }
    };
    request.send();
}

function filter() {
    var genderSelect = document.getElementById("genders");
    var gender = genderSelect.options[genderSelect.selectedIndex].value;
    var gradeSelect = document.getElementById("grades");
    var grade = gradeSelect.options[gradeSelect.selectedIndex].value;
    filterUsers(1, 5, gender, grade);
}

function filterUsers(page, entries, gender, grade) {
    var request = new XMLHttpRequest();
    request.open("get", "controller/filterController.php?page="+page+"&entries="+entries+"&gender="+gender+"&grade="+grade);
    request.onreadystatechange = function (ev) {
        if(this.readyState == 4 && this.status == 200){
            var users = JSON.parse(this.responseText);
            var table = document.getElementById("users-table");
            if(users.length == 0){
                table.innerHTML = "No results!";
            }
            else {
                table.innerHTML = "<tr>\n" +
                    "        <th>Id</th>\n" +
                    "        <th>Username</th>\n" +
                    "        <th>Age</th>\n" +
                    "        <th>City</th>\n" +
                    "        <th>Gender</th>\n" +
                    "        <th>Height</th>\n" +
                    "        <th>Grade</th>\n" +
                    "    </tr>";
                for (var i = 0; i < users.length; i++) {
                    var user = users[i];
                    var row = table.insertRow(i + 1);
                    var cellId = row.insertCell(0)
                    var cellUsername = row.insertCell(1);
                    var cellAge = row.insertCell(2);
                    var cellCity = row.insertCell(3);
                    var cellGender = row.insertCell(4);
                    var cellHeight = row.insertCell(5);
                    var cellGrade = row.insertCell(6);
                    row.style.backgroundColor = user.gender == "M" ? "blue" : "pink";
                    cellId.innerHTML = user.id;
                    cellUsername.innerHTML = user.username;
                    cellAge.innerHTML = user.age;
                    cellCity.innerHTML = user.city;
                    cellGender.innerHTML = user.gender;
                    cellGrade.innerHTML = user.grade;
                    var input = document.createElement("input");
                    input.type = "number";
                    input.value = user.height;
                    input.id = user.id;
                    input.addEventListener('keypress', function (e) {
                        var key = e.which || e.keyCode;
                        if (key === 13) { // 13 is enter
                            updateUser(this.id, this.value);
                        }
                    });
                    cellHeight.appendChild(input)
                }
            }
            loadPageLinks(entries, gender, grade);
        }
    };
    request.send();
}