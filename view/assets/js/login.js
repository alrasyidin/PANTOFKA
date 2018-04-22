function sendLoginRequest(theForm){
    var email = theForm.email.value;
    var password = theForm.password.value;

    var user = {
            "email" : email,
            "password" : password
    };

    var request = new XMLHttpRequest();
    request.open("post", "index.php?r=ajax&target=user&action=login");
    request.onreadystatechange = function (ev) {
        if (this.readyState == 4){

            if(this.status == 200){
                window.location = 'index.php?page=edit_profile';

            }else{

                alert(' Status ' + this.status + ';\n Info: ' + this.responseText );

                if(this.status === 401 || this.status === 400){
                    var response = JSON.parse(this.response);
                    alert("Something went wrong: '" + response.error + "'");
                }else if(this.status === 500){
                    var response = JSON.parse(this.response);
                    alert("Something went wrong: '" + response.error + "'");
                }else{
                    var response = JSON.parse(this.responseText);
                    console.log(response);
                }
            }
        }
    };
    request.send(JSON.stringify(user));
}