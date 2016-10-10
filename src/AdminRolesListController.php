<?php


namespace Mouf\Security\UserManagement;


use Mouf\Html\HtmlElement\HtmlBlock;
use Mouf\Html\Template\TemplateInterface;
use Mouf\Html\Widgets\EvoluGrid\EvoluGrid;
use Mouf\Html\Widgets\EvoluGrid\EvoluGridResultSet;
use Mouf\Html\Widgets\EvoluGrid\SimpleColumn;
use Mouf\Mvc\Splash\Annotations\Get;
use Mouf\Mvc\Splash\Annotations\Post;
use Mouf\Mvc\Splash\Annotations\URL;
use Mouf\Mvc\Splash\Exception\PageNotFoundException;
use Mouf\Mvc\Splash\HtmlResponse;
use Mouf\Security\Right;
use Mouf\Security\UserManagement\Api\RoleDao;
use Mouf\Security\UserManagement\Api\RoleListDao;
use Mouf\Security\UserManagement\Api\UserListDao;
use Mouf\Security\UserService\UserDaoInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use Zend\Diactoros\Response\RedirectResponse;

/**
 * Controller in charge of listing roles.
 *
 * @Right("CAN_ACCESS_ADMIN_ROLES_LIST")
 */
class AdminRolesListController
{
    protected $baseUrl;

    /**
     * The logger used by this controller.
     *
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * The template used by this controller.
     *
     * @var TemplateInterface
     */
    protected $template;

    /**
     * The main content block of the page.
     *
     * @var HtmlBlock
     */
    protected $content;

    /**
     * @var RoleListDao
     */
    protected $roleDao;

    /**
     * @var EvoluGrid
     */
    protected $grid;

    /**
     * @var EvoluGridResultSet
     */
    protected $resultSet;

    /**
     * @param LoggerInterface $logger The logger
     * @param TemplateInterface $template The template used by this controller
     * @param HtmlBlock $content The main content block of the page
     * @param string $baseUrl The base URL for this container (defaults to "user_admin")
     */
    public function __construct(LoggerInterface $logger, TemplateInterface $template, HtmlBlock $content, RoleListDao $roleDao, EvoluGrid $grid, EvoluGridResultSet $resultSet, string $baseUrl = 'roles_admin')
    {
        $this->logger = $logger;
        $this->template = $template;
        $this->content = $content;
        $this->roleDao = $roleDao;
        $this->grid = $grid;
        $this->resultSet = $resultSet;
        $this->baseUrl = $baseUrl;
    }

    /**
     * Displays the HTML screen allowing to edit a user roles.
     *
     * @URL("{$this->baseUrl}/")
     * @Get()
     */
    public function index() : ResponseInterface
    {
        $view = new ListRolesView($this->grid);

        $this->content->addHtmlElement($view);
        return new HtmlResponse($this->template);
    }

    /**
     * Displays the HTML screen allowing to edit a user roles.
     *
     * @URL("{$this->baseUrl}/list")
     * @Get()
     */
    public function list($offset, $limit, $sortKey, $sortOrder, string $q = null) : ResponseInterface
    {
        $roles = $this->roleDao->search([
            'q' => $q
        ], (string) $sortKey, (string) $sortOrder);

        $results = $roles->take($offset, $limit);

        $this->resultSet->setResults($results);

        return $this->resultSet->getResponse();
    }

}
