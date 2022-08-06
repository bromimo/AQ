<?php

    /*
        Разлогинивание
        Разлогинивает пользователя

    */

    class RequestLogout extends AlefRequest
    {
        const KEY_STATUS = "status";
        const KEY_MESSAGE = "message";


        public function executeRequest()
        {

            // Пишите код только ниже этой строки, чтобы избежать конфликтов при git merge
            
            $this->revokeAccess();


            $res = $this->getStub();
            $res[self::KEY_STATUS] = 0;
            return $res;
        }

        public function getStub()
        {
            $res = json_decode('{
    "status": 0
}', true);
            return $res;
        }
    }
