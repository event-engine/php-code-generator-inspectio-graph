<?php

/**
 * @see       https://github.com/event-engine/php-inspectio-graph for the canonical source repository
 * @copyright https://github.com/event-engine/php-inspectio-graph/blob/master/COPYRIGHT.md
 * @license   https://github.com/event-engine/php-inspectio-graph/blob/master/LICENSE.md MIT License
 */

declare(strict_types=1);

namespace EventEngine\InspectioGraph\Connection;

use EventEngine\InspectioGraph\AggregateType;
use EventEngine\InspectioGraph\CommandType;
use EventEngine\InspectioGraph\DocumentType;
use EventEngine\InspectioGraph\EventType;
use EventEngine\InspectioGraph\VertexMap;

/**
 * @property AggregateConnection[] $map
 */
final class AggregateConnectionMap extends ConnectionMap
{
    public static function emptyMap(): AggregateConnectionMap
    {
        return new self();
    }

    public static function fromAggregateConnections(AggregateConnection ...$aggregateConnections): AggregateConnectionMap
    {
        return new self(...$aggregateConnections);
    }

    public function aggregateConnection(string $id): AggregateConnection
    {
        return $this->map[$id];
    }

    public function aggregateVertexMap(): VertexMap
    {
        return VertexMap::fromVertices(
            ...\array_values(
                \array_map(
                    static function (AggregateConnection $aggregateDescription) {
                        return $aggregateDescription->aggregate();
                    },
                    $this->map
                )
            )
        );
    }

    public function aggregateByCommand(CommandType $command): ?AggregateType
    {
        foreach ($this->map as $aggregateConnection) {
            if ($aggregateConnection->commandMap()->has($command->name())) {
                return $aggregateConnection->aggregate();
            }
        }

        return null;
    }

    public function aggregateByEvent(EventType $event): ?AggregateType
    {
        foreach ($this->map as $aggregateConnection) {
            if ($aggregateConnection->eventMap()->has($event->name())) {
                return $aggregateConnection->aggregate();
            }
        }

        return null;
    }

    public function aggregateByDocument(DocumentType $document): ?AggregateType
    {
        foreach ($this->map as $aggregateConnection) {
            if ($aggregateConnection->documentMap()->has($document->name())) {
                return $aggregateConnection->aggregate();
            }
        }

        return null;
    }
}
