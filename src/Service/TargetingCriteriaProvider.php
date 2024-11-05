<?php

namespace Mikamatto\EntityTargetingBundle\Service;

use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;

class TargetingCriteriaProvider
{
    public function __construct(
        #[AutowireIterator(tag: 'app.targeting_criterion')] private iterable $criteria
    ) {
    }

    public function listCriteria(): array
    {
        $criteriaList = [];
        foreach ($this->criteria as $criterion) {
            $choices[$criterion->getCriterionName()] = [
                'name' => $criterion->getCriterionName(),
                'class' => get_class($criterion),
                'description' => $criterion->getDescription(),
            ];
        }
        return $criteriaList;
    }
}