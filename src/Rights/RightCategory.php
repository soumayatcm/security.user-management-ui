<?php


namespace Mouf\Security\UserManagement\Rights;
use Mouf\Security\UserManagement\Api\DisplayableRight;

/**
 * A category of rights (only useful for UI display)
 */
class RightCategory
{
    /**
     * Name of the category.
     *
     * @var string
     */
    private $name;

    /**
     * Description of the category.
     *
     * @var string
     */
    private $description;

    /**
     * A list of displayable rights.
     *
     * @var DisplayableRight[]
     */
    private $rights;

    /**
     * RightCategory constructor.
     * @param string $name
     * @param string $description
     * @param DisplayableRight[] $rights
     */
    public function __construct($name, $description, array $rights)
    {
        $this->name = $name;
        $this->description = $description;
        $this->rights = $rights;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @return \Mouf\Security\UserManagement\Api\DisplayableRight[]
     */
    public function getRights(): array
    {
        return $this->rights;
    }
}