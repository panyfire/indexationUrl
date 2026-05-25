<?php

namespace App\Service\Indexation\Validation\Request;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class IndexationRequest
{
    /**
     * Валидируем запрос на кол-во url и пустого массива
     *
     * @param string[] $urls
     */
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Count(min: 1, max: 100)]
        public array $urls
    ) {
    }
}
