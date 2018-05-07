function showEditProfileSection(showInfo , showSecurity) {
    var resultDiv = document.getElementById('edit-form-result');
    var infoSection = document.getElementById('edit-info-div');
    var securitySection = document.getElementById('edit-security-div');

    var sections = document.getElementById("sections");
    sections.style.visibility = "visible";
    sections.style.display = "block";
    infoSection.style.visibility = 'hidden';
    infoSection.style.display = 'none';
    infoSection.style.visibility = 'hidden';
    infoSection.style.display = 'none';
    resultDiv.innerHTML = '';

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
    request.open("get", "handle_requests.php?target=user&action=getLoggedUserAsJson");
    request.onreadystatechange = function (ev) {
        if(this.readyState == 4){
            if(this.status == 200){
                var data = JSON.parse(this.response);

                console.log(data);
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
    var resultDiv = document.getElementById('edit-form-result');
    resultDiv.innerHTML = '';
    var firstName = document.getElementById('edit-info-form').first_name.value;
    var lastName = document.getElementById('edit-info-form').last_name.value;
    var email = document.getElementById('edit-info-form').email.value;

    var gender = document.getElementById("edit-form-gender");
    var genderSelected = gender.options[gender.selectedIndex].value;

    var changedData = {
        "first_name" : firstName,
        "last_name" : lastName,
        "email" : email,
        "gender" : genderSelected
    };

    var request = new XMLHttpRequest();
    request.open("post", "handle_requests.php?target=user&action=edit&tab=info");
    request.onreadystatechange = function (ev) {
        if(this.readyState == 4 ){
            if(this.status == 200){
                resultDiv.innerHTML = this.responseText;
                return true;
            }else{
                alert( this.responseText );
            }
        }
    };
    request.send(JSON.stringify(changedData));
}

function saveSecurityChanges(){
    var resultDiv = document.getElementById('edit-form-result');
    resultDiv.innerHTML = '';

    var oldPassword = document.getElementById('edit-security-form-old').value;
    var newPassword = document.getElementById('edit-security-form-new').value;
    var newPasswordRepeat = document.getElementById('edit-security-form-repeat').value;

    var changedPasswords = {
        "old_password" : oldPassword,
        "new_password" : newPassword,
        "new_password_repeat" : newPasswordRepeat
    };

    var request = new XMLHttpRequest();
    request.open("post", "handle_requests.php?target=user&action=edit&tab=security");
    request.onreadystatechange = function (ev) {
        if(this.readyState == 4 ){
            if(this.status == 200){
                resultDiv.innerHTML = this.responseText;
            }else{
                alert(this.status + " :\n" + this.statusText);
            }
        }
    };
    request.send(JSON.stringify(changedPasswords));
}

function validateEditInfoFormOnSubmit(theForm) {
    var resultDiv = document.getElementById('edit-form-result');
    var reason = "";

    reason += validateName(theForm.first_name);
    reason += validateName(theForm.last_name);
    reason += validateEmail(theForm.email);

    if (reason != "") {
        resultDiv.innerHTML = "";
        resultDiv.innerHTML = "Some fields need correction:\n" + reason;
        return false;
    }

    return saveInfoChanges();
}

function validateSecurityFormOnSubmit(theForm) {
    var resultDiv = document.getElementById('edit-form-result');

    var reason = "";
    reason += comparePasswords(theForm.new_password , theForm.repeat_new_password);
    reason += validatePassword(theForm.old_password);
    reason += validatePassword(theForm.new_password);
    reason += validatePassword(theForm.repeat_new_password);

    if (reason != "") {
        resultDiv.innerHTML = "";
        resultDiv.innerHTML = "Some fields need correction:\n" + reason;
        return false;
    }
    return saveSecurityChanges();
}