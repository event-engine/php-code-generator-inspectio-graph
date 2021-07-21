<?php

/**
 * @see       https://github.com/event-engine/php-inspectio-graph for the canonical source repository
 * @copyright https://github.com/event-engine/php-inspectio-graph/blob/master/COPYRIGHT.md
 * @license   https://github.com/event-engine/php-inspectio-graph/blob/master/LICENSE.md MIT License
 */

declare(strict_types=1);

namespace EventEngineTest\InspectioGraph;

use EventEngine\InspectioGraph\VertexConnectionMap;
use EventEngine\InspectioGraph\VertexType;
use EventEngineTest\InspectioGraph\Mock\SimpleEventSourcingGraph;
use EventEngineTest\InspectioGraph\Mock\Vertex;
use PHPUnit\Framework\TestCase;

final class EventSourcingGraphTest extends TestCase
{
    private const ID_ADD_BUILDING = '6e01ed33-8ab4-4c06-94f3-1574b319c94f';
    private const ID_BUILDING = '7910f5d7-9f33-4b9f-84c5-0fcc7c4d89d9';
    private const ID_BUILDING_ADDED = '30b10773-9453-4201-8e85-17c50468f6df';
    private const ID_FEATURE = '2c5b0bfa-32d5-4872-a7e6-ee00332c63cd';
    private const ID_DOCUMENT = 'c792635a-6ce4-4ad8-962a-1da0c22e0de4';

    /**
     * @test
     */
    public function it_supports_multiple_connections(): void
    {
        $map = VertexConnectionMap::emptyMap();
        $graph = new SimpleEventSourcingGraph();

        $addBuilding = $this->addBuilding();
        $building = $this->building('');
        $feature = $this->feature('');

        $map = $graph->createConnection($addBuilding, $building, $map);
        $map = $graph->createParentConnection($addBuilding, $feature, $map);
        $map = $graph->createParentConnection($building, $feature, $map);

        $commandAddBuildingConnection = $map->connection(self::ID_ADD_BUILDING);
        $aggregateAddBuildingConnection = $map->connection(self::ID_BUILDING);
        $featureConnection = $map->connection(self::ID_FEATURE);

        $this->assertCount(0, $commandAddBuildingConnection->from());
        $this->assertCount(1, $commandAddBuildingConnection->to());
        $this->assertCount(0, $commandAddBuildingConnection->children());
        $this->assertNotNull($commandAddBuildingConnection->parent());

        $this->assertSame($aggregateAddBuildingConnection->identity(), $commandAddBuildingConnection->to()->current());
        $this->assertSame($commandAddBuildingConnection->identity(),
            $aggregateAddBuildingConnection->from()->current());

        $this->assertSame($featureConnection->identity(), $commandAddBuildingConnection->parent());
        $this->assertSame($featureConnection->identity(), $aggregateAddBuildingConnection->parent());
        $this->assertSame('', $aggregateAddBuildingConnection->parent()->metadata());

        $this->assertCount(0, $featureConnection->from());
        $this->assertCount(0, $featureConnection->to());
        $this->assertCount(2, $featureConnection->children());
        $this->assertNull($featureConnection->parent());
        $this->assertSame($commandAddBuildingConnection->identity(), $featureConnection->children()->current());

        $buildingAdded = $this->buildingAdded();
        $building = $this->building('{}');
        $feature = $this->feature('{}');

        $map = $graph->createConnection($building, $buildingAdded, $map);
        $map = $graph->createParentConnection($buildingAdded, $feature, $map);

        $commandAddBuildingConnection = $map->connection(self::ID_ADD_BUILDING);
        $aggregateAddBuildingConnection = $map->connection(self::ID_BUILDING);
        $featureConnection = $map->connection(self::ID_FEATURE);
        $buildingAddedConnection = $map->connection(self::ID_BUILDING_ADDED);

        $this->assertSame($featureConnection->identity(), $commandAddBuildingConnection->parent());
        $this->assertSame($featureConnection->identity(), $aggregateAddBuildingConnection->parent());
        $this->assertSame('{}', $featureConnection->identity()->metadata());
        $this->assertSame('{}', $aggregateAddBuildingConnection->parent()->metadata());

        $this->assertCount(1, $buildingAddedConnection->from());
        $this->assertCount(0, $buildingAddedConnection->to());
        $this->assertCount(0, $buildingAddedConnection->children());
        $this->assertNotNull($buildingAddedConnection->parent());

        $this->assertSame($aggregateAddBuildingConnection->identity(), $buildingAddedConnection->from()->current());

        $document = $this->documentBuilding();

        $map = $graph->createConnection($buildingAdded, $document, $map);
        $map = $graph->createParentConnection($document, $feature, $map);

        $found = $map->findInGraph(
            self::ID_ADD_BUILDING,
            fn (VertexType $vertex) => $vertex->type() === VertexType::TYPE_DOCUMENT,
            VertexConnectionMap::WALK_FORWARD
        );
        $this->assertSame($document, $found->identity());

        $found = $map->findInGraph(
            self::ID_DOCUMENT,
            fn (VertexType $vertex) => $vertex->type() === VertexType::TYPE_COMMAND,
            VertexConnectionMap::WALK_BACKWARD
        );
        $this->assertSame($addBuilding, $found->identity());
    }

    private function addBuilding(): VertexType
    {
        return Vertex::fromScalar(
            self::ID_ADD_BUILDING,
            'Add Building',
            VertexType::TYPE_COMMAND,
            $this->filterName(),
            ''
        );
    }

    private function building(string $metadata): VertexType
    {
        return Vertex::fromScalar(
            self::ID_BUILDING,
            'Building',
            VertexType::TYPE_AGGREGATE,
            $this->filterName(),
            $metadata
        );
    }

    private function buildingAdded(): VertexType
    {
        $id = '30b10773-9453-4201-8e85-17c50468f6df';

        return Vertex::fromScalar(
            $id,
            'BuildingAdded',
            VertexType::TYPE_EVENT,
            $this->filterName(),
            ''
        );
    }

    private function documentBuilding(): VertexType
    {
        return Vertex::fromScalar(
            'c792635a-6ce4-4ad8-962a-1da0c22e0de4',
            'BuildingAdded',
            VertexType::TYPE_DOCUMENT,
            $this->filterName(),
            ''
        );
    }

    private function feature(string $metadata): VertexType
    {
        return Vertex::fromScalar(
            self::ID_FEATURE,
            'Feature Building',
            VertexType::TYPE_FEATURE,
            $this->filterName(),
            $metadata
        );
    }

    private function filterName(): callable
    {
        return static fn (string $name) => \trim($name);
    }
}
