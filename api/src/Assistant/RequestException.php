<?php

namespace src\Assistant;

class RequestException extends \AlefException
{
    public function __construct(int $code, string $name, ?string $param = null)
    {
        $customUserMessage = str_replace(':name', "'$name'", ERRORS[$code][KEY_USER_MESSAGE]);
        if ($param) {
            $customUserMessage = str_replace(':param', "'$param'", $customUserMessage);
        }
        parent::__construct($code, null, null, $customUserMessage);
    }
}