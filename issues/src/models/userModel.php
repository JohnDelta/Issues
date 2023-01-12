<?php

class User {

    public $user_id;
    public $username;
    public $password;

    public function __construct($user_id, $username, $password) {
        $this->user_id = $user_id;
        $this->username = $username;
        $this->password = $password;
    }

    public function sanitize() {
        $this->username = htmlspecialchars(strip_tags($this->username));
        $this->password = htmlspecialchars(strip_tags($this->password));
    }

}

?>