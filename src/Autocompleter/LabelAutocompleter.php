<?php

namespace App\Autocompleter;

use App\Entity\Therapy\Label;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Security\Core\Security;
use Symfony\UX\Autocomplete\EntityAutocompleterInterface;


class LabelAutocompleter implements EntityAutocompleterInterface
{
    public function getEntityClass(): string
    {
        return Label::class;
    }

    public function createFilteredQueryBuilder(EntityRepository $repository, string $query): QueryBuilder
    {
        return $repository->findLabelsByRequest($query, true);
    }

    public function getLabel(object $entity): string
    {
        return $entity->getShortName();
    }

    public function getValue(object $entity): string
    {
        return $entity->getId();
    }

    public function isGranted(Security $security): bool
    {
        return true;
    }
}