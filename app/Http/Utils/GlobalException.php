<?php

namespace App\Http\Utils;

class GlobalException extends \Exception
{
    protected $message = 'Beklenmedik bir hata oluştu';
    protected $code = 500;

    public function __construct(?string $message = null, int $code = 0, ?\Throwable $previous = null)
    {
        $this->code = $code;
        parent::__construct($message ?? $this->message, $this->code, $previous);
    }

    public function render($request)
    {
        return response()->json(["message" => $this->message], $this->code);
    }
}
