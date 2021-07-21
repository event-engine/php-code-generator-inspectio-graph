<?php

/**
 * @see       https://github.com/event-engine/php-inspectio-graph for the canonical source repository
 * @copyright https://github.com/event-engine/php-inspectio-graph/blob/master/COPYRIGHT.md
 * @license   https://github.com/event-engine/php-inspectio-graph/blob/master/LICENSE.md MIT License
 */

declare(strict_types=1);

namespace EventEngine\InspectioGraph;

interface EventSourcingAnalyzer extends CanAccessVertexConnection
{
    /**
     * Returns all command connections
     *
     * @return VertexConnectionMap
     */
    public function commandMap(): VertexConnectionMap;

    /**
     * Returns all event connections
     *
     * @return VertexConnectionMap
     */
    public function eventMap(): VertexConnectionMap;

    /**
     * Returns all aggregate connections
     *
     * @return VertexConnectionMap
     */
    public function aggregateMap(): VertexConnectionMap;

    /**
     * Returns all document connections
     *
     * @return VertexConnectionMap
     */
    public function documentMap(): VertexConnectionMap;

    /**
     * Returns all external system connections
     *
     * @return VertexConnectionMap
     */
    public function externalSystemMap(): VertexConnectionMap;

    /**
     * Returns all hot spot connections
     *
     * @return VertexConnectionMap
     */
    public function hotSpotMap(): VertexConnectionMap;

    /**
     * Returns all policy connections
     *
     * @return VertexConnectionMap
     */
    public function policyMap(): VertexConnectionMap;

    /**
     * Returns all ui connections
     *
     * @return VertexConnectionMap
     */
    public function uiMap(): VertexConnectionMap;

    /**
     * Returns all feature connections
     *
     * @return VertexConnectionMap
     */
    public function featureMap(): VertexConnectionMap;

    /**
     * Returns all bounded context connections
     *
     * @return VertexConnectionMap
     */
    public function boundedContextMap(): VertexConnectionMap;

    /**
     * Returns whole graph with all connections. Can be used for searching of vertices or lookup of connections.
     *
     * @return VertexConnectionMap
     */
    public function graph(): VertexConnectionMap;
}
