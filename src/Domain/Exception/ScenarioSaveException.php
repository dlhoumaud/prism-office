<?php

declare(strict_types=1);

namespace PrismOffice\Domain\Exception;

/**
 * Exception levée quand un scénario ne peut pas être sauvegardé
 */
final class ScenarioSaveException extends \RuntimeException
{
    public static function failedToWrite(string $filepath, ?\Throwable $previous = null): self
    {
        return new self(
            sprintf('Failed to write scenario file: %s', $filepath),
            0,
            $previous
        );
    }
}
