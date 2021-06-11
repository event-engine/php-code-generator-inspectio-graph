<?php

/**
 * @see       https://github.com/event-engine/php-inspectio-graph for the canonical source repository
 * @copyright https://github.com/event-engine/php-inspectio-graph/blob/master/COPYRIGHT.md
 * @license   https://github.com/event-engine/php-inspectio-graph/blob/master/LICENSE.md MIT License
 */

declare(strict_types=1);

namespace EventEngine\InspectioGraph\Connection;

use Countable;
use Iterator;

/**
 * @implements Iterator<string, Connection>
 */
abstract class ConnectionMap implements Iterator, Countable
{
    /**
     * @var array<string, Connection>
     */
    protected array $map = [];

    /**
     * @var array<string, string[]>
     */
    protected array $nameToIdMap = [];

    protected function __construct(Connection ...$connections)
    {
        foreach ($connections as $connection) {
            $this->map[$connection->id()] = $connection;
            $this->updateMaps($connection);
        }
    }

    private function updateMaps(Connection $connection): void
    {
        $connectionId = $connection->id();
        $connectionName = $connection->name();

        if (! isset($this->nameToIdMap[$connectionName])) {
            $this->nameToIdMap[$connectionName] = [];
        }
        $this->nameToIdMap[$connectionName][] = $connectionId;
        $this->nameToIdMap[$connectionName] = \array_unique($this->nameToIdMap[$connectionName]);

        foreach ($this->nameToIdMap[$connectionName] as $id) {
            if ($id !== $connectionId) {
                $this->map[$id] = $this->map[$id]->merge($connection, true);
            }
        }
    }

    /**
     * Adds a new connection to the map. Existing map will be merged.
     *
     * @param string $id
     * @param Connection $connection
     * @return self
     */
    public function with(string $id, Connection $connection): self
    {
        $instance = clone $this;

        if (isset($instance->map[$id])) {
            $instance->map[$id] = $instance->map[$id]->merge($connection, false);
        } else {
            $instance->map[$id] = $connection;
        }
        $instance->updateMaps($connection);
        \reset($instance->map);

        return $instance;
    }

    /**
     * Remove connection from map
     *
     * @param string $id
     * @return self
     */
    public function without(string $id): self
    {
        $instance = clone $this;
        $connectionName = $instance->map[$id]->name();
        unset($instance->map[$id]);

        $instance->nameToIdMap[$connectionName] = \array_filter(
            $instance->nameToIdMap[$connectionName],
            static function (string $mapId) use ($id) {
                return $mapId !== $id;
            }
        );

        return $instance;
    }

    public function connection(string $id): Connection
    {
        return $this->map[$id];
    }

    public function has(string $id): bool
    {
        return isset($this->map[$id]);
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
     * @return Connection|false
     */
    public function current()
    {
        return \current($this->map);
    }
}
