<?php

namespace App\Service\Indexation\Check;

use App\Result\UrlIndexationResult;

abstract class AbstractIndexationCheckHandler
{
    private ?self $next = null;

    /**
     * Устанавливает вариант проверки индексации
     */
    public function setNext(self $next): self
    {
        $this->next = $next;
        return $next;
    }

    /**
     * Выполняет проверку индексации
     *
     * @param array{
     *     original: string,
     *     normalized: string,
     *     domain: string
     * } $context
     */
    public function check(array $context): UrlIndexationResult
    {
        $result = $this->process($context);

        if ($result->indexed) {
            return $result;
        }

        if ($this->next !== null) {
            return $this->next->check($context);
        }

        return UrlIndexationResult::fail();
    }

    /**
     * Выполняем процесс текущей проверки
     *
     * @param array{
     *     original: string,
     *     normalized: string,
     *     domain: string
     * } $context
     */
    abstract protected function process(
        array $context
    ): UrlIndexationResult;
}
