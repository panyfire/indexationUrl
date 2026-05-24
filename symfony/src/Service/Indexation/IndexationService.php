<?php

namespace App\Service\Indexation;

use App\DTO\Response\IndexationResult;
use App\Exception\AllUrlsInvalidException;
use App\Service\Indexation\Check\ExactUrlIndexationCheck;
use App\Service\Indexation\Check\InUrlIndexationCheck;
use App\Service\Indexation\Check\SiteTitleIndexationCheck;
use App\Service\Indexation\Validation\IndexationValidationService;
use App\Service\Normalizer\UrlNormalizer;

final readonly class IndexationService
{

    public function __construct(
        private UrlNormalizer $normalizer,
        private ExactUrlIndexationCheck $exactUrlCheck,
        private InUrlIndexationCheck $inUrlCheck,
        private SiteTitleIndexationCheck $siteTitleCheck
    ) {
    }

    /**
     * Check URL indexation status.
     *
     * @param string[] $urls
     *
     * @return IndexationResult[]
     * @throws AllUrlsInvalidException
     */
    public function check(array $urls): array
    {
        $this->exactUrlCheck
            ->setNext($this->inUrlCheck)
            ->setNext($this->siteTitleCheck);

        $contexts = [];

        foreach ($urls as $url) {
            if (!IndexationValidationService::isValidUrl($url)) {
                $results[] = new IndexationResult(
                    url: $url,
                    indexed: false
                );

                continue;
            }

            $normalized = $this->normalizer->normalize($url);

            $contexts[] = $normalized;
        }

        //Удаляем дубликаты после нормализации
        $uniqueContexts = [];
        foreach ($contexts as $item) {
            if (!isset($uniqueContexts[$item['normalized']])) {
                $uniqueContexts[$item['normalized']] = $item;
            }
        }

        $contexts = $uniqueContexts;

        if (count($contexts) === 0) {
            throw new AllUrlsInvalidException(
                'Не найдено ни одного валидного URL',
            );
        }

        $results = [];

        foreach ($contexts as $context) {
            $result = $this->exactUrlCheck->check($context);
            $results[] = new IndexationResult(
                url: $context['original'],
                indexed: $result->indexed
            );
        }

        return $results;
    }
}
