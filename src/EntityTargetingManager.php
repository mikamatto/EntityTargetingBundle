<?php

namespace Mikamatto\EntityTargetingBundle;

use Mikamatto\EntityTargetingBundle\Repository\CriteriaRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\Cache\ItemInterface;

class EntityTargetingManager {

    private ?CriteriaRepositoryInterface $repository = null; 

    public function __construct(
        private TargetCriteriaFactory $criteriaFactory,
        private CacheInterface $cache, 
        private EntityManagerInterface $em,
        private bool $cacheEnabled,
        private ?int $cacheExpiration
    ) {}

    /**
     * Sets the repository to use for targeted entities.
     * The repository must implement CriteriaRepositoryInterface.
     *
     * @param string $className
     * @return void
     */
    public function setRepository(string $className): void {
        $repository = $this->em->getRepository($className);

        if (!$repository instanceof CriteriaRepositoryInterface) {
            throw new \InvalidArgumentException(sprintf(
                'The repository for class %s must implement %s.',
                $className,
                CriteriaRepositoryInterface::class
            ));
        }

        $this->repository = $repository;
    }

    /**
     * Retrieves the targeted entities based on the criteria that apply,
     * conditionally using the cache if enabled.
     *
     * @param UserInterface|null $user
     * @return array
     */
    public function getTargetedEntities(?UserInterface $user, array $params = []): array {
        if ($this->repository === null) {
            throw new \LogicException('Repository has not been set.');
        }

        if (!$this->cacheEnabled) {
            // No caching - just retrieve directly
            return $this->retrieveTargetedEntities($user, $params);
        }
    
        // Caching enabled - use cache with configured expiration
        return $this->cache->get('targeted_entities', function (ItemInterface $item) use ($user, $params) {
            $item->expiresAfter($this->cacheExpiration);
            return $this->retrieveTargetedEntities($user, $params);
        });
    }

    /**
     * Manually invalidates the cache for targeted entities.
     *
     * @return void
     */
    public function invalidateCache(): void {
        $this->cache->delete('targeted_entities');
    }

    /**
     * Retrieves the targeted entities based on the criteria that apply.
     *
     * @param UserInterface|null $user
     * @return array
     */
    private function retrieveTargetedEntities(?UserInterface $user, array $params = []): array
    {
        $entities = $this->repository->getEntities($params);

        return array_filter($entities, function ($entity) use ($user) {
            $criterion = $entity->getCriterion();
            $params = $entity->getCriterionParams();

            // Show to all if criterion is null
            if ($criterion === null) {
                return true;
            }

            // Use the factory to create the audience criterion
            $audienceCriterion = $this->criteriaFactory->create($criterion, $params);
            return $audienceCriterion->isEligible($user, $entity);
        });
    }
}