<?php

namespace App\Service\Indexation\Check;

use App\Result\UrlIndexationResult;
use App\Service\Http\XmlStock\XmlStockClient;

final class InUrlIndexationCheck extends AbstractIndexationCheckHandler
{
    public function __construct(
        private readonly XmlStockClient $client,
    ) {
    }

    /**
     * @inheritDoc
     */
    protected function process(array $context): UrlIndexationResult
    {
        $query = sprintf(
            'inurl:"%s"',
            $context['normalized'],
        );

        $result = $this->client->fetch($query);

        if (array_any($result->urls, fn($url) => $url === $context['normalized'])) {
            print_r($context);
            return UrlIndexationResult::success();
        }

        return UrlIndexationResult::fail();
    }
}
