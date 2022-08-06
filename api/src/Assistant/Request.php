<?php

namespace src\Assistant;

class Request
{
    private array $input;

    public function __construct()
    {
        $this->input = $_REQUEST;
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

    /** Поле для проверки должно быть "yes" или "on".
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
        if ($request !== 'on' && $request !== 'yes') {
            throw new RequestException(ERR_VALIDATOR_ACCEPTED, $name);
        }
    }

    /** Поле для проверки должно быть "yes" или "on", если другое поле для проверки равно указанному значению.
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
        if ($anotherName && $value && $this->input[$anotherName] === $value) {
            if ($request !== 'on' && $request !== 'yes') {
                throw new RequestException(ERR_VALIDATOR_ACCEPTED_IF, $name, null, $anotherName, $value);
            }
        }
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
        if (strlen($request) < $param) {
            throw new RequestException(ERR_VALIDATOR_MIN, $name, $param);
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
}