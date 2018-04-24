function validateRegisterFormOnSubmit(theForm) {
    var reason = "";

    reason += validateName(theForm.first_name);
    reason += validateName(theForm.last_name);
    reason += validateEmail(theForm.email);
    reason += validatePasswords(theForm.password , theForm.password_repeat);


    if (reason != "") {

        alert("Some fields need correction:\n" + reason);
        return false;
    }

    return true;

}

function validateLoginFormOnSubmit(theForm) {
    var reason = "";

    reason += validateEmail(theForm.email);
    reason += validatePassword(theForm.password);

    if (reason != "") {
        alert("Some fields need correction:\n" + reason);
        return false;
    }
    return true;
}

function validateEditInfoFormOnSubmit(theForm) {
    var reason = "";

    reason += validateName(theForm.first_name);
    reason += validateName(theForm.last_name);
    reason += validateEmail(theForm.email);

    if (reason != "") {
        alert("Some fields need correction:\n" + reason);
        return false;
    }

    return saveInfoChanges();
}

function validateSecurityFormOnSubmit(theForm) {
    var reason = "";

    reason = validatePassword(theForm.old_password);
    reason = validatePassword(theForm.new_password);
    reason = validatePassword(theForm.repeat_new_password);
    reason += comparePasswords(theForm.new_password , theForm.repeat_new_password);

    if (reason != "") {
        alert("Some fields need correction:\n" + reason);
        return false;
    }

    return saveSecurityChanges();
}

function validateEmpty(fld) {
    fld.style.background = 'White';

    var error = "";

    if (fld.value.length == 0) {
        fld.style.background = '#ffa798';
        error = "The required field has not been filled in.\n"
    } else {
        fld.style.background = 'White';
    }
    return error;
}

function validateName(fld) {
    var error = "";
    var illegalChars = /\W/; // allow letters, numbers, and underscores

    if (fld.value == "") {
        fld.style.background = 'White';

        fld.style.background = '#ffa798';
        error = "You didn't enter a name.\n";
    } else if ((fld.value.length < 5) || (fld.value.length > 45)) {
        fld.style.background = '#ffa798';
        error = "The name is the wrong length.\n";
    } else if (illegalChars.test(fld.value)) {
        fld.style.background = '#ffa798';
        error = "The name contains illegal characters.\n";
    } else {
        fld.style.background = 'White';
    }
    return error;
}

function validatePassword(fld) {
    fld.style.background = 'White';

    var error = "";
    var illegalChars = /[\W_]/; // allow only letters and numbers

    if (fld.value == "") {
        fld.style.background = '#ffa798';
        error = "You didn't enter a password.\n";
    } else if ((fld.value.length < 5) || (fld.value.length > 45)) {
        error = "The password is the wrong length. \n";
        fld.style.background = '#ffa798';
    } else if (illegalChars.test(fld.value)) {
        error = "The password contains illegal characters.\n";
        fld.style.background = '#ffa798';
    } else if (!((fld.value.search(/(a-z)+/)) && (fld.value.search(/(0-9)+/)))) {
        error = "The password must contain at least one numeral.\n";
        fld.style.background = '#ffa798';
    } else {
        fld.style.background = 'White';
    }


    return error;
}

function validatePasswords(password , repeatedPassword){
    var error = "";

    error += validatePassword(password);
    error += validatePassword(repeatedPassword);

    if(error != ""){
        password.style.background  = '#ffa798';
        repeatedPassword.style.background  = '#ffa798';
    }else if (password.value != repeatedPassword.value) {
        error = "Passwords do not match!\n";
        password.style.background  = '#ffa798';
        repeatedPassword.style.background  = '#ffa798';
    } else {
        password.style.background = 'White';
        repeatedPassword.style.background = 'White';
    }
    return error;

}

function trim(s) {
    return s.replace(/^\s+|\s+$/, '');
}

function validateEmail(fld) {
    var error="";
    var tfld = trim(fld.value);                        // value of field with whitespace trimmed off
    var emailFilter = /^[^@]+@[^@.]+\.[^@]*\w\w$/ ;
    var illegalChars= /[\(\)\<\>\,\;\:\\\"\[\]]/ ;

    fld.style.background = 'White';

    if (fld.value == "") {
        fld.style.background  = '#ffa798';
        error = "You didn't enter an email address.\n";
    } else if (!emailFilter.test(tfld)) {              //test email for illegal characters
        fld.style.background  = '#ffa798';
        error = "Please enter a valid email address.\n";
    }else if ((fld.value.length < 5) || (fld.value.length > 45)) {
        error = "The email is the wrong length. \n";
        fld.style.background = '#ffa798';
    } else if (fld.value.match(illegalChars)) {
        fld.style.background  = '#ffa798';
        error = "The email address contains illegal characters.\n";
    } else {
        fld.style.background = 'White';
    }
    return error;
}

