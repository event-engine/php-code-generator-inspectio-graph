<?php

/**
 * @see       https://github.com/event-engine/php-inspectio-graph for the canonical source repository
 * @copyright https://github.com/event-engine/php-inspectio-graph/blob/master/COPYRIGHT.md
 * @license   https://github.com/event-engine/php-inspectio-graph/blob/master/LICENSE.md MIT License
 */

declare(strict_types=1);

namespace EventEngine\InspectioGraph;

use EventEngine\InspectioGraph;

trait EventSourcingGraph
{
    protected function addIdentity(
        VertexType $identity,
        VertexConnectionMap $vertexConnectionMap
    ): VertexConnectionMap {
        if (false === $vertexConnectionMap->has($identity->id())) {
            $vertexConnectionMap = $vertexConnectionMap->with(
                new InspectioGraph\VertexConnection($identity)
            );
        } else {
            $vertexConnectionMap->connection($identity->id())->identity()->merge($identity);
        }

        return $vertexConnectionMap;
    }

    protected function removeIdentity(
        VertexType $identity,
        VertexConnectionMap $vertexConnectionMap
    ): VertexConnectionMap {
        return $vertexConnectionMap->has($identity->id())
            ? $vertexConnectionMap->without($identity->id())
            : $vertexConnectionMap;
    }

    protected function addConnection(
        VertexType $from,
        VertexType $to,
        VertexConnectionMap $vertexConnectionMap
    ): VertexConnectionMap {
        $vertexConnectionMap = $this->addIdentity($from, $vertexConnectionMap);
        $vertexConnectionMap = $this->addIdentity($to, $vertexConnectionMap);

        $identityConnection = $vertexConnectionMap->connection($from->id());
        $identityConnectionTo = $vertexConnectionMap->connection($to->id());

        return $vertexConnectionMap
            ->with($identityConnection->withTo($identityConnectionTo->identity()))
            ->with($identityConnectionTo->withFrom($identityConnection->identity()));
    }

    protected function addParentConnection(
        VertexType $child,
        VertexType $parent,
        VertexConnectionMap $vertexConnectionMap
    ): VertexConnectionMap {
        $vertexConnectionMap = $this->addIdentity($child, $vertexConnectionMap);
        $vertexConnectionMap = $this->addIdentity($parent, $vertexConnectionMap);

        $childConnection = $vertexConnectionMap->connection($child->id());
        $parentConnection = $vertexConnectionMap->connection($parent->id());

        return $vertexConnectionMap
            ->with($childConnection->withParent($parentConnection->identity()))
            ->with($parentConnection->withChildren($childConnection->identity()));
    }
}
