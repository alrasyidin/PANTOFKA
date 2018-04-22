function sendRegisterRequest(theForm) {

    var firstName = theForm.first_name.value;
    var lastName = theForm.last_name.value;
    var email = theForm.email.value;
    var password = theForm.password.value;
    var passwordRepeat = theForm.password_repeat.value;

    var gender = theForm.gender;
    var genderSelected = gender.options[gender.selectedIndex].value;
    var messageDiv = document.getElementById("register-form-message-div");

    var newUser = {
        'personal_data' : {
            "first_name" : firstName,
            "last_name" : lastName,
            "email" : email,
            "gender" : genderSelected,
            "password" : password

        },
        'security_data' : {
            "password" : password,
            "password_repeat" : passwordRepeat
        }
    };

    var request = new XMLHttpRequest();
    request.open("post", "index.php?r=ajax&target=user&action=register");
    request.onreadystatechange = function (ev) {
        if (this.readyState == 4){

            if(this.status == 200){
                var response = JSON.parse(this.responseText);
                alert("'" + response + "'");
                window.location = "index.php?page=login";

            }else if(this.status === 401 || this.status === 400){

                //insert something user friendly here
                var response = JSON.parse(this.response);
                messageDiv.innerHTML = response.error;
                alert("Something went wrong: '" + response.error + "'");
            }else if(this.status === 500){

                // window.location -> error page
                var response = JSON.parse(this.response);
                alert("Something went wrong: '" + response.error + "'");
            }else{

                alert('undefined ' + this.status );
                var response = JSON.parse(this.responseText);
                console.log(response);
            }

        }

    };
    request.send(JSON.stringify(newUser));

}
