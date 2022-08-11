<?php

namespace src\Assistant;

use Exception;
use Symfony\Component\HttpFoundation\File\File;

class Request
{
    private array $input;

    const TYPE_NUMERIC = 'numeric';
    const TYPE_ARRAY = 'array';
    const TYPE_FILE = 'file';
    const TYPE_STRING = 'string';

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

    /** Поле для проверки должно быть "yes", "on", "1", 1, "true" или true.
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
        $acceptable = ['yes', 'on', '1', 1, true, 'true'];

        if (in_array($request, $acceptable, true)) {
            return;
        }
        throw new RequestException(ERR_VALIDATOR_ACCEPTED, $name);
    }

    /** Поле для проверки должно быть "yes", "on", "1", 1, "true" или true,
     * если другое поле для проверки равно указанному значению.
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
        $acceptable = ['yes', 'on', '1', 1, true, 'true'];
        if ($anotherName && $value && isset($this->input[$anotherName]) && $this->input[$anotherName] === $value) {
            if (!in_array($request, $acceptable, true)) {
                throw new RequestException(ERR_VALIDATOR_ACCEPTED_IF, $name, null, $anotherName, $value);
            }
        }
    }

    /** Поле для проверки должно быть "no", "off", "0", 0, "false" или false.
     * Это полезно для проверки принятия "Условий предоставления услуг" или аналогичных полей.
     *     Использование 'declined'
     * @param string      $name
     * @param             $request
     * @param string|null $param
     * @return void
     * @throws RequestException
     */
    private function validateDeclined(string $name, $request, ?string $param = null): void
    {
        $acceptable = ['no', 'off', '0', 0, false, 'false'];

        if (in_array($request, $acceptable, true)) {
            return;
        }
        throw new RequestException(ERR_VALIDATOR_DECLINED, $name);
    }

    /** Поле для проверки должно быть "no", "off", "0", 0, "false" или false,
     * если другое поле для проверки равно указанному значению.
     * Это полезно для проверки принятия "Условий предоставления услуг" или аналогичных полей.
     *     Использование 'declined_if:поле,значение'
     * @param string      $name
     * @param             $request
     * @param string|null $param
     * @return void
     * @throws RequestException
     */
    private function validateDeclined_if(string $name, $request, ?string $param = null): void
    {
        if (empty($param)) {
            return;
        }
        $arr = explode(',', $param);
        $anotherName = $arr[0] ?? false;
        $value = $arr[1] ?? false;
        $acceptable = ['no', 'off', '0', 0, false, 'false'];
        if ($anotherName && $value && isset($this->input[$anotherName]) && $this->input[$anotherName] === $value) {
            if (!in_array($request, $acceptable, true)) {
                throw new RequestException(ERR_VALIDATOR_DECLINED_IF, $name, null, $anotherName, $value);
            }
        }
    }

    /** Поле при проверке должно иметь допустимую запись A или AAAA в соответствии с функцией dns_get_record PHP.
     * Имя хоста предоставленного URL извлекается с помощью функции parse_url PHP перед передачей dns_get_record.
     *     Использование 'active_url'
     * @param string      $name
     * @param             $request
     * @param string|null $param
     * @return void
     * @throws RequestException
     */
    private function validateActive_url(string $name, $request, ?string $param = null): void
    {
        if (is_string($request)) {
            if ($url = parse_url($request, PHP_URL_HOST)) {
                try {
                    if (count(dns_get_record($url . '.', DNS_A | DNS_AAAA)) > 0) {
                        return;
                    };
                } catch (Exception $e) {

                }
            }
        }
        throw new RequestException(ERR_VALIDATOR_ACTIVE_URL, $name);
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
        if ($this->getSize($request) >= $param) {
            switch ($this->getType($request)) {
                case self::TYPE_ARRAY:
                    throw new RequestException(ERR_VALIDATOR_MIN_COUNT, $name, $param);
                case self::TYPE_NUMERIC:
                    throw new RequestException(ERR_VALIDATOR_MIN_NUMERIC, $name, $param);
                case self::TYPE_FILE:
                    throw new RequestException(ERR_VALIDATOR_MIN_FILE, $name, $param);
                default:
                    throw new RequestException(ERR_VALIDATOR_MIN_STRING, $name, $param);
            }
        }
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
        $acceptable = [true, false, 1, 0, '1', '0'];
        if (!in_array($request, $acceptable, true)) {
            throw new RequestException(ERR_VALIDATOR_BOOLEAN, $name);
        }
    }

    /** Возвращает размер:
     * - если целое число - его значение;
     * - если массив - количество элементов массива;
     * - если файл - размер файла в килобайтах;
     * - если строка - длинна строки.
     * @param $value
     * @return false|float|int|string
     */
    private function getSize($value)
    {
        switch (gettype($value)) {
            case self::TYPE_NUMERIC:
                return $value;
            case self::TYPE_ARRAY:
                return count($value);
            case self::TYPE_FILE:
                return $value->getSize() / 1024;
            default:
                return mb_strlen($value ?? '');
        }
    }

    /** Возвращает тип данных для функции getSize().
     * @param $value
     * @return string
     */
    private function getType($value): string
    {
        if (is_numeric($value)) {
            return self::TYPE_NUMERIC;
        } elseif (is_array($value)) {
            return self::TYPE_ARRAY;
        } elseif ($value instanceof \SplFileInfo) {
            return self::TYPE_FILE;
        }
        return self::TYPE_STRING;
    }
}