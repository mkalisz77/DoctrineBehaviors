<?php

declare(strict_types=1);

namespace Knp\DoctrineBehaviors\Versionable\Exception;

use Exception;
use Knp\DoctrineBehaviors\Versionable\Contract\VersionableInterface;

final class VersionableException extends Exception
{
    public static function versionedEntityRequired(): self
    {
        return new self(sprintf('A versioned entity must implement "%s" interface.', VersionableInterface::class));
    }
}
