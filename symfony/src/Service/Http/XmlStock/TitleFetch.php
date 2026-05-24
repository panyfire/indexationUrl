<?php
declare(strict_types=1);

namespace App\Service\Http\XmlStock;

use App\Exception\TitleFetchException;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final readonly class TitleFetch
{
    public function __construct(
        private HttpClientInterface $httpClient,
    ) {
    }

    /**
     * Осуществляем запрос на Title по url в XmlStock
     * и возвращаем тайтл страницы
     *
     * @param string $url
     * @return string|null
     * @throws ClientExceptionInterface
     * @throws TitleFetchException
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     */
    public function fetch(string $url): ?string
    {
        try {
            $response = $this->httpClient->request(
                'GET',
                $url,
                [
                    'timeout' => 20,
                ],
            );

            $html = $response->getContent();

            preg_match(
                '#<title>(.*?)</title>#is',
                $html,
                $matches,
            );

            if (
                !isset($matches[1])
            ) {
                return null;
            }

            return trim($matches[1]);
        } catch (ExceptionInterface $exception) {
            throw new TitleFetchException(
                'Ошибка получения title страницы',
            );
        }
    }
}
