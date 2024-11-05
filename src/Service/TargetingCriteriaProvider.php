<?php

namespace Mikamatto\EntityTargetingBundle\Service;

class TargetingCriteriaProvider
{
    public function __construct(private iterable $criteria) 
    {
    }

    public function listCriteria(): array
    {
        $criteriaList = [];
        foreach ($this->criteria as $criterion) {
            $choices[$criterion->getCriterionName()] = [
                'name' => $criterion->getCriterionName(),
                'class' => get_class($criterion),
                'description' => $criterion->getCriterionDescription(),
            ];
        }
        return $criteriaList;
    }
}