<?php

namespace src\Assistant;

class RequestException extends \AlefException
{
    public function __construct(int $code, string $name, ?string $param = null, ?string $anotherName = null, ?string $value = null)
    {
        $customUserMessage = str_replace(':name', "'$name'", ERRORS[$code][KEY_USER_MESSAGE]);
        if (!empty($param)) {
            $customUserMessage = str_replace(':param', "'$param'", $customUserMessage);
        }
        if (!empty($anotherName)) {
            $customUserMessage = str_replace(':anothername', "'$anotherName'", $customUserMessage);
        }
        if (!empty($value)) {
            $customUserMessage = str_replace(':value', "'$value'", $customUserMessage);
        }
        parent::__construct($code, null, null, $customUserMessage);
    }
}