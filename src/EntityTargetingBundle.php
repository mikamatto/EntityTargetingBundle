<?php

namespace Mikamatto\EntityTargetingBundle;

use Mikamatto\EntityTargetingBundle\DependencyInjection\EntityTargetingExtension;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class EntityTargetingBundle extends Bundle
{
    public function getContainerExtension(): ?ExtensionInterface
    {
        return new EntityTargetingExtension();
    }
}