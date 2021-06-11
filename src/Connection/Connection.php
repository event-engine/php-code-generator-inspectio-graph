<?php

/**
 * @see       https://github.com/event-engine/php-inspectio-graph for the canonical source repository
 * @copyright https://github.com/event-engine/php-inspectio-graph/blob/master/COPYRIGHT.md
 * @license   https://github.com/event-engine/php-inspectio-graph/blob/master/LICENSE.md MIT License
 */

declare(strict_types=1);

namespace EventEngine\InspectioGraph\Connection;

interface Connection
{
    /**
     * Id of the connection vertex
     *
     * @return string
     */
    public function id(): string;

    /**
     * Name of the connection vertex
     *
     * @return string
     */
    public function name(): string;

    /**
     * Merge provided connection to update all vertices with latest changes
     *
     * @param Connection $connection
     * @param bool $onlyConnectionVertex Update only connection vertex with latest changes
     * @return self
     */
    public function merge(Connection $connection, bool $onlyConnectionVertex): self;
}
