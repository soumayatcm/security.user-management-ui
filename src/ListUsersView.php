<?php


namespace Mouf\Security\UserManagement;


use Mouf\Html\HtmlElement\HtmlElementInterface;
use Mouf\Html\Renderer\Renderable;
use Mouf\Html\Widgets\EvoluGrid\EvoluGrid;
use Mouf\Security\UserManagement\Api\RoleInterface;
use Mouf\Security\UserService\UserInterface;

class ListUsersView implements HtmlElementInterface
{
    use Renderable;

    /**
     * @var EvoluGrid
     */
    private $grid;

    /**
     * ListUsersView constructor.
     * @param EvoluGrid $grid
     */
    public function __construct(EvoluGrid $grid)
    {
        $this->grid = $grid;
    }


}
