<?php

namespace Mouf\Security\UserManagement\Rights;

use Mouf\Security\RightsService\RightInterface;
use Mouf\Security\UserManagement\Api\RightDao;

/**
 * This class registers all available rights in Mouf, in a hierarchical way.
 */
class HierarchicalRightsRegistry implements RightDao
{
    /**
     * The list of all supported rights in the application, indexed by right name.
     *
     * @var RightInterface[]
     */
    protected $rights;

    /**
     * @var RightCategory[]
     */
    protected $rightCategories;

    /**
     * @param RightCategory[] $rightCategories The list of all right categories in the application.
     */
    public function __construct(array $rightCategories)
    {
        $this->rightCategories = $rightCategories;

        $this->rights = [];
        foreach ($rightCategories as $rightCategory) {
            foreach ($rightCategory->getRights() as $right) {
                $this->rights[$right->getName()] = $right;
            }
        }
    }

    /**
     * Returns a right by name.
     *
     * @param string $name
     *
     * @return RightInterface
     *
     * @throws NotFoundException
     */
    public function get(string $name) : RightInterface
    {
        if (!isset($this->rights[$name])) {
            throw NotFoundException::create($name);
        }

        return $this->rights[$name];
    }

    /**
     * Returns a list of all rights
     *
     * @return RightInterface[]
     */
    public function getAllRights()
    {
        return $this->rights;
    }

    /**
     * @return RightCategory[]
     */
    public function getRightCategories(): array
    {
        return $this->rightCategories;
    }
}
