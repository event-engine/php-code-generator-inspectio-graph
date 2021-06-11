<?php

/**
 * @see       https://github.com/event-engine/php-inspectio-graph for the canonical source repository
 * @copyright https://github.com/event-engine/php-inspectio-graph/blob/master/COPYRIGHT.md
 * @license   https://github.com/event-engine/php-inspectio-graph/blob/master/LICENSE.md MIT License
 */

declare(strict_types=1);

namespace EventEngine\InspectioGraph\Connection;

use EventEngine\InspectioGraph\CommandType;
use EventEngine\InspectioGraph\EventType;
use EventEngine\InspectioGraph\ExternalSystemType;
use EventEngine\InspectioGraph\PolicyType;
use EventEngine\InspectioGraph\VertexMap;
use SplObjectStorage;

final class PolicyConnection
{
    /**
     * @var PolicyType
     **/
    private $policy;

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
    private $externalSystemMap;

    /**
     * @var array<string, string>
     */
    private $eventToCommandList = [];

    /**
     * @var array<string, string>
     */
    private $eventToExternalSystemList = [];

    public function __construct(PolicyType $policy)
    {
        $this->policy = $policy;
        $this->commandMap = VertexMap::emptyMap();
        $this->eventMap = VertexMap::emptyMap();
        $this->externalSystemMap = VertexMap::emptyMap();
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

    public function withExternalSystems(ExternalSystemType ...$externalSystems): self
    {
        $self = clone $this;

        $self->externalSystemMap = $self->externalSystemMap->with(...$externalSystems);

        return $self;
    }

    public function withEventCommand(EventType $event, CommandType $command): self
    {
        $self = clone $this;

        $self->eventMap = $self->eventMap->with($event);
        $self->commandMap = $self->commandMap->with($command);
        $self->eventToCommandList[$event->name()] = $command->name();

        return $self;
    }

    public function withEventExternalSystem(EventType $event, ExternalSystemType $externalSystem): self
    {
        $self = clone $this;

        $self->eventMap = $self->eventMap->with($event);
        $self->externalSystemMap = $self->externalSystemMap->with($externalSystem);
        $self->eventToExternalSystemList[$event->name()] = $externalSystem->name();

        return $self;
    }

    public function policy(): PolicyType
    {
        return $this->policy;
    }

    public function commandMap(): VertexMap
    {
        return $this->commandMap;
    }

    public function eventMap(): VertexMap
    {
        return $this->eventMap;
    }

    public function externalSystemMap(): VertexMap
    {
        return $this->externalSystemMap;
    }

    /**
     * Event vertex is the key and the command vertex is the value.
     *
     * @return SplObjectStorage
     */
    public function eventsToCommandMap(): SplObjectStorage
    {
        $map = new SplObjectStorage();

        foreach ($this->eventToCommandList as $eventName => $commandName) {
            $map[$this->eventMap->vertex($eventName)] = $this->commandMap->vertex($commandName);
        }

        return $map;
    }

    /**
     * Event vertex is the key and the external system vertex is the value.
     *
     * @return SplObjectStorage
     */
    public function eventsToExternalSystemMap(): SplObjectStorage
    {
        $map = new SplObjectStorage();

        foreach ($this->eventToExternalSystemList as $eventName => $commandName) {
            $map[$this->eventMap->vertex($eventName)] = $this->commandMap->vertex($commandName);
        }

        return $map;
    }
}
