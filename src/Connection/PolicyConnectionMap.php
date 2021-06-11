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
use EventEngine\InspectioGraph\PolicyType;
use EventEngine\InspectioGraph\VertexMap;
use Iterator;

final class PolicyConnectionMap implements Iterator, \Countable
{
    /**
     * @var PolicyConnection[]
     */
    private $map = [];

    public static function emptyMap(): PolicyConnectionMap
    {
        return new self();
    }

    public static function fromPolicyConnections(PolicyConnection ...$policyConnections): PolicyConnectionMap
    {
        return new self(...$policyConnections);
    }

    private function __construct(PolicyConnection ...$policyConnections)
    {
        foreach ($policyConnections as $policyConnection) {
            $this->map[$policyConnection->policy()->id()] = $policyConnection;
        }
    }

    public function with(string $id, PolicyConnection $policyConnection): self
    {
        $instance = clone $this;
        $instance->map[$id] = $policyConnection;

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

    public function policyConnection(string $id): PolicyConnection
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
     * @return PolicyConnection
     */
    public function current()
    {
        return \current($this->map);
    }

    public function policyVertexMap(): VertexMap
    {
        return VertexMap::fromVertices(
            ...\array_values(
                \array_map(
                    static function (PolicyConnection $policyDescription) {
                        return $policyDescription->policy();
                    },
                    $this->map
                )
            )
        );
    }

    public function policyByCommand(CommandType $command): ?PolicyType
    {
        foreach ($this->map as $policyConnection) {
            if ($policyConnection->commandMap()->has($command->name())) {
                return $policyConnection->policy();
            }
        }

        return null;
    }

    public function policyByEvent(EventType $event): ?PolicyType
    {
        foreach ($this->map as $policyConnection) {
            if ($policyConnection->eventMap()->has($event->name())) {
                return $policyConnection->policy();
            }
        }

        return null;
    }
}
