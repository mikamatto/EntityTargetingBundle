<?php

namespace Mikamatto\EntityTargetingBundle\Repository;

interface CriteriaRepositoryInterface {

    /**
     * Retrieves the entities that match the given criteria
     *
     * @param array $params - Any custom parameters
     * @return array
     */
    public function getEntities(array $params = []): array;
}