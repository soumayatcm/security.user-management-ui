<?php


namespace Mouf\Security\UserManagement;


use Mouf\Html\HtmlElement\HtmlElementInterface;
use Mouf\Html\Renderer\Renderable;
use Mouf\Security\UserManagement\Api\RoleInterface;
use Mouf\Security\UserService\UserInterface;

class EditRoleView implements HtmlElementInterface
{
    use Renderable;

    /**
     * @var UserInterface
     */
    private $role;

    /**
     * All existing rights in the application.
     * An array of array{ hasRight:true|false, right:RightInterface }
     *
     * @var array
     */
    private $rights;

    /**
     * @param RoleInterface $role
     * @param array $rights
     */
    public function __construct(RoleInterface $role, array $rights)
    {
        $this->role = $role;
        $this->rights = $rights;
    }

}
