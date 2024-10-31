<?php

namespace Mikamatto\EntityTargetingBundle;

class TargetCriteriaFactory
{

    public function __construct(private iterable $criteria)
    {
    }

    /**
     * Provides a target criteria instance based on the criterion name, and initializes it with the provided parameters.
     *
     * @param string $criterionName
     * @param array $parameters
     * @return TargetCriteriaInterface|null
     */
    public function create(string $criterionName, array $parameters = []): ?TargetCriteriaInterface
    {
        foreach ($this->criteria as $criterion) {
            if ($criterion->getCriterionName() === $criterionName) {
                $criterion->setParameters($parameters);
                return $criterion;
            }
        }

        throw new \InvalidArgumentException("Criterion '{$criterionName}' is not recognized.");
    }
}