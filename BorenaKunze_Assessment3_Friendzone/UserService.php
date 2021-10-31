<?php
/*
 * All functionalities regarding users.
 * e.g. on line 29 we use verify the user's password.
 */

require_once("db.php");
require_once("base64.php");

class UserService {

    private $conn = null;
    function connectIfNecessary() {
        if ($this->conn == null) {
            $this->conn = connect();
        }
    }

    function register(string $email, string $name, string $password) {

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $this->connectIfNecessary();
        userCreate($this->conn, $email, $name, $hashedPassword);
    }

    function login(string $email, string $password) {
        $this->connectIfNecessary();
        $user = userGetByEmail($this->conn, $email);
        if (!password_verify($password, $user->password)) {
            die("Invalid Password");
        }
        $token = sessionCreate($this->conn, $user->id);
        return base64url_encode($token);
    }

    function logout(string $tokenBase64) {
        $token = base64url_decode($tokenBase64);
        $this->connectIfNecessary();
        return tokenDelete($this->conn, $token);
    }

    function fromSession(string $tokenBase64) {
        $token = base64url_decode($tokenBase64);
        $this->connectIfNecessary();
        return userGetByToken($this->conn, $token);
    }

    public function edit(int $id, $name, $email, $bio)
    {
        $this->connectIfNecessary();
        userEdit($this->conn, $id, $name, $email, $bio);
    }

    public function get(int $id) {
        $this->connectIfNecessary();
        return userGetById($this->conn, $id);
    }
}

