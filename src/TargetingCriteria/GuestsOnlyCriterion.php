<?php

namespace Mikamatto\EntityTargetingBundle\TargetingCriteria;

use Mikamatto\EntityTargetingBundle\TargetCriteriaInterface;
use Mikamatto\EntityTargetingBundle\Entity\CriteriaAwareInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class GuestsOnlyCriterion implements TargetCriteriaInterface
{
    public function setParameters(array $parameters): void
    {
    }

    public function isEligible(?UserInterface $user, CriteriaAwareInterface $entity): bool
    {
        return !$user || !method_exists($user, 'getUsername');
    }

    public function supports(string $targetAudience): bool
    {
        return $targetAudience === $this->getCriterionName();
    }

    public function getCriterionName(): string
    {
        return 'guests_only';
    }

    public function getCriterionDescription(): ?string
    {
        return 'Targets only users who are not logged in';
    }
}