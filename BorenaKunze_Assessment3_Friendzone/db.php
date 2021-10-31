<?php
/*
 * All SQL Queries
 */

require_once("model.php");

use model\Post;
use model\User;

function connect() {
    $servername = "localhost";
    $username = "department_app";
    $password = "uIs8EBL)_9NnmP0_";
    $conn = new mysqli($servername, $username, $password, "friendzone");

// Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    return $conn;
}

function userCreate(mysqli $conn, string $email, string $name, string $password) {
    $stmt = mysqli_prepare($conn, "INSERT INTO `user`(`email`, `name`, `password`) VALUES (?,?,?)");
    mysqli_stmt_bind_param($stmt, "sss", $email, $name, $password);
    $result = mysqli_stmt_execute($stmt );
    if ($result == false) {
        die("query failed");
    }
}

function userGet(mysqli $conn, string $joinClause, string $whereClause, $bindParams) {
    $stmt = mysqli_prepare($conn, "SELECT u.`id`, u.`email`, u.`name`, u.`bio`, u.`password` FROM `user` u " . $joinClause . " WHERE " . $whereClause);
    $bindParams($stmt);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result == false) {
        die("query failed");
    }
    $row = $result->fetch_assoc();
    if($row == null) {
        return null;
    }
    return new User($row["id"], $row["email"], $row["name"], $row["bio"], $row["password"]);
}

function userGetByEmail(mysqli $conn, string $email) {
    return userGet($conn, "", "email = ?", function ($stmt) use ($email) {
        mysqli_stmt_bind_param($stmt, "s", $email);
    });
}
function userGetById(mysqli $conn, int $id) {
    return userGet($conn, "", "id = ?", function ($stmt) use ($id) {
        mysqli_stmt_bind_param($stmt, "i", $id);
    });
}

function userGetByToken(mysqli $conn, string $token) {
    return userGet($conn, "INNER JOIN `session` s ON u.id = s.user", "s.token = ?", function ($stmt) use ($token) {
        mysqli_stmt_bind_param($stmt, "s", $token);
    });
}

function tokenDelete(mysqli $conn, string $token) {
    $stmt = mysqli_prepare($conn, "DELETE FROM `session` WHERE `session`.`token` = ?");
    mysqli_stmt_bind_param($stmt, "s", $token);
    $result = mysqli_stmt_execute($stmt);
    if ($result == false) {
        die("query failed");
    }
}

function userEdit(mysqli $conn, int $userId, string $name, string $email, string $bio) {
    $stmt = mysqli_prepare($conn, "UPDATE `user` SET `name` = ?, email = ?, `bio` = ? WHERE `user`.`id` = ?");
    mysqli_stmt_bind_param($stmt, "sssi", $name, $email, $bio, $userId);
    $result = mysqli_stmt_execute($stmt);
    if ($result == false) {
        die("query failed");
    }
}

function sessionCreate(mysqli $conn, int $userId) {
    $token = openssl_random_pseudo_bytes(16);
    $stmt = mysqli_prepare($conn, "INSERT INTO `session`(`user`, `token`) VALUES (?,?)");
    mysqli_stmt_bind_param($stmt, "is", $userId, $token);
    $result = mysqli_stmt_execute($stmt);
    if ($result == false) {
        die("query failed");
    }
    return $token;
}

function postCreate(mysqli $conn, int $userId, string $text) {
    $stmt = mysqli_prepare($conn, "INSERT INTO `post`(`user`, `text`) VALUES (?,?)");
    mysqli_stmt_bind_param($stmt, "is", $userId, $text);
    $result = mysqli_stmt_execute($stmt);
    if ($result == false) {
        die("query failed");
    }
    return $conn->insert_id;
}

function postFromRow($row): Post
{
    return new Post($row["id"], $row["user"], $row["text"], $row["creation_date"]);
}

function postsGet(mysqli $conn, string $joinClause, string $whereClause, $bindParams) {
    $stmt = mysqli_prepare($conn, "SELECT p.`id`, p.`user`, p.`text`, UNIX_TIMESTAMP(p.`creation_date`) as creation_date FROM `post` p " . $joinClause . " WHERE " . $whereClause . " ORDER BY p.creation_date DESC");
    $bindParams($stmt);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result == false) {
        die("query failed");
    }
    $row = $result->fetch_assoc();

    $posts = array();
    while ($row != null) {
        array_push($posts, postFromRow($row));
        $row = $result->fetch_assoc();
    }
    return $posts;
}

function postGet(mysqli $conn, int $id) {
    return postsGet($conn, "", "p.id = ?", function ($stmt) use ($id) {
        mysqli_stmt_bind_param($stmt, "i", $id);
    })[0];
}

function postsGetAll(mysqli $conn) {
    return postsGet($conn, "", "1", function () {});
}

function postsGetByUser(mysqli $conn, int $userId) {
    return postsGet($conn, "", "p.user = ?", function ($stmt) use ($userId) {
        mysqli_stmt_bind_param($stmt, "i", $userId);
    });
}