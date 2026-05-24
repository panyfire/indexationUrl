<?php
declare(strict_types=1);

namespace App\Service\Normalizer;

final class UrlNormalizer
{
    /**
     * Осуществляем нормализацию URL:
     * 1. Убираем домены https:// и http://
     * 2. Убираем слеш в конце
     * 3. Приводим к нижнему регистру
     *
     * А также сохраняем оригинальный URL
     *
     * @return array{
     *     original: string,
     *     normalized: string,
     *     domain: string
     * }
     */
    public function normalize(string $url): array
    {
        $original = $url;

        $normalized  = mb_strtolower($url);

        $normalized = preg_replace(
            '#^https?://#',
            '',
            $normalized
        );

        $normalized = rtrim(
            $normalized,
            '/'
        );


        $domain = (string) parse_url(
            'https://' . $normalized,
            PHP_URL_HOST
        );

        return [
            'original' => $original,
            'normalized' => $normalized,
            'domain' => $domain
        ];
    }
}
