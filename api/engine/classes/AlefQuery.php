<?php

require_once __DIR__ . "/AlefCore.php";

// Класс php-интерфейс, позволяющий обращаться к API. Например, может быть использован для подключения и использования на сайте, дублирующем функции приложения

class AlefQuery
{
    public static function requestGetTemperature($lang = null)
    {
        return AlefCore::executeRequest("getTemperature", $lang, []);
    }

    public static function requestLogout($lang = null)
    {
        return AlefCore::executeRequest("logout", $lang, []);
    }

    public static function requestLogin($login, $password, $lang = null)
    {
        return AlefCore::executeRequest("login", $lang, ["login" => $login, "password" => $password]);
    }

    public static function requestRegister($login, $password, $firstname, $lastname, $lang = null)
    {
        return AlefCore::executeRequest("register", $lang, ["login" => $login, "password" => $password, "firstname" => $firstname, "lastname" => $lastname]);
    }
}
