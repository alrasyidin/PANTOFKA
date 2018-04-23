function showEditProfileSection(showInfo , showSecurity) {

    var infoSection = document.getElementById('edit-info-div');
    var securitySection = document.getElementById('edit-security-div');

    var sections = document.getElementById("sections");
    sections.style.visibility = "visible";
    sections.style.display = "block";
    infoSection.style.visibility = 'hidden';
    infoSection.style.display = 'none';
    infoSection.style.visibility = 'hidden';
    infoSection.style.display = 'none';

    if (showInfo !== false ){

        infoSection.style.visibility = 'hidden';
        securitySection.style.visibility = 'hidden';

        infoSection.style.visibility = 'visible';
        infoSection.style.display = 'block';

        securitySection.style.visibility = 'hidden';
        securitySection.style.display = 'none';

    } else if (showSecurity !== false){
        securitySection.style.visibility = 'visible';
        securitySection.style.display = 'block';

        infoSection.style.visibility = 'hidden';
        infoSection.style.display = 'none';


    }else {

        infoSection.style.visibility = 'visible';
        infoSection.style.display = 'block';

        securitySection.style.visibility = 'hidden';
        securitySection.style.display = 'none';

    }
}

function loadSessionUserDataInEditForm(){
    var request = new XMLHttpRequest();
    request.open("get", "index.php?r=ajax&target=user&action=getLoggedUser");
    request.onreadystatechange = function (ev) {
        if(this.readyState == 4){
            if(this.status == 200){

                var data = JSON.parse(this.responseText);
                document.getElementById("edit-form-f-name").value = data.first_name;
                document.getElementById("edit-form-l-name").value = data.last_name;
                document.getElementById("edit-form-email").value = data.email;

                var gender = data.gender;
                var genderSelect = document.getElementById('edit-form-gender');

                for(var i, j = 0; i = genderSelect.options[j]; j++) {
                    if(i.value == gender) {
                        genderSelect.selectedIndex = j;
                        break;
                    }
                }
            }else{
                alert(this.status + "\n" + this.statusText);
            }
        }
    };
    request.send();
}

function saveInfoChanges(){
    var firstName = document.getElementById('edit-info-form').first_name.value;
    var lastName = document.getElementById('edit-info-form').last_name.value;
    var email = document.getElementById('edit-info-form').email.value;

    var gender = document.getElementById("edit-form-gender");
    var genderSelected = gender.options[gender.selectedIndex].value;

    var newUserData = {
        "first_name" : firstName,
        "last_name" : lastName,
        "email" : email,
        "gender" : genderSelected
    };

    var request = new XMLHttpRequest();
    request.open("post", "index.php?r=ajax&target=user&action=edit&tab=info");
    request.onreadystatechange = function (ev) {
        if(this.readyState == 4 ){
            if(this.status == 200){
                alert(this.responseText);

            }else{
                alert(this.status + " :\n" + this.statusText);

            }
        }
    };
    request.send(JSON.stringify(newUserData));
}

function saveSecurityChanges(){

    var oldPassword = document.getElementById('edit-security-form-old').value;
    var newPassword = document.getElementById('edit-security-form-new').value;

    var passwords = {
        "old_password" : oldPassword,
        "new_password" : newPassword
    };

    var request = new XMLHttpRequest();
    request.open("post", "index.php?r=ajax&target=user&action=edit&tab=security");
    request.onreadystatechange = function (ev) {
        if(this.readyState == 4 ){
            if(this.status == 200){
                alert(this.responseText);

            }else{
                alert(this.status + " :\n" + this.statusText);

            }
        }
    };
    request.send(JSON.stringify(passwords));
}