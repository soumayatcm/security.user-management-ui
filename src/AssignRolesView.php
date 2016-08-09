<?php


namespace Mouf\Security\UserManagement;


use Mouf\Html\HtmlElement\HtmlElementInterface;
use Mouf\Html\Renderer\Renderable;
use Mouf\Security\UserManagement\Api\RoleInterface;
use Mouf\Security\UserService\UserInterface;

class AssignRolesView implements HtmlElementInterface
{
    use Renderable;

    /**
     * @var UserInterface
     */
    private $user;

    /**
     * All existing roles in the application.
     * An array of array{ hasRole:true|false, role:RoleInterface }
     *
     * @var array
     */
    private $roles;

    /**
     * @param UserInterface $user
     * @param array $roles
     */
    public function __construct(UserInterface $user, array $roles)
    {
        $this->user = $user;
        $this->roles = $roles;
    }

}
