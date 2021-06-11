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
use EventEngine\InspectioGraph\Exception\ConnectionMergeNotPossible;
use EventEngine\InspectioGraph\VertexMap;
use SplObjectStorage;

final class AggregateConnection implements Connection
{
    /**
     * @var AggregateType
     **/
    private $aggregate;

    /**
     * @var VertexMap
     */
    private $commandMap;

    /**
     * @var VertexMap
     */
    private $eventMap;

    /**
     * @var VertexMap
     */
    private $documentMap;

    /**
     * @var string[][]
     */
    private $commandToEventList = [];

    public function __construct(AggregateType $aggregate)
    {
        $this->aggregate = $aggregate;
        $this->commandMap = VertexMap::emptyMap();
        $this->eventMap = VertexMap::emptyMap();
        $this->documentMap = VertexMap::emptyMap();
    }

    public function withCommands(CommandType ...$commands): self
    {
        $self = clone $this;

        $self->commandMap = $self->commandMap->with(...$commands);

        return $self;
    }

    public function withEvents(EventType ...$events): self
    {
        $self = clone $this;

        $self->eventMap = $self->eventMap->with(...$events);

        return $self;
    }

    public function withDocuments(DocumentType ...$documents): self
    {
        $self = clone $this;

        $self->documentMap = $self->documentMap->with(...$documents);

        return $self;
    }

    public function withCommandEvents(CommandType $command, EventType ...$events): self
    {
        $self = clone $this;

        foreach ($events as $event) {
            $self->eventMap = $self->eventMap->with($event);

            if (! isset($self->commandToEventList[$command->name()])) {
                $self->commandToEventList[$command->name()] = [];
            }

            if (! \in_array($event->name(), $self->commandToEventList[$command->name()], true)) {
                $self->commandToEventList[$command->name()][] = $event->name();
            }
        }

        $self->commandMap = $self->commandMap->with($command);

        return $self;
    }

    public function merge(Connection $connection, bool $onlyConnectionVertex): self
    {
        if (! $connection instanceof self) {
            throw ConnectionMergeNotPossible::notPossibleFor($this, $connection);
        }

        $self = clone $this;

        $self->aggregate = $connection->aggregate;

        if ($onlyConnectionVertex === false) {
            /** @phpstan-ignore-next-line */
            $self = $self->withCommands(...$connection->commandMap->vertices());
            /** @phpstan-ignore-next-line */
            $self = $self->withEvents(...$connection->eventMap->vertices());
            /** @phpstan-ignore-next-line */
            $self = $self->withDocuments(...$connection->documentMap->vertices());

            $commandsToEventsMap = $connection->commandsToEventsMap();

            foreach ($commandsToEventsMap as $command) {
                $self = $self->withCommandEvents($command, ...$commandsToEventsMap[$command]);
            }
        }

        return $self;
    }

    public function aggregate(): AggregateType
    {
        return $this->aggregate;
    }

    public function commandMap(): VertexMap
    {
        return $this->commandMap;
    }

    public function eventMap(): VertexMap
    {
        return $this->eventMap;
    }

    public function documentMap(): VertexMap
    {
        return $this->documentMap;
    }

    /**
     * Command vertex is the key and the event vertices are the values.
     *
     * @return SplObjectStorage
     */
    public function commandsToEventsMap(): SplObjectStorage
    {
        $map = new SplObjectStorage();

        foreach ($this->commandToEventList as $commandName => $events) {
            $map[$this->commandMap->vertex($commandName)] = \array_map(
                function (string $eventName) {
                    return $this->eventMap->vertex($eventName);
                },
                $events
            );
        }

        return $map;
    }

    public function id(): string
    {
        return $this->aggregate->id();
    }

    public function name(): string
    {
        return $this->aggregate->name();
    }
}
