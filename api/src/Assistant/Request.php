<?php

namespace src\Assistant;

class Request
{
    private array $input;

    public function __construct()
    {
        $this->input = json_decode(file_get_contents('php://input'), true);
    }

    /** Возвращает сырые данные.
     * @return array
     */
    public function all(): array
    {
        return $this->input;
    }

    /** Валидирует входящие данные согласно правилам.
     * @param array $rules Правила валидации.
     * @return array
     */
    public function validated(array $rules): array
    {
        $result = [];
        $prefix = 'validate';
        foreach ($rules as $name => $list) {
            foreach ($list as $rule) {
                $request = $this->input[$name] ?? null;
                $arr = explode(':', $rule);
                $params = $arr[1] ?? null;
                $method = $prefix . ucfirst($arr[0]);
                if (method_exists(self::class, $method)) {
                    self::$method($name, $request, $params);
                }
                $result[$name] = $request;
            }
        }

        return $result;
    }

    /** Поле для проверки должно быть "yes", "on", 1 или <i>true</i>.
     * Это полезно для проверки принятия "Условий предоставления услуг" или аналогичных полей.
     *     Использование 'accepted'
     * @param string      $name
     * @param             $request
     * @param string|null $param
     * @return void
     * @throws RequestException
     */
    private function validateAccepted(string $name, $request, ?string $param = null): void
    {
        if (gettype($request) === 'string') {
            $request = strtolower($request);
        }
        if ($request !== 'on' && $request !== 'yes' && $request !== 1 && $request !== true) {
            throw new RequestException(ERR_VALIDATOR_ACCEPTED, $name);
        }
    }

    /** Поле для проверки должно быть "yes", "on", 1 или <i>true</i>, если другое поле для проверки равно указанному значению.
     * Это полезно для проверки принятия "Условий предоставления услуг" или аналогичных полей.
     *     Использование 'accepted_if:поле,значение'
     * @param string      $name
     * @param             $request
     * @param string|null $param
     * @return void
     * @throws RequestException
     */
    private function validateAccepted_if(string $name, $request, ?string $param = null): void
    {
        if (empty($param)) {
            return;
        }
        $arr = explode(',', $param);
        $anotherName = $arr[0] ?? false;
        $value = $arr[1] ?? false;
        if ($anotherName && $value && isset($this->input[$anotherName]) && $this->input[$anotherName] === $value) {
            if (gettype($request) === 'string') {
                $request = strtolower($request);
            }
            if ($request !== 'on' && $request !== 'yes' && $request !== 1 && $request !== true) {
                throw new RequestException(ERR_VALIDATOR_ACCEPTED_IF, $name, null, $anotherName, $value);
            }
        }
    }

    /** Поле при проверке должно иметь допустимую запись A или AAAA в соответствии с функцией dns_get_record PHP.
     * Имя хоста предоставленного URL извлекается с помощью функции parse_urlPHP перед передачей dns_get_record.
     *     Использование 'active_url'
     * @param string      $name
     * @param             $request
     * @param string|null $param
     * @return void
     * @throws RequestException
     */
    private function validateActive_url(string $name, $request, ?string $param = null): void
    {
        //TODO Описать функцию active_url
    }

    /** Поле при проверке должно быть значением после заданной даты. Даты будут переданы в функцию strtotime PHP
     * для преобразования в действительный DateTime экземпляр
     *     Использование 'after:date'
     * @param string      $name
     * @param             $request
     * @param string|null $param
     * @return void
     * @throws RequestException
     */
    private function validateAfter(string $name, $request, ?string $param = null): void
    {
        //TODO Описать функцию after:date
    }

    /** Поле для проверки должно иметь значение после или равное заданной дате.
     * Дополнительные сведения см. в функции правила 'after:date'.
     *     Использование 'after_or_equal:date'
     * @param string      $name
     * @param             $request
     * @param string|null $param
     * @return void
     * @throws RequestException
     */
    private function validateAfter_or_equal(string $name, $request, ?string $param = null): void
    {
        //TODO Описать функцию after_or_equal:date
    }
    
    /** Поле для проверки должно состоять только из буквенных символов.
     *     Использование 'alpha'
     * @param string      $name
     * @param             $request
     * @param string|null $param
     * @return void
     * @throws RequestException
     */
    private function validateAlpha(string $name, $request, ?string $param = null): void
    {
        if (!preg_match('/^\pL+$/u', $request)) {
            throw new RequestException(ERR_VALIDATOR_ALPHA, $name);
        }
    }

    /** Поле для проверки должно состоять только из буквенных символов и символов подчеркивания.
     *     Использование 'alpha_dash'
     * @param string      $name
     * @param             $request
     * @param string|null $param
     * @return void
     * @throws RequestException
     */
    private function validateAlpha_dash(string $name, $request, ?string $param = null): void
    {
        if (!preg_match('/^[\pL_]+$/u', $request)) {
            throw new RequestException(ERR_VALIDATOR_ALPHA_DASH, $name);
        }
    }

    /** Поле для проверки должно состоять только из буквенных символов и пробелов.
     *     Использование 'alpha_space'
     * @param string      $name
     * @param             $request
     * @param string|null $param
     * @return void
     * @throws RequestException
     */
    private function validateAlpha_space(string $name, $request, ?string $param = null): void
    {
        if (!preg_match('/^[\pL\s]+$/u', $request)) {
            throw new RequestException(ERR_VALIDATOR_ALPHA_SPACE, $name);
        }
    }

    /** Поле для проверки должно иметь минимальное значение.
     * Строки, числа, массивы и файлы оцениваются таким же образом, как 'size' правило.
     *     Использование 'min:size'
     * @param string      $name
     * @param             $request
     * @param string|null $param
     * @return void
     * @throws RequestException
     */
    private function validateMin(string $name, $request, ?string $param = null): void
    {
        // TODO Сделать проверку размера файла
        switch (gettype($request)) {
            case 'array':
                if (count($request) < $param) {
                    throw new RequestException(ERR_VALIDATOR_MIN_COUNT, $name, $param);
                }
                return;
            case 'integer':
                if ($request < $param) {
                    throw new RequestException(ERR_VALIDATOR_MIN_INTEGER, $name, $param);
                }
                return;
            case 'string':
                if (strlen($request) < $param) {
                    throw new RequestException(ERR_VALIDATOR_MIN_STRING, $name, $param);
                }
                return;
        }
        throw new RequestException(ERR_VALIDATOR_MIN, $name, $param);
    }

    /** Поле для проверки должно присутствовать во входных данных и не быть пустым.
     * Поле считается "пустым", если выполняется одно из следующих условий:
     * - Значение null.
     * - Значение представляет собой пустую строку.
     * - Значение представляет собой пустой массив или пустой Countable объект.
     * - Значение представляет собой загруженный файл без пути.
     *         Использование 'required'
     * @param string      $name
     * @param             $request
     * @param string|null $param
     * @return void
     * @throws RequestException
     */
    private function validateRequired(string $name, $request, ?string $param = null): void
    {
        if (empty($request)) {
            throw new RequestException(ERR_VALIDATOR_REQUIRED, $name);
        }
    }

    /** Поле для проверки должно быть строкой.
     *     Использование 'string'
     * @param string      $name
     * @param             $request
     * @param string|null $param
     * @return void
     * @throws RequestException
     */
    private function validateString(string $name, $request, ?string $param = null): void
    {
        if (gettype($request) !== 'string') {
            throw new RequestException(ERR_VALIDATOR_STRING, $name);
        }
    }

    /** Поле для проверки должно быть массивом и содержать только перечисленные поля.
     *     Использование 'array:field1,...'
     * @param string      $name
     * @param             $request
     * @param string|null $param
     * @return void
     * @throws RequestException
     */
    private function validateArray(string $name, $request, ?string $param = null): void
    {
        if (gettype($request) !== 'array') {
            throw new RequestException(ERR_VALIDATOR_ARRAY, $name);
        }
        if (!$param) {
            return;
        }
        $list = explode(',', $param);
        foreach ($list as $item) {
            if (!isset($request[$item])) {
                throw new RequestException(ERR_VALIDATOR_ARRAY_FIELD, $name, $item);
            }
        }
        foreach ($request as $key => $item) {
            if (!in_array($key, $list, true)) {
                throw new RequestException(ERR_VALIDATOR_ARRAY_EXCESS, $name, $key);
            }
        }
    }

    /** Поле для проверки должно быть целым числом.
     *     Использование 'integer'
     * @param string      $name
     * @param             $request
     * @param string|null $param
     * @return void
     * @throws RequestException
     */
    private function validateInteger(string $name, $request, ?string $param = null): void
    {
        if (gettype($request) !== 'integer') {
            throw new RequestException(ERR_VALIDATOR_INTEGER, $name);
        }
    }

    /** Поле при проверке должно иметь возможность преобразования в логическое значение.
     * Допустимыми входными данными являются true, false, 1, 0, "1", и "0".
     *     Использование 'boolean'
     * @param string      $name
     * @param             $request
     * @param string|null $param
     * @return void
     * @throws RequestException
     */
    private function validateBoolean(string $name, $request, ?string $param = null): void
    {
        if (gettype($request) !== 'boolean' && $request !== 1 && $request !== 0 && $request !== '1' && $request !== '0') {
            throw new RequestException(ERR_VALIDATOR_BOOLEAN, $name);
        }
    }
}