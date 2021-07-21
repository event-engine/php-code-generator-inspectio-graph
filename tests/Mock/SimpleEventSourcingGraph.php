<?php

/**
 * @see       https://github.com/event-engine/php-inspectio-graph for the canonical source repository
 * @copyright https://github.com/event-engine/php-inspectio-graph/blob/master/COPYRIGHT.md
 * @license   https://github.com/event-engine/php-inspectio-graph/blob/master/LICENSE.md MIT License
 */

declare(strict_types=1);

namespace EventEngineTest\InspectioGraph\Mock;

use EventEngine\InspectioGraph\EventSourcingGraph;
use EventEngine\InspectioGraph\VertexConnectionMap;
use EventEngine\InspectioGraph\VertexType;

final class SimpleEventSourcingGraph
{
    use EventSourcingGraph;

    public function createConnection(
        VertexType $from,
        VertexType $to,
        VertexConnectionMap $vertexConnectionMap
    ): VertexConnectionMap {
        return $this->addConnection($from, $to, $vertexConnectionMap);
    }

    public function createParentConnection(
        VertexType $vertex,
        VertexType $parent,
        VertexConnectionMap $vertexConnectionMap
    ): VertexConnectionMap {
        return $this->addParentConnection($vertex, $parent, $vertexConnectionMap);
    }
}
