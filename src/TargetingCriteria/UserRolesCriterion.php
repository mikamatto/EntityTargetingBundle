<?php

namespace Mikamatto\EntityTargetingBundle\TargetingCriteria;

use Mikamatto\EntityTargetingBundle\TargetCriteriaInterface;
use Mikamatto\EntityTargetingBundle\Entity\CriteriaAwareInterface;
use Symfony\Component\Security\Core\Role\RoleHierarchyInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserRolesCriterion implements TargetCriteriaInterface
{
    private array $roles;
    private string $mode;
    private bool $includeHierarchy;
    private RoleHierarchyInterface $roleHierarchy;

    public function __construct(RoleHierarchyInterface $roleHierarchy)
    {
        $this->roleHierarchy = $roleHierarchy;
    }

    public function setParameters(array $parameters): void
    {
        $this->roles = array_map('strtoupper', $parameters['roles'] ?? []);
        $this->mode = $parameters['mode'] ?? 'ANY';
        $this->includeHierarchy = $parameters['hierarchy'] ?? true;
    }

    public function isEligible(?UserInterface $user, CriteriaAwareInterface $entity): bool
    {
        if (!$user) {
            return false;
        }

        $userRoles = $this->includeHierarchy ? $this->roleHierarchy->getReachableRoleNames($user->getRoles()) : $user->getRoles();
        $userRoles = array_map('strtoupper', $userRoles);

        if ($this->mode === 'ALL') {
            return !array_diff($this->roles, $userRoles);
        }

        if ($this->mode === 'ANY') {
            return !empty(array_intersect($this->roles, $userRoles));
        }

        return false; // or throw an exception for unsupported mode
    }

    public function supports(string $targetAudience): bool
    {
        return $targetAudience === $this->getCriterionName();
    }

    public function getCriterionName(): string
    {
        return 'user_roles';
    }

    public function getCriterionDescription(): ?string
    {
        return 'Targets authenticated users based on their roles';
    }
}