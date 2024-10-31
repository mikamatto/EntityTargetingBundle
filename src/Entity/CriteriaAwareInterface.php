<?php

namespace Mikamatto\EntityTargetingBundle\Entity;

interface CriteriaAwareInterface {
    /**
     * Retrieves the criterion name for targeting the current entity
     *
     * @return string|null
     */
    public function getCriterion(): ?string;

    /**
     * Retrieves the criterion parameters (if any) for targeting the current entity
     *
     * @return array|null
     */
    public function getCriterionParams(): ?array;
}