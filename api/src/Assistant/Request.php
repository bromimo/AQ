<?php

namespace src\Assistant;

use Exception;

class Request
{
    private array  $input;
    private string $name;
    private array  $list;
    private string $secondDate;

    const TYPE_FILE    = 0;
    const TYPE_ARRAY   = 1;
    const TYPE_STRING  = 2;
    const TYPE_NUMERIC = 3;

    const COMPARE_DATE_OPERATORS = ['=', '<', '>', '<=', '>=', '!='];

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
        foreach ($rules as $this->name => $this->list) {
            foreach ($this->list as $rule) {
                $request = $this->input[$this->name] ?? null;
                $arr = explode(':', $rule);
                $params = $arr[1] ?? null;
                $method = $prefix . ucfirst($arr[0]);
                if (method_exists(self::class, $method)) {
                    self::$method($request, $params);
                }
                $result[$this->name] = $request;
            }
        }

        return $result;
    }

    /** Поле для проверки должно быть "yes", "on", "1", 1, "true" или true.
     * Это полезно для проверки принятия "Условий предоставления услуг" или аналогичных полей.
     *     Использование 'accepted'
     * @param             $request
     * @param string|null $param
     * @return void
     * @throws RequestException
     */
    private function validateAccepted($request, ?string $param = null): void
    {
        $acceptable = ['yes', 'on', '1', 1, true, 'true'];

        if (in_array($request, $acceptable, true)) {
            return;
        }
        throw new RequestException(ERR_VALIDATOR_ACCEPTED, $this->name);
    }

    /** Поле для проверки должно быть "yes", "on", "1", 1, "true" или true,
     * если другое поле для проверки равно указанному значению.
     * Это полезно для проверки принятия "Условий предоставления услуг" или аналогичных полей.
     *     Использование 'accepted_if:поле,значение'
     * @param             $request
     * @param string|null $param
     * @return void
     * @throws RequestException
     */
    private function validateAccepted_if($request, ?string $param = null): void
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
                throw new RequestException(ERR_VALIDATOR_ACCEPTED_IF, $this->name, null, $anotherName, $value);
            }
        }
    }

    /** Поле для проверки должно быть действительной, не относительной датой в соответствии с функцией strtotime PHP.
     *     Использование 'date'
     * @param             $request
     * @param string|null $param
     * @return void
     * @throws RequestException
     */
    private function validateDate($request, ?string $param = null): void
    {
        if ($request instanceof \DateTime) {
            return;
        }
        try {
            if ((is_string($request) || is_numeric($request)) && strtotime($request) === true) {
                return;
            }
        } catch (Exception $e) {
            //
        }
        $date = date_parse($request);
        if (checkdate($date['month'], $date['day'], $date['year'])) {
            return;
        }
        throw new RequestException(ERR_VALIDATOR_DATE, $this->name, $param);
    }

    /** Поле для проверки должно соответствовать заданному формату. При проверке поля следует использовать
     * либо 'date' или 'date_format', а не оба. Это правило проверки поддерживает все форматы,
     * поддерживаемые классом DateTime PHP.
     *     Использование 'date_format:формат'
     * @param             $request
     * @param string|null $param
     * @return void
     * @throws RequestException
     */
    private function validateDate_format($request, ?string $param = null): void
    {
        if (is_string($request) || is_numeric($request)) {
            $date = \DateTime::createFromFormat('!' . $param, $request);
            if ($date && $date->format($param) == $request) {
                return;
            }
        }
        throw new RequestException(ERR_VALIDATOR_DATE_FORMAT, $this->name, $param);
    }

    /** Поле для проверки должно быть "no", "off", "0", 0, "false" или false.
     * Это полезно для проверки принятия "Условий предоставления услуг" или аналогичных полей.
     *     Использование 'declined'
     * @param             $request
     * @param string|null $param
     * @return void
     * @throws RequestException
     */
    private function validateDeclined($request, ?string $param = null): void
    {
        $acceptable = ['no', 'off', '0', 0, false, 'false'];

        if (in_array($request, $acceptable, true)) {
            return;
        }
        throw new RequestException(ERR_VALIDATOR_DECLINED, $this->name);
    }

    /** Поле для проверки должно быть "no", "off", "0", 0, "false" или false,
     * если другое поле для проверки равно указанному значению.
     * Это полезно для проверки принятия "Условий предоставления услуг" или аналогичных полей.
     *     Использование 'declined_if:поле,значение'
     * @param             $request
     * @param string|null $param
     * @return void
     * @throws RequestException
     */
    private function validateDeclined_if($request, ?string $param = null): void
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
                throw new RequestException(ERR_VALIDATOR_DECLINED_IF, $this->name, null, $anotherName, $value);
            }
        }
    }

    /** Поле при проверке должно иметь допустимую запись A или AAAA в соответствии с функцией dns_get_record PHP.
     * Имя хоста предоставленного URL извлекается с помощью функции parse_url PHP перед передачей dns_get_record.
     *     Использование 'active_url'
     * @param             $request
     * @param string|null $param
     * @return void
     * @throws RequestException
     */
    private function validateActive_url($request, ?string $param = null): void
    {
        if (is_string($request)) {
            if ($url = parse_url($request, PHP_URL_HOST)) {
                try {
                    if (count(dns_get_record($url . '.', DNS_A | DNS_AAAA)) > 0) {
                        return;
                    };
                } catch (Exception $e) {
                    //
                }
            }
        }
        throw new RequestException(ERR_VALIDATOR_ACTIVE_URL, $this->name);
    }

    /** Поле при проверке должно быть значением после заданной даты. Даты будут переданы в функцию strtotime PHP
     * для преобразования в действительный DateTime экземпляр
     *     Использование 'after:date'
     * @param             $request
     * @param string|null $param
     * @return void
     * @throws RequestException
     */
    private function validateAfter($request, ?string $param = null): void
    {
        if (is_null($param)) {
            return;
        }
        if (!$this->compareDates($request, $param, '>')) {
            throw new RequestException(ERR_VALIDATOR_AFTER, $this->name, $this->secondDate);
        }
    }

    /** Поле для проверки должно иметь значение после или равное заданной дате.
     * Дополнительные сведения см. в функции правила 'after:date'.
     *     Использование 'after_or_equal:date'
     * @param             $request
     * @param string|null $param
     * @return void
     * @throws RequestException
     */
    private function validateAfter_or_equal($request, ?string $param = null): void
    {
        if (is_null($param)) {
            return;
        }
        if (!$this->compareDates($request, $param, '>=')) {
            throw new RequestException(ERR_VALIDATOR_AFTER_OR_EQUAL, $this->name, $this->secondDate);
        }
    }

    /** Поле при проверке должно быть значением ранее заданной даты. Даты будут переданы в функцию strtotime PHP
     * для преобразования в действительный DateTime экземпляр
     *     Использование 'before:date'
     * @param             $request
     * @param string|null $param
     * @return void
     * @throws RequestException
     */
    private function validateBefore($request, ?string $param = null): void
    {
        if (is_null($param)) {
            return;
        }
        if (!$this->compareDates($request, $param, '<')) {
            throw new RequestException(ERR_VALIDATOR_BEFORE, $this->name, $this->secondDate);
        }
    }

    /** Поле для проверки должно иметь значение ранее или равное заданной дате.
     * Дополнительные сведения см. в функции правила 'before:date'.
     *     Использование 'before_or_equal:date'
     * @param             $request
     * @param string|null $param
     * @return void
     * @throws RequestException
     */
    private function validateBefore_or_equal($request, ?string $param = null): void
    {
        if (is_null($param)) {
            return;
        }
        if (!$this->compareDates($request, $param, '<=')) {
            throw new RequestException(ERR_VALIDATOR_BEFORE_OR_EQUAL, $this->name, $this->secondDate);
        }
    }

    /** Поле при проверке должно быть значением равное заданной даты. Даты будут переданы в функцию strtotime PHP
     * для преобразования в действительный DateTime экземпляр
     *     Использование 'date_equal:date'
     * @param             $request
     * @param string|null $param
     * @return void
     * @throws RequestException
     */
    private function validateDate_equal($request, ?string $param = null): void
    {
        if (is_null($param)) {
            return;
        }
        if (!$this->compareDates($request, $param)) {
            throw new RequestException(ERR_VALIDATOR_EQUAL, $this->name, $this->secondDate);
        }
    }

    /** Поле при проверке должно быть значением не равное заданной даты. Даты будут переданы в функцию strtotime PHP
     * для преобразования в действительный DateTime экземпляр
     *     Использование 'date_not_equal:date'
     * @param             $request
     * @param string|null $param
     * @return void
     * @throws RequestException
     */
    private function validateDate_not_equal($request, ?string $param = null): void
    {
        if (is_null($param)) {
            return;
        }
        if (!$this->compareDates($request, $param, '!=')) {
            throw new RequestException(ERR_VALIDATOR_NOT_EQUAL, $this->name, $this->secondDate);
        }
    }

    /** Поле для проверки должно состоять только из буквенных символов.
     *     Использование 'alpha'
     * @param             $request
     * @param string|null $param
     * @return void
     * @throws RequestException
     */
    private function validateAlpha($request, ?string $param = null): void
    {
        if (!preg_match('/^[\pL\pM]+$/u', $request)) {
            throw new RequestException(ERR_VALIDATOR_ALPHA, $this->name);
        }
    }

    /** Поле для проверки должно состоять только из буквенно-цифровых символов, тире и символов подчеркивания.
     *     Использование 'alpha_dash'
     * @param             $request
     * @param string|null $param
     * @return void
     * @throws RequestException
     */
    private function validateAlpha_dash($request, ?string $param = null): void
    {
        if (!preg_match('/^[\pL\pM\pN_-]+$/u', $request)) {
            throw new RequestException(ERR_VALIDATOR_ALPHA_DASH, $this->name);
        }
    }

    /** Поле для проверки должно состоять только из буквенно-цифровых символов.
     *     Использование 'alpha_num'
     * @param             $request
     * @param string|null $param
     * @return void
     * @throws RequestException
     */
    private function validateAlpha_num($request, ?string $param = null): void
    {
        if (!preg_match('/^[\pL\pM\pN]+$/u', $request)) {
            throw new RequestException(ERR_VALIDATOR_ALPHA_NUM, $this->name);
        }
    }

    /** Поле для проверки должно состоять только из буквенных символов и пробелов.
     *     Использование 'alpha_space'
     * @param             $request
     * @param string|null $param
     * @return void
     * @throws RequestException
     */
    private function validateAlpha_space($request, ?string $param = null): void
    {
        if (!preg_match('/^[\pL\s]+$/u', $request)) {
            throw new RequestException(ERR_VALIDATOR_ALPHA_SPACE, $this->name);
        }
    }

    /** Поле для проверки должно иметь минимальное значение.
     * Строки, числа, массивы и файлы оцениваются таким же образом, как 'size' правило.
     *     Использование 'min:size'
     * @param             $request
     * @param string|null $param
     * @return void
     * @throws RequestException
     */
    private function validateMin($request, ?string $param = null): void
    {
        if ($this->getSize($request) >= $param) {
            switch ($this->getType($request)) {
                case self::TYPE_ARRAY:
                    throw new RequestException(ERR_VALIDATOR_MIN_COUNT, $this->name, $param);
                case self::TYPE_NUMERIC:
                    throw new RequestException(ERR_VALIDATOR_MIN_NUMERIC, $this->name, $param);
                case self::TYPE_FILE:
                    throw new RequestException(ERR_VALIDATOR_MIN_FILE, $this->name, $param);
                default:
                    throw new RequestException(ERR_VALIDATOR_MIN_STRING, $this->name, $param);
            }
        }
    }

    /** Поле для проверки должно иметь максимальное значение.
     * Строки, числа, массивы и файлы оцениваются таким же образом, как 'size' правило.
     *     Использование 'max:size'
     * @param             $request
     * @param string|null $param
     * @return void
     * @throws RequestException
     */
    private function validateMax($request, ?string $param = null): void
    {
        if ($this->getSize($request) <= $param) {
            switch ($this->getType($request)) {
                case self::TYPE_ARRAY:
                    throw new RequestException(ERR_VALIDATOR_MAX_COUNT, $this->name, $param);
                case self::TYPE_NUMERIC:
                    throw new RequestException(ERR_VALIDATOR_MAX_NUMERIC, $this->name, $param);
                case self::TYPE_FILE:
                    throw new RequestException(ERR_VALIDATOR_MAX_FILE, $this->name, $param);
                default:
                    throw new RequestException(ERR_VALIDATOR_MAX_STRING, $this->name, $param);
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
     * @param             $request
     * @param string|null $param
     * @return void
     * @throws RequestException
     */
    private function validateRequired($request, ?string $param = null): void
    {
        if (empty($request)) {
            throw new RequestException(ERR_VALIDATOR_REQUIRED, $this->name);
        }
    }

    /** Поле для проверки должно быть строкой.
     *     Использование 'string'
     * @param             $request
     * @param string|null $param
     * @return void
     * @throws RequestException
     */
    private function validateString($request, ?string $param = null): void
    {
        if (gettype($request) !== 'string') {
            throw new RequestException(ERR_VALIDATOR_STRING, $this->name);
        }
    }

    /** Поле для проверки должно быть массивом и содержать только перечисленные поля.
     *     Использование 'array:field1,...'
     * @param             $request
     * @param string|null $param
     * @return void
     * @throws RequestException
     */
    private function validateArray($request, ?string $param = null): void
    {
        if (gettype($request) !== 'array') {
            throw new RequestException(ERR_VALIDATOR_ARRAY, $this->name);
        }
        if (!$param) {
            return;
        }
        $list = explode(',', $param);
        foreach ($list as $item) {
            if (!isset($request[$item])) {
                throw new RequestException(ERR_VALIDATOR_ARRAY_FIELD, $this->name, $item);
            }
        }
        foreach ($request as $key => $item) {
            if (!in_array($key, $list, true)) {
                throw new RequestException(ERR_VALIDATOR_ARRAY_EXCESS, $this->name, $key);
            }
        }
    }

    /** Поле для проверки должно быть целым числом.
     *     Использование 'integer'
     * @param             $request
     * @param string|null $param
     * @return void
     * @throws RequestException
     */
    private function validateInteger($request, ?string $param = null): void
    {
        if (gettype($request) !== 'integer') {
            throw new RequestException(ERR_VALIDATOR_INTEGER, $this->name);
        }
    }

    /** Поле при проверке должно иметь возможность преобразования в логическое значение.
     * Допустимыми входными данными являются true, false, 1, 0, "1", и "0".
     *     Использование 'boolean'
     * @param             $request
     * @param string|null $param
     * @return void
     * @throws RequestException
     */
    private function validateBoolean($request, ?string $param = null): void
    {
        $acceptable = [true, false, 1, 0, '1', '0'];
        if (!in_array($request, $acceptable, true)) {
            throw new RequestException(ERR_VALIDATOR_BOOLEAN, $this->name);
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

    /** Сравнивает даты.
     * @param        $request
     * @param        $param
     * @param string $operator
     * @return bool
     * @throws RequestException
     */
    private function compareDates($request, $param, string $operator = '='): bool
    {
        if (!is_string($request) && !is_numeric($request) && !$request instanceof \DateTime) {
            throw new RequestException(ERR_VALIDATOR_DATE, $this->name);
        }

        if (!in_array($operator, self::COMPARE_DATE_OPERATORS)) {
            throw new Exception("Invalid operator '$operator'.");
        }

        $format = $this->getDateFormat();
        return $this->checkDateTimeOrder($format, $request, $param, $operator);
    }

    /** Ищет date_format в списке правил поля.
     * @return string
     */
    private function getDateFormat(): string
    {
        $pattern = 'date_format:';
        $rule = $this->getRule($pattern);

        return str_replace($pattern, '', $rule);
    }

    /** Ищет определенное правило в списке правил поля.
     * @param string $pattern
     * @return string
     */
    private function getRule(string $pattern): string
    {
        foreach ($this->list as $rule) {
            preg_match("/$pattern.*/", $rule, $matches);
            if ($matches) {
                return $matches[0];
            }
        }

        return '';
    }

    /** Обертка метода 'compare'.
     * @param string $format
     * @param string $first
     * @param string $second
     * @param string $operator
     * @return bool
     * @throws Exception
     */
    private function checkDateTimeOrder(string $format, string $first, string $second, string $operator): bool
    {
        $firstDate = $this->getDateTimeWithOptionalFormat($format, $first);

        $secondDate = $this->getDateTimeWithOptionalFormat($format, $second);
        $this->secondDate = $secondDate->format($format);

        return $this->compare($firstDate, $secondDate, $operator);
    }

    /** Определяет, выполняется ли сравнение между заданными значениями.
     * @param \DateTime $first
     * @param \DateTime $second
     * @param string    $operator '=', '<', '>', '<=', '>=', '!='
     * @return bool
     * @throws Exception
     */
    private function compare(\DateTime $first, \DateTime $second, string $operator): bool
    {
        switch ($operator) {
            case '=':
                return $first == $second;
            case '<':
                return $first < $second;
            case '>':
                return $first > $second;
            case '<=':
                return $first <= $second;
            case '>=':
                return $first >= $second;
            case '!=':
                return $first != $second;
            default:
                throw new Exception("Invalid operator '$operator'.");
        }
    }

    /** Пытается получить DateTime.
     * @param string $format
     * @param string $value
     * @return \DateTime
     * @throws RequestException
     */
    private function getDateTimeWithOptionalFormat(string $format, string $value): \DateTime
    {
        if ($date = \DateTime::createFromFormat('!'.$format, $value)) {
            echo $date->format('Y-m-d') . PHP_EOL;
            return $date;
        }
        try {
            $date = new \DateTime($value);
            echo $date->format('Y-m-d') . PHP_EOL;
            return $date;
        } catch (Exception $e) {
            throw new RequestException(ERR_VALIDATOR_DATE, $value);
        }
    }
}