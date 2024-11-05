<?php

namespace Mikamatto\EntityTargetingBundle\Entity;

use Mikamatto\EntityTargetingBundle\Repository\CriteriaRepositoryInterface;

interface CriteriaAwareInterface {
    /**
     * Retrieves the criterion name for targeting the current entity
     *
     * @return string|null
     */
    public function getCriterion(): string;

    /**
     * Sets the criterion name for targeting the current entity
     *
     * @param string $criterion - The criterion name
     * @return self
     */
    public function setCriterion(string $criterion): self;

    /**
     * Retrieves the criterion parameters (if any) for targeting the current entity
     *
     * @return array|null
     */
    public function getCriterionParams(): ?array;

    /**
     * Sets the criterion parameters (if any) for targeting the current entity
     *
     * @param string|null $params - The criterion parameters as a JSON string
     * @return self
     */
    public function setCriterionParams(?string $params): self;
}