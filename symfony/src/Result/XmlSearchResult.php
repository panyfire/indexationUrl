<?php
declare(strict_types=1);

namespace App\Result;

final readonly class XmlSearchResult
{
    /**
     * @param string[] $urls
     */
    public function __construct(
        public array $urls,
    ) {
    }
}
