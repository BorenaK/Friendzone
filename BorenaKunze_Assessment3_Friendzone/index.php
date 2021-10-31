<?php
/*
 * Catch-all handler for urls that are not found.
 * We look at the requested url and render the page that the user is looking for.
 * For example when the user is looking for /post/5 we render the postPage.php template with the data of post 5.
 */
require_once($_SERVER['DOCUMENT_ROOT'] . "/PostService.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/UserService.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/templates/postPage.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/templates/postDelete.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/templates/selfEdit.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/templates/userPage.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/templates/login.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/templates/register.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/templates/home.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/templates/create.php");
$method = $_SERVER["REQUEST_METHOD"];

$path = $_SERVER['REQUEST_URI'];
if ($path[0] == "/") {
    $path = substr($path, 1);
}

$pathParts = explode("/", $path);
$postService = new PostService();
$userService = new UserService();
function auth() {
    if(!isset($_COOKIE["session-token"])) {
        header('Location: /login', true,302);
        exit;
    }
    global $userService;
    $token = $_COOKIE["session-token"];
    return $userService->fromSession($token);
}
$firstPart = $pathParts[0];
if ($firstPart == "post") {
    $currentUser = auth();
    $postId = intval($pathParts[1]);
    $post = $postService->get($postId);
    $poster = $userService->get($post->userId);
    if (sizeof($pathParts) == 2) {
        renderPostPage($post, $poster);
        exit();
    } else {
        $thirdPart = $pathParts[2];
        if ($thirdPart == "") {
            header('Location: /post/' . $postId, true,302);
            exit;
        } elseif ($thirdPart == "delete") {
            renderPostDelete($post, $poster);
            exit();
        }
    }
} elseif ($firstPart == "user") {
    $currentUser = auth();
    $userId = intval($pathParts[1]);
    $user = $userService->get($userId);
    if ($user == null) {
        echo "404 User with id " . $userId . " was not found.";
        exit(404);
    }
    if (sizeof($pathParts) == 2) {
        $posts = $postService->postsGetByUser($userId);
        renderUserPage($user, $posts, false);
        exit();
    } else {
        $thirdPart = $pathParts[2];
        if ($thirdPart == "") {
            header('Location: /user/' . $userId, true,302);
            exit;
        }
    }
} elseif ($firstPart == "my") {
    $currentUser = auth();
    if (sizeof($pathParts) >= 2) {
        $secondPart = $pathParts[1];
        if ($secondPart == "profile") {
            if (sizeof($pathParts) == 2) {
                $posts = $postService->postsGetByUser($currentUser->id);
                renderUserPage($currentUser, $posts, true);
                exit();
            } elseif (sizeof($pathParts) == 3) {
                $thirdPart = $pathParts[2];
                if ($thirdPart == "edit") {
                    if ($method == "POST") {
                        $token = $_COOKIE["session-token"];
                        $name = $_POST["name"];
                        $email = $_POST["email"];
                        $bio = $_POST["bio"];
                        $userService = new UserService();
                        $user = $userService->fromSession($token);
                        $userService->edit($user->id, $name, $email, $bio);
                        header('Location: /my/profile', true,302);
                        exit;
                    } elseif ($method == "GET") {
                        renderSelfEdit($currentUser);
                        exit();
                    }
                } elseif ($thirdPart == "") {
                    header('Location: /my/profile', true,302);
                    exit;
                }
            }
        }
    }
} elseif ($firstPart == "logout") {
    $currentUser = auth();
    if ($method == "POST") {
        $token = $_COOKIE["session-token"];
        $userService = new UserService();
        $userService->logout($token);
        setcookie("session-token", "", time()+1, "/", null, false, true);
        header('Location: /login', true,302);
        exit;
    }
} elseif ($firstPart == "login") {
    if ($method == "POST") {
        $email = $_POST["email"];
        $password = $_POST["password"];
        $token = $userService->login($email, $password);
        setcookie("session-token", $token, time()+60*60*24*30, "/", null, false, true);
        header('Location: /', true,302);
        exit;
    } elseif ($method == "GET") {
        renderLogin();
        exit();
    }
} elseif ($firstPart == "register") {
    if ($method == "POST") {
        $email = $_POST["email"];
        $name = $_POST["name"];
        $password = $_POST["password"];
        $passwordRepeat = $_POST["password-repeat"];

        if ($password != $passwordRepeat) {
            die("Passwords are not the same");
        }

        $userService = new UserService();
        $userService->register($email, $name, $password);

        $token = $userService->login($email, $password);
        setcookie("session-token", $token, time()+60*60*24*30, "/", null, false, true);
        header('Location: /', true,302);
        exit;
    } elseif ($method == "GET") {
        renderRegister();
        exit();
    }
} elseif ($firstPart == "") {
    $currentUser = auth();
    $posts = $postService->postsGet();
    $postsAndPosters = [];
    foreach ($posts as $post) {
        $poster = $userService->get($post->userId);
        array_push($postsAndPosters, (object)["post" => $post, "poster" => $poster]);
    }
    renderHome($postsAndPosters, $currentUser);
    exit();
} elseif ($firstPart == "create") {
    $currentUser = auth();
    if ($method == "POST") {
        $token = $_COOKIE["session-token"];
        $text = $_POST["text"];
        $userService = new UserService();
        $postService = new PostService();
        $user = $userService->fromSession($token);
        $postId = $postService->postCreate($user->id, $text);
        header('Location: /post/' . $postId, true,302);
        exit();
    } elseif ($method == "GET") {
        renderCreate();
        exit();
    }
}
echo "404 not found";
exit(404);
