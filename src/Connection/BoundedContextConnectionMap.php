<?php

/**
 * @see       https://github.com/event-engine/php-inspectio-graph for the canonical source repository
 * @copyright https://github.com/event-engine/php-inspectio-graph/blob/master/COPYRIGHT.md
 * @license   https://github.com/event-engine/php-inspectio-graph/blob/master/LICENSE.md MIT License
 */

declare(strict_types=1);

namespace EventEngine\InspectioGraph\Connection;

use EventEngine\InspectioGraph\AggregateType;
use EventEngine\InspectioGraph\BoundedContextType;
use EventEngine\InspectioGraph\CommandType;
use EventEngine\InspectioGraph\DocumentType;
use EventEngine\InspectioGraph\EventType;
use EventEngine\InspectioGraph\ExternalSystemType;
use EventEngine\InspectioGraph\FeatureType;
use EventEngine\InspectioGraph\HotSpotType;
use EventEngine\InspectioGraph\PolicyType;
use EventEngine\InspectioGraph\UiType;
use EventEngine\InspectioGraph\VertexMap;
use Iterator;

final class BoundedContextConnectionMap implements Iterator, \Countable
{
    /**
     * @var BoundedContextConnection[]
     */
    private $map = [];

    public static function emptyMap(): BoundedContextConnectionMap
    {
        return new self();
    }

    public static function fromBoundedContextConnections(BoundedContextConnection ...$boundedContextConnections): BoundedContextConnectionMap
    {
        return new self(...$boundedContextConnections);
    }

    private function __construct(BoundedContextConnection ...$boundedContextConnections)
    {
        foreach ($boundedContextConnections as $boundedContextConnection) {
            $this->map[$boundedContextConnection->boundedContext()->id()] = $boundedContextConnection;
        }
    }

    public function with(string $id, BoundedContextConnection $boundedContextConnection): self
    {
        $instance = clone $this;
        $instance->map[$id] = $boundedContextConnection;

        \reset($instance->map);

        return $instance;
    }

    public function without(string $id): self
    {
        $instance = clone $this;
        unset($instance->map[$id]);

        return $instance;
    }

    public function has(string $id): bool
    {
        return isset($this->map[$id]);
    }

    public function boundedContextConnection(string $id): BoundedContextConnection
    {
        return $this->map[$id];
    }

    public function count(): int
    {
        return \count($this->map);
    }

    public function rewind(): void
    {
        \reset($this->map);
    }

    public function key(): string
    {
        return \key($this->map);
    }

    public function next(): void
    {
        \next($this->map);
    }

    public function valid(): bool
    {
        return false !== \current($this->map);
    }

    /**
     * @return BoundedContextConnection
     */
    public function current()
    {
        return \current($this->map);
    }

    public function boundedContextConnectionVertexMap(): VertexMap
    {
        return VertexMap::fromVertices(
            ...\array_values(
                \array_map(
                    static function (BoundedContextConnection $boundedContextConnection) {
                        return $boundedContextConnection->boundedContext();
                    },
                    $this->map
                )
            )
        );
    }

    public function boundedContextByCommand(CommandType $command): ?BoundedContextType
    {
        foreach ($this->map as $boundedContextConnection) {
            if ($boundedContextConnection->commandMap()->has($command->name())) {
                return $boundedContextConnection->boundedContext();
            }
        }

        return null;
    }

    public function boundedContextByEvent(EventType $event): ?BoundedContextType
    {
        foreach ($this->map as $boundedContextConnection) {
            if ($boundedContextConnection->eventMap()->has($event->name())) {
                return $boundedContextConnection->boundedContext();
            }
        }

        return null;
    }

    public function boundedContextByAggregate(AggregateType $aggregate): ?BoundedContextType
    {
        foreach ($this->map as $boundedContextConnection) {
            if ($boundedContextConnection->aggregateMap()->has($aggregate->name())) {
                return $boundedContextConnection->boundedContext();
            }
        }

        return null;
    }

    public function boundedContextByPolicy(PolicyType $policy): ?BoundedContextType
    {
        foreach ($this->map as $boundedContextConnection) {
            if ($boundedContextConnection->policyMap()->has($policy->name())) {
                return $boundedContextConnection->boundedContext();
            }
        }

        return null;
    }

    public function boundedContextByDocument(DocumentType $document): ?BoundedContextType
    {
        foreach ($this->map as $boundedContextConnection) {
            if ($boundedContextConnection->documentMap()->has($document->name())) {
                return $boundedContextConnection->boundedContext();
            }
        }

        return null;
    }

    public function boundedContextByExternalSystem(ExternalSystemType $externalSystem): ?BoundedContextType
    {
        foreach ($this->map as $boundedContextConnection) {
            if ($boundedContextConnection->externalSystemMap()->has($externalSystem->id())) {
                return $boundedContextConnection->boundedContext();
            }
        }

        return null;
    }

    public function boundedContextByHotSpot(HotSpotType $hotSpot): ?BoundedContextType
    {
        foreach ($this->map as $boundedContextConnection) {
            if ($boundedContextConnection->hotSpotMap()->has($hotSpot->id())) {
                return $boundedContextConnection->boundedContext();
            }
        }

        return null;
    }

    public function boundedContextByUi(UiType $ui): ?BoundedContextType
    {
        foreach ($this->map as $boundedContextConnection) {
            if ($boundedContextConnection->uiMap()->has($ui->id())) {
                return $boundedContextConnection->boundedContext();
            }
        }

        return null;
    }

    public function boundedContextByFeature(FeatureType $feature): ?BoundedContextType
    {
        foreach ($this->map as $boundedContextConnection) {
            if ($boundedContextConnection->featureMap()->has($feature->id())) {
                return $boundedContextConnection->boundedContext();
            }
        }

        return null;
    }
}
