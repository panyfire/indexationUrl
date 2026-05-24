<?php
declare(strict_types=1);

namespace App\Service\Http\XmlStock;

use App\Exception\XmlStockClientException;
use App\Result\XmlSearchResult;
use App\Service\Normalizer\UrlNormalizer;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TimeoutExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final readonly class XmlStockClient
{
    public function __construct(
        private HttpClientInterface $httpClient,
        private UrlNormalizer $normalizer
    ) {
    }

    /**
     * Выполняем запрос в XmlStock
     *
     * @param string $query
     * @return XmlSearchResult
     * @throws XmlStockClientException
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     */
    public function fetch(string $query): XmlSearchResult
    {
        try {
            $response = $this->httpClient->request(
                'GET',
                $_ENV['XMLSTOCK_URL'],
                [
                    'query' => [
                        'user' => $_ENV['XMLSTOCK_USER'],
                        'key' => $_ENV['XMLSTOCK_KEY'],
                        'query' => $query,
                    ],
                    'timeout' => 20,
                ],
            );

            $content = $response->getContent();

            $xml = simplexml_load_string($content);
            $xmlResults = $xml->response->results;
            $urls = [];

            if (isset($xmlResults->grouping->group)) {
                foreach ($xmlResults->grouping->group as $group) {
                    $normalizedUrl = $this->normalizer
                        ->normalize(
                            (string)$group->doc->url
                        );

                    $urls[] = $normalizedUrl['normalized'];
                }
            }

            usleep(200_000);

            return new XmlSearchResult($urls);
        }  catch (TimeoutExceptionInterface $exception) {
            throw new XmlStockClientException(
                'Превышено время ожидания ответа от XMLStock',
            );
        } catch (ExceptionInterface $e) {
            throw new XmlStockClientException(
               "Ошибка при выполнении запроса к XMLStock",
            );
        }
    }
}
