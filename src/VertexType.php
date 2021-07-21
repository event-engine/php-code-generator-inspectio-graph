<?php

/**
 * @see       https://github.com/event-engine/php-inspectio-graph for the canonical source repository
 * @copyright https://github.com/event-engine/php-inspectio-graph/blob/master/COPYRIGHT.md
 * @license   https://github.com/event-engine/php-inspectio-graph/blob/master/LICENSE.md MIT License
 */

declare(strict_types=1);

namespace EventEngine\InspectioGraph;

interface VertexType
{
    public const TYPE_AGGREGATE = 'aggregate';
    public const TYPE_EVENT = 'event';
    public const TYPE_COMMAND = 'command';
    public const TYPE_DOCUMENT = 'document';
    public const TYPE_POLICY = 'policy';
    public const TYPE_EXTERNAL_SYSTEM = 'externalSystem';
    public const TYPE_HOT_SPOT = 'hotSpot';
    public const TYPE_ROLE = 'role';
    public const TYPE_UI = 'ui';
    public const TYPE_FEATURE = 'feature';
    public const TYPE_BOUNDED_CONTEXT = 'boundedContext';

    /**
     * Unique id in whole graph
     *
     * @return string
     */
    public function id(): string;

    /**
     * Vertex type, one of VertexType::TYPE_* constants
     *
     * @return string
     */
    public function type(): string;

    /**
     * Raw vertex label
     *
     * @return string
     */
    public function label(): string;

    /**
     * Filtered label for name
     *
     * @return string
     */
    public function name(): string;

    /**
     * Optional vertex metadata which can be JSON, XML, etc.
     *
     * @return ?string
     */
    public function metadata(): ?string;

    /**
     * Optional an instance of the given metadata
     *
     * @return Metadata\Metadata|null
     */
    public function metadataInstance(): ?Metadata\Metadata;

    /**
     * Use it only to sync latest data from event map
     *
     * @param VertexType $vertex
     */
    public function merge(VertexType $vertex): void;
}
