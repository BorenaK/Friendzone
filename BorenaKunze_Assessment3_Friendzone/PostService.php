<?php
/*
 * Handles all functionality regarding posts
 */

require_once("db.php");
require_once("base64.php");

class PostService
{
    private $conn = null;
    function connectIfNecessary() {
        if ($this->conn == null) {
            $this->conn = connect();
        }
    }
    function postCreate(int $userId, string $text) {
        $this->connectIfNecessary();
        return postCreate($this->conn, $userId, $text);
    }

    function get(int $id) {
        $this->connectIfNecessary();
        return postGet($this->conn, $id);
    }

    function postsGet() {
        $this->connectIfNecessary();
        return postsGetAll($this->conn);
    }

    function postsGetByUser(int $userId) {
        $this->connectIfNecessary();
        return postsGetByUser($this->conn, $userId);
    }
}


