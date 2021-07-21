<?php

/**
 * @see       https://github.com/event-engine/php-inspectio-graph for the canonical source repository
 * @copyright https://github.com/event-engine/php-inspectio-graph/blob/master/COPYRIGHT.md
 * @license   https://github.com/event-engine/php-inspectio-graph/blob/master/LICENSE.md MIT License
 */

declare(strict_types=1);

namespace EventEngine\InspectioGraph;

final class VertexConnection
{
    private VertexType $identity;
    private ?VertexType $parent = null;
    private VertexMap $children;
    private VertexMap $from;
    private VertexMap $to;

    public function __construct(VertexType $identity)
    {
        $this->identity = $identity;
        $this->from = VertexMap::emptyMap();
        $this->to = VertexMap::emptyMap();
        $this->children = VertexMap::emptyMap();
    }

    public function withFrom(VertexType ...$from): self
    {
        $instance = clone $this;
        foreach ($from as $item) {
            $instance->from = $instance->from->with($item);
        }

        return $instance;
    }

    public function withTo(VertexType ...$to): self
    {
        $instance = clone $this;

        foreach ($to as $item) {
            $instance->to = $instance->to->with($item);
        }

        return $instance;
    }

    public function withChildren(VertexType ...$children): self
    {
        $instance = clone $this;

        foreach ($children as $item) {
            $instance->children = $instance->children->with($item);
        }

        return $instance;
    }

    public function withParent(VertexType $parent): self
    {
        $instance = clone $this;
        $instance->parent = $instance->parent = $parent;

        return $instance;
    }

    public function identity(): VertexType
    {
        return $this->identity;
    }

    public function from(): VertexMap
    {
        return $this->from;
    }

    public function to(): VertexMap
    {
        return $this->to;
    }

    public function children(): VertexMap
    {
        return $this->children;
    }

    public function parent(): ?VertexType
    {
        return $this->parent;
    }
}
