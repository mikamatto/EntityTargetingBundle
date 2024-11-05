<?php

namespace Mikamatto\EntityTargetingBundle;

use Mikamatto\EntityTargetingBundle\Entity\CriteriaAwareInterface;
use Symfony\Component\Security\Core\User\UserInterface;

interface TargetCriteriaInterface
{
    /**
     * Sets the parameters for the current criterion
     *
     * @param array $parameters
     * @return void
     */
    public function setParameters(array $parameters): void;
    
    /**
     * Determines if the current entity is eligible for targeting using the current criterion
     *
     * @param UserInterface|null $user
     * @param CriteriaAwareInterface $entity
     * @return boolean
     */
    public function isEligible(?UserInterface $user, CriteriaAwareInterface $entity): bool;

    /**
     * Determines if the current criterion supports the provided target audience
     *
     * @param string $targetAudience
     * @return boolean
     */
    public function supports(string $targetAudience): bool;

    /**
     * Retrieves the name of the current criterion
     *
     * @return string
     */
    public function getCriterionName(): string;

    /**
     * Retrieves the description of the current criterion
     *
     * @return string
     */
    public function getCriterionDescription(): ?string;
}