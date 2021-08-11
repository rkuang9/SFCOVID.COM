<?php

require_once "OrionRecord.php";

/**
 * Putting all these @properties here so all those weird highlight colors don't appear
 *
 * @property string|null record_id
 * @property string|null username
 * @property string|null password
 * @property string|null email
 * @property string|null first_name
 * @property string|null last_name
 * @property bool|null   admin
 */
class OrionAuth extends OrionRecord {

    private ?string $login;      // is either the username or email, used in login()

    /**
     * OrionAuth constructor. Can set parameters on object instantiation.
     * @param string|null $login,       is either the username or password
     * @param string|null $password,    user password
     */
    function __construct(string $login = null, string $password = null)
    {
        parent::__construct('user');
        $this->login = $login;
        $this->password = $password;
    }



    /**
     * Validate the email, username, and password, then insert the record into the user table.
     *
     * @return bool,        return TRUE if insert successful, FALSE on failure
     */
    function register() {
        // check for valid unused username
        if (!isset($this->username)) {
            //return 101;
            return "Invalid username";
        }
        if (!$this->isValidUserName($this->username)) {
            //return 100;
            return "Invalid username";
        }
        if ($this->alreadyInUse('username', $this->username)) {
            //return 102;
            return "This username is already in use";
        }

        // check for valid unused email
        if (!isset($this->email)) {
            //return 200;
            return "Invalid email";
        }
        if (!$this->isValidEmail($this->email)) {
            //return 201;
            return "Invalid email";
        }
        if ($this->alreadyInUse('email', $this->email)) {
            //return 202;
            return "This email address is already in use";
        }

        // check for a valid password
        if (!isset($this->password)) {
            return "Invalid password";
            //return 300;
        }
        if (!$this->isValidPassword($this->password)) {
            return "Invalid password";
            //return 301;
        }

        $this->password = password_hash($this->password, PASSWORD_BCRYPT );
        $this->admin = 'false';

        $result = $this->insert();

        if ($result) {
            $this->setSession();
            return true;
        }

        return false;
    }



    /**
     * Log in the user by searching the user table for the provided username then verifying the password
     */
    function login() {
        // copy $this->password to another variable because the default fetch_into query will overwrite it
        $provided_password = $this->password;

        $this->addQuery('username', '=', $this->login, '(');
        $this->addOrQuery('email', '=', $this->login, ')');
        $this->addQuery('username', '!=', '');
        $this->addQuery('email', '!=', '');
        $this->query();

        if ($this->next() && $this->row_count == 1 && password_verify($provided_password, $this->password)) {

            $this->setSession();
            unset($provided_password);
            return true;
        }
        else if ($this->row_count > 1) {
            OrionRecord::logIssue('Error', 'user', "Username $this->username or email $this->email has duplicate records.", '');
            return false;
        }
        else {
            return false;
        }
    }



    /**
     * Log out the user by destroying the session.
     * Taken word for word from: https://www.php.net/session_destroy
     */
    static function logout() {
        // Initialize the session.
        // If you are using session_name("something"), don't forget it now!
        if (session_status() != 2) {
            session_start();
        }

        // Unset all of the session variables.
        $_SESSION = array();

        // If it's desired to kill the session, also delete the session cookie.
        // Note: This will destroy the session, and not just the session data!
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }

        // Finally, destroy the session.
        session_destroy();
    }



    /**
     * Set session variables, typically after registration or login
     */
    function setSession() {
        session_start();
        //$_SESSION['record_id'] = $this->record_id;
        $_SESSION['username'] = $this->username;
        $_SESSION['email'] = $this->email;
        $_SESSION['admin'] = $this->admin;

        if (isset($_SESSION['first_name'])) {
            $_SESSION['first_name'] = $this->first_name;
        }

        if (isset($_SESSION['first_name'])) {
            $_SESSION['last_name'] = $this->last_name;
        }
    }



    /**
     * Check if an email or username is already in use.
     *
     * @param $column,  column name
     * @param $value,   column value
     * @return bool,    return TRUE is a record with the column and value already exists
     */
    function alreadyInUse($column, $value) {
        $this->newQuery();
        $this->addQuery($column, '=', $value);
        $this->query('into');

        return $this->row_count > 0;
    }



    /**
     * Validate that the username starts with a letter, is alphanumeric, and can contain underscores
     * Max length 20
     *
     * @param $username,        registering user's username
     * @return bool,            return TRUE if valid, false if invalid
     */
    function isValidUsername($username) {
        return preg_match('/^[a-zA-Z0-9][a-zA-Z0-9_]+$/', $username) === 1 && strlen($username) <= 20 && strlen($username) >= 4;
    }



    /**
     * @param $email,           registering user's email
     * @return string|bool,     return string of email if TRUE, false if invalid email
     */
    function isValidEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }



    /**
     * Validate a password. Allows alphanumeric and `~!@#$%^&*()_+-={}|[]\:";'<>?,./
     * Max length 20
     *
     * @param $password,        registering user's password
     * @return bool,            return TRUE if valid, false if invalid
     */
    function isValidPassword($password) {
        return preg_match("/^[a-zA-Z0-9~`!@#$%^&*()\-=_+{}|\[\]\\\:\";'<>?,.\/]+$/", $password) === 1 && strlen($password) <= 20 && strlen($password) >= 8;
    }
}
