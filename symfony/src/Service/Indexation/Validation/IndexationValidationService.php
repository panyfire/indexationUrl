<?php

namespace App\Service\Indexation\Validation;

final class IndexationValidationService
{
    /**
     * Проверяем валиден ли URL
     *
     * @param string $url
     * @return bool
     */
    public static function isValidUrl(string $url): bool
    {
        return filter_var(
                $url,
                FILTER_VALIDATE_URL
            ) !== false;
    }

}
