<?php

    /*
        Регистрация нового пользователя
        Регистрирует нового пользователя в системе.

    */

use src\Assistant\Request;

class RequestRegister extends AlefRequest
    {
        const KEY_ID = "id";
        const KEY_STATUS = "status";
        const KEY_MESSAGE = "message";


        public function executeRequest($login, $password, $firstname, $lastname)
        {
            $login = (string) $login; // Логин ||| Пример значения: vasya
            $password = (string) $password; // Пароль ||| Пример значения: admin#12345
            $firstname = (string) $firstname; // Имя ||| Пример значения: Вася
            $lastname = (string) $lastname; // Фамилия ||| Пример значения: Пупкин

            // Пишите код только ниже этой строки, чтобы избежать конфликтов при git merge

            $request = new Request();
            $data = $request->validated([
                'login' => ['required', 'alpha', 'min:8']
            ]);

            $this->grantAccess();


            $res = $this->getStub();
            $res[self::KEY_STATUS] = 0;
            return $res;
        }

        public function getStub()
        {
            $res = json_decode('{
    "id": 1,
    "status": 0
}', true);
            return $res;
        }
    }
