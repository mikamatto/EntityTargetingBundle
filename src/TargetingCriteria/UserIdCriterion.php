<?php

namespace Mikamatto\EntityTargetingBundle\TargetingCriteria;

use Mikamatto\EntityTargetingBundle\TargetCriteriaInterface;
use Mikamatto\EntityTargetingBundle\Entity\CriteriaAwareInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserIdCriterion implements TargetCriteriaInterface
{
    private ?int $userId = null;

    public function setParameters(array $parameters): void
    {
        $this->userId = $parameters['user_id'] ?? null;
    }

    public function isEligible(?UserInterface $user, CriteriaAwareInterface $entity): bool
    {
        if (!$user || $this->userId === null) {
            return false;
        }

        return method_exists($user, 'getId') && $user->getId() === $this->userId;
    }

    public function supports(string $targetAudience): bool
    {
        return $targetAudience === $this->getCriterionName();
    }

    public function getCriterionName(): string
    {
        return 'user_id';
    }

    public function getCriterionDescription(): ?string
    {
        return 'Targets a specific user based on their ID';
    }
}
