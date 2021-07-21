<?php

/**
 * @see       https://github.com/event-engine/php-inspectio-graph for the canonical source repository
 * @copyright https://github.com/event-engine/php-inspectio-graph/blob/master/COPYRIGHT.md
 * @license   https://github.com/event-engine/php-inspectio-graph/blob/master/LICENSE.md MIT License
 */

declare(strict_types=1);

namespace EventEngineTest\InspectioGraph\Mock;

use EventEngine\InspectioGraph\Exception\RuntimeException;
use EventEngine\InspectioGraph\Metadata\Metadata;
use EventEngine\InspectioGraph\VertexType;

final class Vertex implements VertexType
{
    /**
     * @var string
     */
    protected $id;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var string
     */
    protected $label;

    /**
     * @var string
     */
    protected $name;

    /**
     * Arbitrary user metadata e. g. JSON schema
     *
     * @var string
     */
    protected $metadata;

    /**
     * @var mixed
     */
    protected $metadataInstance;

    public static function fromScalar(
        string $id,
        string $label,
        string $type,
        callable $filterName,
        string $metadata
    ): VertexType {
        $self = new self(
            $id,
            $type,
            $label,
            ($filterName)($label)
        );

        $self->metadata = $metadata;

        return $self;
    }

    protected function __construct(string $id, string $type, string $label, string $name)
    {
        $this->id = $id;
        $this->type = $type;
        $this->label = $label;
        $this->name = $name;
    }

    public function id(): string
    {
        return $this->id;
    }

    public function type(): string
    {
        return $this->type;
    }

    public function label(): string
    {
        return $this->label;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function metadata(): string
    {
        return $this->metadata;
    }

    public function metadataInstance(): ?Metadata
    {
        return $this->metadataInstance;
    }

    public function merge(VertexType $vertex): void
    {
        if ($vertex->id() !== $this->id()) {
            throw new RuntimeException(
                \sprintf('Can not merge vertex due different ids. Id is "%s" but got "%s"', $this->id, $vertex->id())
            );
        }
        if ($vertex->type() !== $this->type()) {
            throw new RuntimeException(
                \sprintf('Can not merge vertex due different types. Type is "%s" but got "%s"', $this->type, $vertex->type())
            );
        }

        $this->metadataInstance = $vertex->metadataInstance();
        $this->metadata = $vertex->metadata();
        $this->name = $vertex->name();
        $this->label = $vertex->label();
    }
}
