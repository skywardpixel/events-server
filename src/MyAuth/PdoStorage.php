<?php
namespace App\MyAuth;

class PdoStorage extends \OAuth2\Storage\Pdo {
    protected function checkPassword($user, $pwd) {
        return password_verify($pwd, $user['password']);
    }
}
