<?php
/*
 * This represents entities inside the database.
 * e.g. User represents a row in the user table.

 */
namespace model;
class User {
    public int $id;
    public string  $email;
    public string $name;
    public string $bio;
    public string $password;

    public function __construct(int $id, string  $email, string $name, string $bio, string $password) {
        $this->id = $id;
        $this->email = $email;
        $this->name = $name;
        $this->bio = $bio;
        $this->password = $password;
    }
}

class Post {
    public int $id;
    public int $userId;
    public string $text;
    public int $creationDate;

    public function __construct(int $id, int $userId, string $text, int $creationDate) {
        $this->id = $id;
        $this->userId = $userId;
        $this->text = $text;
        $this->creationDate = $creationDate;
    }
}