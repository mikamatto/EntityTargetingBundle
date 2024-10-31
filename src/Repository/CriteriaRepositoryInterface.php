<?php

namespace Mikamatto\EntityTargetingBundle\Repository;

interface CriteriaRepositoryInterface {

    /**
     * Retrieves the candidate entities for targeting
     *
     * @return array
     */
    public function getEntities(): array;
}