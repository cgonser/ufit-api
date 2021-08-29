<?php

namespace App\Core\ORM;

use Doctrine\ORM\Mapping\ClassMetaData;
use Doctrine\ORM\Query\Filter\SQLFilter;

class SoftDeletableFilter extends SQLFilter
{
    protected $entityManager;
    protected $disabled = array();

    /**
     * Add the soft deletable filter
     *
     * @param ClassMetaData $targetEntity
     * @param                $targetTableAlias
     * @return string
     */
    public function addFilterConstraint(ClassMetadata $targetEntity, $targetTableAlias): string
    {
        $class = $targetEntity->getName();
        if (array_key_exists($class, $this->disabled) && $this->disabled[$class] === true) {
            return '';
        } else {
            if (
                array_key_exists($targetEntity->rootEntityName, $this->disabled)
                && $this->disabled[$targetEntity->rootEntityName] === true
            ) {
                return '';
            } elseif (!$targetEntity->hasField('deletedAt')) {
                return '';
            }
        }

        $conn = $this->getEntityManager()->getConnection();
        $platform = $conn->getDatabasePlatform();
        $column = $targetEntity->getQuotedColumnName('deletedAt', $platform);

        return $platform->getIsNullExpression($targetTableAlias.'.'.$column);
    }

    protected function getEntityManager()
    {
        if ($this->entityManager === null) {
            $refl = new \ReflectionProperty(SQLFilter::class, 'em');
            $refl->setAccessible(true);
            $this->entityManager = $refl->getValue($this);
        }

        return $this->entityManager;
    }

    public function disableForEntity($class): void
    {
        $this->disabled[$class] = true;
    }

    public function enableForEntity($class): void
    {
        $this->disabled[$class] = false;
    }
}