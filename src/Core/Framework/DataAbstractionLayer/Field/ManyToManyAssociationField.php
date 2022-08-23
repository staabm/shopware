<?php declare(strict_types=1);

namespace Shopware\Core\Framework\DataAbstractionLayer\Field;

use Shopware\Core\Framework\DataAbstractionLayer\Dbal\FieldResolver\ManyToManyAssociationFieldResolver;
use Shopware\Core\Framework\DataAbstractionLayer\DefinitionInstanceRegistry;
use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\FieldSerializer\ManyToManyAssociationFieldSerializer;

class ManyToManyAssociationField extends AssociationField
{
    /**
     * @var EntityDefinition
     */
    private $mappingDefinition;

    /**
     * @var string
     */
    private $mappingLocalColumn;

    /**
     * @var string
     */
    private $mappingReferenceColumn;

    /**
     * @var string
     */
    private $sourceColumn;

    /**
     * @var string
     */
    private $toManyDefinitionClass;

    /**
     * @var EntityDefinition
     */
    private $toManyDefinition;

    public function __construct(
        string $propertyName,
        string $referenceDefinition,
        string $mappingDefinition,
        string $mappingLocalColumn,
        string $mappingReferenceColumn,
        string $sourceColumn = 'id',
        string $referenceField = 'id'
    ) {
        parent::__construct($propertyName);
        $this->toManyDefinitionClass = $referenceDefinition;
        $this->referenceClass = $mappingDefinition;
        $this->mappingLocalColumn = $mappingLocalColumn;
        $this->mappingReferenceColumn = $mappingReferenceColumn;
        $this->sourceColumn = $sourceColumn;
        $this->referenceField = $referenceField;
    }

    public function compile(DefinitionInstanceRegistry $registry): void
    {
        parent::compile($registry);
    }

    public function getToManyReferenceDefinition(): EntityDefinition
    {
        $this->compileLazy();

        return $this->toManyDefinition;
    }

    public function getMappingDefinition(): EntityDefinition
    {
        $this->compileLazy();

        return $this->mappingDefinition;
    }

    public function getMappingLocalColumn(): string
    {
        return $this->mappingLocalColumn;
    }

    public function getMappingReferenceColumn(): string
    {
        return $this->mappingReferenceColumn;
    }

    public function getLocalField(): string
    {
        return $this->sourceColumn;
    }

    protected function getSerializerClass(): string
    {
        return ManyToManyAssociationFieldSerializer::class;
    }

    protected function getResolverClass(): ?string
    {
        return ManyToManyAssociationFieldResolver::class;
    }

    protected function compileLazy(): void
    {
        parent::compileLazy();

        if ($this->mappingDefinition === null) {
            $this->mappingDefinition = $this->getReferenceDefinition();
        }

        if ($this->toManyDefinition === null) {
            $this->toManyDefinition = $this->registry->getByClassOrEntityName($this->toManyDefinitionClass);
        }

        $this->toManyDefinitionClass = $this->toManyDefinition->getClass();
    }
}
