<?php
declare(strict_types=1);

namespace App\Result;

final readonly class UrlIndexationResult
{
    public function __construct(
        public bool $indexed
    ) {
    }

    /**
     * Объект успешной проверки на индексацию
     */
    public static function success(): self
    {
        return new self(
            indexed: true
        );
    }

    /**
     * Объект неуспешной проверки на индексацию
     */
    public static function fail(): self
    {
        return new self(
            indexed: false
        );
    }
}
