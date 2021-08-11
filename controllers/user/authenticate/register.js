function validateRegistration(event) {

    document.getElementById('registration_error').style.visibility = 'hidden';

    let valid = true;

    // validate username
    let username = form.getValue('username');
    if (!/^[a-zA-Z0-9_]+$/.test(username) || !/^[a-zA-Z0-9]/.test(username) ||
        username.length <= 4 || username.length >= 20) {
        document.getElementById('username_helper').style.color = 'red';
        valid = false;
    }
    else {
        document.getElementById('username_helper').style.color = 'grey';
        valid = true;
    }

    // validate email
    if (!/[a-zA-Z0-9.]+[@]+[a-zA-Z0-9]+[.]+[a-zA-Z]{2,6}/.test(form.getValue('email'))) {
        document.getElementById('email_helper').style.visibility = 'visible';
        document.getElementById('email_helper').style.color = 'red';
        valid = false;
    }
    else {
        document.getElementById('email_helper').style.visibility = 'hidden';
        document.getElementById('email_helper').style.color = 'grey';
        valid = true;
    }

    // validate password
    let password = form.getValue('password');
    if (!/^[a-zA-Z0-9~`!@#$%^&*()\-=_+{}|\[\]\\:";'<>?,.\/]+$/.test(password) || password.length < 8 && password.length > 20) {
        document.getElementById('password_helper').style.color = 'red';
        valid = false;
    }
    else {
        document.getElementById('password_helper').style.color = 'grey';
        valid = true;
    }

    // validate confirm password
    if (form.getValue('password') !== form.getValue('confirm_password')) {
        document.getElementById('confirm_password_helper').style.color = 'red';
        valid = false;
    }
    else {
        document.getElementById('confirm_password_helper').style.color = 'grey';
        valid = true;
    }

    /*
    if (document.getElementById('terms').checked === false) {
        document.getElementById('terms_helper').style.visibility = 'visible';
        valid = false;
    }
    else {
        document.getElementById('terms_helper').style.visibility = 'hidden';
        valid = true;
    }
    */

    if (valid === true) {
        let login = new OrionAjax('/controllers/user/authenticate/register.php', 'post')
        login.addPostParam('username', form.getValue('username'));
        login.addPostParam('email', form.getValue('email'));
        login.addPostParam('password', form.getValue('password'));
        login.getResponse(callback);
        event.preventDefault();
    }

    event.preventDefault();
}


function callback(response) {
    let result = JSON.parse(response);
    if (result === true) {
        window.location.href = "/views/index.php";
    }
    else {
        document.getElementById('registration_error').style.visibility = 'visible';
        document.getElementById('registration_error').innerText = result;
    }

}