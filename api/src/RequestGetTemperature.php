<?php

    /*
        Температура в Москве
        Получаем температуру в Москве.

    */

    class RequestGetTemperature extends AlefRequest
    {
        const KEY_STATUS = "status";
        const KEY_TEMPERATURE = "temperature";


        public function executeRequest()
        {

            // Пишите код только ниже этой строки, чтобы избежать конфликтов при git merge
            
            $res = $this->getStub();
            $res[self::KEY_STATUS] = 0;
            return $res;
        }

        public function getStub()
        {
            $res = json_decode('{
    "status": 0,
    "temperature": "16"
}', true);
            return $res;
        }
    }
