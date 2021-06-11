<?php

/**
 * @see       https://github.com/event-engine/php-inspectio-graph for the canonical source repository
 * @copyright https://github.com/event-engine/php-inspectio-graph/blob/master/COPYRIGHT.md
 * @license   https://github.com/event-engine/php-inspectio-graph/blob/master/LICENSE.md MIT License
 */

declare(strict_types=1);

namespace EventEngine\InspectioGraph\Exception;

use EventEngine\InspectioGraph\Connection\Connection;

final class ConnectionMergeNotPossible extends RuntimeException
{
    public static function notPossibleFor(Connection $currentConnection, Connection $connectionToMerge): self
    {
        return new self(
            \sprintf(
                'You can only merge with a "%s" but got "%s"',
                \get_class($currentConnection),
                \get_class($connectionToMerge),
            ),
        );
    }
}
