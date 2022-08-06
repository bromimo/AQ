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
    public function all()
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

    private function validateRequired(string $name, ?string $request, ?string $param = null)
    {
        if (empty($request)) {
            throw new RequestException(ERR_VALIDATOR_REQUIRED, $name);
        }
    }

    private function validateMin(string $name, ?string $request, ?string $param = null)
    {
        if (strlen($request) < $param) {
            throw new RequestException(ERR_VALIDATOR_MIN_CHARACTERS, $name, $param);
        }
    }

    private function validateAlpha(string $name, ?string $request, ?string $param = null)
    {
        if (!preg_match('/^\pL+$/u', $request)) {
            throw new RequestException(ERR_VALIDATOR_ALPHA, $name);
        }
    }
}