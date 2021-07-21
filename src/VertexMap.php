<?php

/**
 * @see       https://github.com/event-engine/php-inspectio-graph for the canonical source repository
 * @copyright https://github.com/event-engine/php-inspectio-graph/blob/master/COPYRIGHT.md
 * @license   https://github.com/event-engine/php-inspectio-graph/blob/master/LICENSE.md MIT License
 */

declare(strict_types=1);

namespace EventEngine\InspectioGraph;

use Iterator;

final class VertexMap implements Iterator, \Countable
{
    /**
     * @var array<string, VertexType>
     */
    private array $vertices = [];

    public static function emptyMap(): VertexMap
    {
        return new self();
    }

    public static function fromVertices(VertexType ...$vertices): VertexMap
    {
        return new self(...$vertices);
    }

    private function __construct(VertexType ...$vertices)
    {
        foreach ($vertices as $vertex) {
            $this->vertices[$vertex->id()] = $vertex;
        }
    }

    public function with(VertexType ...$vertexes): self
    {
        $instance = clone $this;

        foreach ($vertexes as $vertex) {
            $instance->vertices[$vertex->id()] = $vertex;
        }
        \reset($instance->vertices);

        return $instance;
    }

    public function without(string $id): self
    {
        $instance = clone $this;
        unset($instance->vertices[$id]);
        \reset($instance->vertices);

        return $instance;
    }

    public function filterByType(string $type): self
    {
        $instance = clone $this;
        $instance->vertices = \array_filter(
            $instance->vertices,
            static fn (VertexType $vertex) => $vertex->type() === $type
        );
        \reset($instance->vertices);

        return $instance;
    }

    /**
     * @return VertexType[]
     */
    public function vertices(): array
    {
        return \array_values($this->vertices);
    }

    public function has(string $id): bool
    {
        return isset($this->vertices[$id]);
    }

    public function count(): int
    {
        return \count($this->vertices);
    }

    public function rewind(): void
    {
        \reset($this->vertices);
    }

    public function key(): string
    {
        return \key($this->vertices);
    }

    public function next(): void
    {
        \next($this->vertices);
    }

    public function valid(): bool
    {
        return false !== \current($this->vertices);
    }

    public function current()
    {
        return \current($this->vertices);
    }
}
