function validateLogin(event) {

    let login = new OrionAjax('/controllers/user/authenticate/login.php', 'post');
    login.addPostParam('login', form.getValue('login'));
    login.addPostParam('password', form.getValue('password'));
    login.getResponse(callback);

    event.preventDefault();
}

function callback(response) {

    if (JSON.parse(response) === true) {
        window.location.href = "/views/index.php";
    }
    else {
        document.getElementById('invalid_login').style.visibility = 'visible';
    }

}