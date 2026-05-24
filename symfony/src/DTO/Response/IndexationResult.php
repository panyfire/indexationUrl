<?php

declare(strict_types=1);

namespace App\DTO\Response;

final readonly class IndexationResult
{
    public function __construct(
        public string $url,
        public bool $indexed
    ) {
    }
}
