<?php

/**
 * @see       https://github.com/event-engine/php-inspectio-graph for the canonical source repository
 * @copyright https://github.com/event-engine/php-inspectio-graph/blob/master/COPYRIGHT.md
 * @license   https://github.com/event-engine/php-inspectio-graph/blob/master/LICENSE.md MIT License
 */

declare(strict_types=1);

namespace EventEngine\InspectioGraph;

use Countable;
use EventEngine\InspectioGraph\Exception\RuntimeException;
use Iterator;

final class VertexConnectionMap implements Iterator, Countable, CanAccessVertexConnection
{
    public const WALK_FORWARD = 'to';
    public const WALK_BACKWARD = 'from';

    /**
     * @var VertexConnection[]
     */
    private array $connections = [];

    public static function emptyMap(): VertexConnectionMap
    {
        return new self();
    }

    public static function fromConnections(VertexConnection ...$vertices): VertexConnectionMap
    {
        return new self(...$vertices);
    }

    private function __construct(VertexConnection ...$vertices)
    {
        foreach ($vertices as $vertex) {
            $this->connections[$vertex->identity()->id()] = $vertex;
        }
    }

    public function with(VertexConnection ...$vertexes): self
    {
        $instance = clone $this;

        foreach ($vertexes as $vertex) {
            $id = $vertex->identity()->id();

            if (isset($instance->connections[$id])) {
                $instance->connections[$id] = $instance->connections[$id]
                    ->withFrom(...$vertex->from()->vertices())
                    ->withTo(...$vertex->to()->vertices())
                    ->withChildren(...$vertex->children()->vertices());

                if ($parent = $vertex->parent()) {
                    $instance->connections[$id] = $instance->connections[$id]->withParent($parent);
                }
            } else {
                $instance->connections[$id] = $vertex;
            }
        }
        \reset($instance->connections);

        return $instance;
    }

    public function without(string $id): self
    {
        $instance = clone $this;
        // TODO cycle through all connections and remove identity of from / to / parent / child map
        unset($instance->connections[$id]);

        return $instance;
    }

    public function filterByType(string $type): self
    {
        $instance = clone $this;
        $instance->connections = \array_filter(
            $instance->connections,
            static fn (VertexConnection $identity) => $identity->identity()->type() === $type
        );
        \reset($instance->connections);

        return $instance;
    }

    public function filterByName(string $name): self
    {
        $instance = clone $this;
        $instance->connections = \array_filter(
            $instance->connections,
            static fn (VertexConnection $identity) => $identity->identity()->name() === $name
        );
        \reset($instance->connections);

        return $instance;
    }

    public function filter(callable $callback): self
    {
        $instance = clone $this;
        $instance->connections = \array_filter(
            $instance->connections,
            $callback
        );
        \reset($instance->connections);

        return $instance;
    }

    /**
     * Search for a specific vertex in graph
     *
     * @param string $startId ID of the start vertex
     * @param callable $callback Callback which gets the VertexType provided to check if this is the vertex you looking for
     * @param string $direction Move forward or backward from the start vertex, use a WALK_* constant
     * @param int $maxSteps How much steps do you want to go through the graph
     * @return VertexConnection|null
     */
    public function findInGraph(string $startId, callable $callback, string $direction, int $maxSteps = 5): ?VertexConnection
    {
        if ($maxSteps < 0) {
            throw new RuntimeException(
                \sprintf('The provided maxSteps "%s" must be greater than 0.', $maxSteps)
            );
        }

        if ($direction !== self::WALK_FORWARD
            && $direction !== self::WALK_BACKWARD
        ) {
            throw new RuntimeException(
                \sprintf('The provided direction "%s" is wrong. Please use the constants %s::WALK_*', $direction, self::class)
            );
        }

        if ($maxSteps === 0) {
            return null;
        }

        if (! isset($this->connections[$startId])) {
            throw new RuntimeException(
                \sprintf('The id "%s" was not found in graph.', $startId)
            );
        }
        $connection = $this->connections[$startId];

        foreach ($connection->$direction() as $toConnection) {
            if ($callback($toConnection) === true) {
                return $this->connections[$toConnection->id()];
            }
            if ($found = $this->findInGraph($toConnection->id(), $callback, $direction, --$maxSteps)) {
                return $found;
            }
        }

        return null;
    }

    public function has(string $id): bool
    {
        return isset($this->connections[$id]);
    }

    public function connection(string $id): VertexConnection
    {
        return $this->connections[$id];
    }

    public function count(): int
    {
        return \count($this->connections);
    }

    public function rewind(): void
    {
        \reset($this->connections);
    }

    public function key(): string
    {
        return \key($this->connections);
    }

    public function next(): void
    {
        \next($this->connections);
    }

    public function valid(): bool
    {
        return false !== \current($this->connections);
    }

    /**
     * @return VertexConnection|false|mixed
     */
    public function current()
    {
        return \current($this->connections);
    }
}
