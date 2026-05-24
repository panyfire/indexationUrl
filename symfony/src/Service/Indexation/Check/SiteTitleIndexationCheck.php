<?php

namespace App\Service\Indexation\Check;

use App\Exception\TitleFetchException;
use App\Exception\XmlStockClientException;
use App\Result\UrlIndexationResult;
use App\Service\Http\XmlStock\TitleFetch;
use App\Service\Http\XmlStock\XmlStockClient;

final class SiteTitleIndexationCheck extends AbstractIndexationCheckHandler
{

    public function __construct(
        private readonly XmlStockClient $client,
        private readonly TitleFetch $titleFetcher
    ) {
    }

    /**
     * @inheritDoc
     */
    protected function process(array $context): UrlIndexationResult
    {
        try {
            $title = $this->titleFetcher->fetch(
                $context['original']
            );

            if ($title === null) {
                return UrlIndexationResult::fail();
            }

            $query = sprintf(
                'site:%s "%s"',
                $context['domain'],
                $title
            );

            $result = $this->client->fetch($query);

            if (array_any($result->urls, fn($url) => $url === $context['normalized']))  {
                return UrlIndexationResult::success();
            }
        } catch (TitleFetchException | XmlStockClientException $exception) {
            return UrlIndexationResult::fail();
        }

        return UrlIndexationResult::fail();
    }
}
