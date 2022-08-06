<?php

    /*
        Авторизация пользователя
        Авторизует пользователя в системе

    */

    class RequestLogin extends AlefRequest
    {
        const KEY_STATUS = "status";
        const KEY_ID = "id";
        const KEY_IS_ACTIVE = "is_active";
        const KEY_MESSAGE = "message";


        public function executeRequest($login, $password)
        {
            $login = (string) $login; // Логин ||| Пример значения: vasya
            $password = (string) $password; // Пароль ||| Пример значения: admin#12345

            // Пишите код только ниже этой строки, чтобы избежать конфликтов при git merge
            
            $this->grantAccess();


            $res = $this->getStub();
            $res[self::KEY_STATUS] = 0;
            return $res;
        }

        public function getStub()
        {
            $res = json_decode('{
    "status": 0,
    "id": 1,
    "is_active": 1
}', true);
            return $res;
        }
    }
