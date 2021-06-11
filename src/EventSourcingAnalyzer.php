<?php

/**
 * @see       https://github.com/event-engine/php-inspectio-graph for the canonical source repository
 * @copyright https://github.com/event-engine/php-inspectio-graph/blob/master/COPYRIGHT.md
 * @license   https://github.com/event-engine/php-inspectio-graph/blob/master/LICENSE.md MIT License
 */

declare(strict_types=1);

namespace EventEngine\InspectioGraph;

interface EventSourcingAnalyzer
{
    public function commandMap(): VertexMap;

    public function eventMap(): VertexMap;

    public function aggregateMap(): VertexMap;

    public function documentMap(): VertexMap;

    public function externalSystemMap(): VertexMap;

    public function hotSpotMap(): VertexMap;

    public function policyMap(): VertexMap;

    public function uiMap(): VertexMap;

    public function featureMap(): VertexMap;

    public function boundedContextMap(): VertexMap;
}
