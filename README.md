# EntityTargetingBundle

## Overview

The **EntityTargetingBundle** is a flexible service designed to manage a set of entities based on specified criteria. This bundle is intentionally designed to be bare, allowing developers to provide the entities themselves, as well as any CRUD operations or actions to display and manage those entities. This approach maximizes decoupling and flexibility, enabling developers to tailor the service to their specific application needs.

For example, suppose you have a set of messages you want to display at the top of your main page, but in a selective way based on who is making the request. You may want to display a certain message to guests, perhaps to persuade them to join. Alternatively, you might want to show a completely different message to registered users who belong to a specific group, informing them about a promotion running just for them. Additionally, you may need to display another message to users who haven’t confirmed their details, indicating that their account may be closed if they don't do it.

By using this bundle, you can assign criteria to each message without writing extensive logic to handle each case. The logic for determining eligibility comes built-in and operates transparently, allowing you to focus on delivering relevant content to your users.

## Features
- Flexible Entity Management: The bundle allows you to manage a variety of entities based on user-defined criteria, ensuring that your application can cater to different audiences without hardcoding logic.
- Native Criteria Support: Comes with pre-defined criteria such as `guests_only` and `user_roles`, which can be easily applied to your entities to control visibility based on user status and roles.
- Custom Criteria Creation: You can easily create your own criteria by implementing the provided `TargetCriteriaInterface`. This allows for endless customization tailored to your application's needs.
- Caching Mechanism: The bundle includes an optional caching layer to improve performance, enabling faster access to targeted entities. The caching duration can be configured via the application's configuration files.
- Decoupled Architecture: Designed for maximum decoupling, the bundle allows developers to provide entities and CRUD operations independently, promoting a clean separation of concerns within the application.
- Automatic Tagging: Utilizes Symfony’s automatic tagging feature to simplify the management of criteria classes, making it easy to extend functionality by simply adding new criteria.
- Easy Configuration: The bundle can be easily configured in your application’s services.yaml, allowing for quick adjustments to fit your project’s requirements.

## Installation

To install the bundle, run the following command:

```bash
composer require mikamatto/entity-targeting-bundle
```
If you're using Symfony Flex, the bundle will be enabled automatically. Otherwise, you may need to manually enable it in your `config/bundles.php`:

```php
return [
    // Other bundles...
    Mikamatto\EntityTargetingBundle\EntityTargetingBundle::class => ['all' => true],
];
```

### Configuration

To configure the bundle, you need to set up the parameters in your `config/packages/entity_targeting.yaml`:

```yaml
entity_targeting:
    enable_cache: true  # Enable or disable caching
    cache_expiration: 3600  # Default cache expiration time in seconds
```

## Interfaces

### CriteriaAwareInterface

The `CriteriaAwareInterface` is designed for entities that will be managed by the EntityTargetingBundle. Implementing this interface enables the bundle to determine the eligibility of each entity based on defined criteria.

```php
namespace Mikamatto\EntityTargetingBundle\Entity;

interface CriteriaAwareInterface
{
    public function getCriterion(): string;
    public function setCriterion(string $criterion): self;
    public function getCriterionParams(): array;
    public function setCriterionParams(?string $params): self;
}
```
- **getCriterion()**: string
This method returns the label or name of a criterion that applies to the entity. The label should match a criterion registered with the bundle, such as `guests_only` or`user_roles` (or any custom criteria you define).
- **setCriterion(string $criterion)**: self
Setter method for the above property.
- **getCriterionParams()**: array
This method should return an associative array of parameters expected by the selected criterion. The values in this array configure how the criterion will be applied to the entity and depend on the specific criterion.
- **setCriterionParams(?string $params)**: self
Setter method for the criterion parameters. Its value must be passed as a string matching a valid JSON structure. 


### CriteriaRepositoryInterface

The `CriteriaRepositoryInterface` allows for repository classes to be compatible with the EntityTargetingBundle. Any repository managing entities targeted by this bundle should implement this interface, ensuring the bundle can fetch entities based on criteria configurations seamlessly.

```php
namespace Mikamatto\EntityTargetingBundle\Repository;

interface CriteriaRepositoryInterface
{
    public function getEntities(): array;  // Method to fetch active entities
}
```
### TargetCriteriaInterface

The `TargetCriteriaInterface` defines the structure for all criteria that can be applied to target entities based on specific parameters and conditions. Implementing this interface allows the creation of custom targeting logic by defining eligibility criteria and associating entities with a particular criterion.

```php
interface TargetCriteriaInterface
{
    public function setParameters(array $parameters): void;
    
    public function isEligible(?UserInterface $user, CriteriaAwareInterface $entity): bool;

    public function supports(string $targetAudience): bool;

    public function getCriterionName(): string;

    public function getCriterionDescription(): ?string;
}
```

#### Explanation of Methods

- `setParameters(array $parameters)`: void: Sets the parameters for the criterion, such as roles or mode for a user_roles criterion. Each implementation should handle parameters specific to its functionality.
- `isEligible(?UserInterface $user, CriteriaAwareInterface $entity)`: bool: Determines if the entity is eligible based on the criterion’s parameters and any relevant user attributes. This method drives the core eligibility check for an entity and user.
- `supports(string $targetAudience)`: bool: Verifies if the criterion supports the provided audience target, helping to match criteria with specific entity types or conditions.
- `getCriterionName()`: string: Provides a unique identifier for the criterion, used for associating criteria with entities in a consistent and meaningful way.
- `getCriterionDescription()`: ?string: Provides a text description for the criterion, if available.

This interface enables the definition of reusable, custom criteria within the bundle, while still supporting native, configurable criteria like `guests_only` and `user_roles`.

# Natively Available Criteria

This bundle provides some native criteria to handle common use cases out of the box. Each criterion has specific requirements for its parameters.

## Guests Only Criterion

The `guests_only` criterion targets non-authenticated users (i.e., users who are not logged in).

- Label: **guests_only**
- Parameters: None required

## User Roles Criterion

The `user_roles` criterion targets users based on their assigned roles, with an option to consider Symfony’s role hierarchy.

- Label: **user_roles**
- Parameters:
	- **roles** (array): An array of role strings that the user must have. Examples: ['ROLE_USER'], ['ROLE_ADMIN', 'ROLE_MANAGER'].
	- **mode** (string, default ANY): The mode of role matching, which can be:
    	- ALL: The user must have all of the specified roles.
    	- ANY: The user must have at least one of the specified roles.
	- **hierarchy** (boolean, default true): If true, Symfony’s role hierarchy will be used to check if the user has inherited roles (e.g., a user with ROLE_SUPER_ADMIN would satisfy a check for ROLE_ADMIN). If false, only the user’s explicitly assigned roles will be considered.

# Define Your Own Custom Made Criteria

To create your own criteria, implement the TargetingCriterionInterface. Your custom criteria should define how to determine eligibility based on the entity and user context.

Example:
```php
namespace App\TargetingCriteria;

use Mikamatto\EntityTargetingBundle\TargetCriteriaInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Mikamatto\EntityTargetingBundle\CriteriaAwareInterface;

class CustomCriterion implements TargetCriteriaInterface
{
    private array $parameters = [];

    /**
     * Sets the parameters for the current criterion.
     *
     * @param array $parameters
     */
    public function setParameters(array $parameters): void
    {
        $this->parameters = $parameters;
    }

    /**
     * Determines if the current entity is eligible based on custom logic.
     *
     * @param UserInterface|null $user
     * @param CriteriaAwareInterface $entity
     * @return bool
     */
    public function isEligible(?UserInterface $user, CriteriaAwareInterface $entity): bool
    {
        // Custom eligibility logic using $user, $entity, and $this->parameters
        if (!$user) {
            return false;
        }

        // Example logic: allow if user has a custom role or attribute
        return in_array('ROLE_SPECIAL', $user->getRoles(), true);
    }

    /**
     * Checks if the criterion supports the given target audience.
     *
     * @param string $targetAudience
     * @return bool
     */
    public function supports(string $targetAudience): bool
    {
        // Specify if this criterion is meant to support a specific target audience.
        // Example: only applicable to 'special_users' audience
        return $targetAudience === 'special_users';
    }

    /**
     * Retrieves the name of this criterion.
     *
     * @return string
     */
    public function getCriterionName(): string
    {
        return 'custom_criterion';
    }
}
```
# List Registered Criteria

In order to list all the available criteria (for example, for populating a selector with options), a service which returns an array of the objects tagged as app.targeting_criterion is available at `Mikamatto\EntityTargetingBundle\Service\TargetingCriteriaProvider`

The TargetingCriteriaProvider service enables you to retrieve a list of registered targeting criteria in a structured format, which is helpful for managing and displaying available criteria options within your application.

This service leverages the `#[AutowireIterator]` attribute to automatically inject all services tagged with `app.targeting_criterion`, making it easy to register and retrieve criteria dynamically.

## Usage
To use this service, ensure that your criteria classes are tagged with app.targeting_criterion in the service configuration:
```yaml
# config/services.yaml
App\Targeting\Criterion\YourCriterionClass:
    tags: ['app.targeting_criterion']
```
The `listCriteria()` method on TargetingCriteriaProvider returns all registered criteria as an array, with each criterion containing:
- **name**: The unique name of the criterion.
- **class**: The class name of the criterion.
- **description**: A brief description of the criterion.
```php
// Example usage
$criteriaProvider = $container->get(TargetingCriteriaProvider::class);
$criteriaList = $criteriaProvider->listCriteria();

foreach ($criteriaList as $criterion) {
    echo "Name: " . $criterion['name'] . "\n";
    echo "Class: " . $criterion['class'] . "\n";
    echo "Description: " . $criterion['description'] . "\n\n";
}
```
Example output:
```json
[
    {
        "name": "LocationCriterion",
        "class": "App\\Targeting\\Criterion\\LocationCriterion",
        "description": "Filters users based on their location."
    },
    {
        "name": "AgeCriterion",
        "class": "App\\Targeting\\Criterion\\AgeCriterion",
        "description": "Filters users based on their age."
    }
]
```
This service provides a consistent and extensible way to manage and display all available targeting criteria within the application. By adding new criteria classes and tagging them with `app.targeting_criterion`, they will automatically be included in the output from listCriteria() without any additional configuration.

# Example Usage

Suppose we create a Notification entity that implements the CriteriaAwareInterface to target specific users based on roles. Here’s a simple example of such an entity:

```php
namespace App\Entity;

use Mikamatto\EntityTargetingBundle\CriteriaAwareInterface;

class Notification implements CriteriaAwareInterface
{
    private string $criterion;
    private array $criterionParams;

    public function __construct(string $criterion, array $criterionParams = [])
    {
        $this->criterion = $criterion;
        $this->criterionParams = $criterionParams;
    }

    public function getCriterion(): string
    {
        return $this->criterion;
    }

    public function getCriterionParams(): array
    {
        return $this->criterionParams;
    }
}
```
// Example of a CriterionAware entity targeting guests only:
```php
$notification = new Notification('guests_only');
```
Example of a CriterionAware entity targeting users with certain roles:
```php
$notification = new Notification(
    'user_roles',
    [
        'roles' => ['ROLE_ADMIN', 'ROLE_MANAGER'],
        'mode' => 'ANY'
    ]
);
```

This design allows easy configuration of native or custom criteria in your application. Custom criteria can also be added by implementing the `TargetCriteriaInterface` and registering them with the bundle.

# License

This bundle is licensed under the MIT License. See the LICENSE file for more details.

# Contributing

Contributions are welcome! Please open an issue or submit a pull request for any improvements or suggestions.



