<?php


namespace Mouf\Security\UserManagement;


use Mouf\Html\HtmlElement\HtmlBlock;
use Mouf\Html\Template\TemplateInterface;
use Mouf\Mvc\Splash\Annotations\Get;
use Mouf\Mvc\Splash\Annotations\Post;
use Mouf\Mvc\Splash\Annotations\URL;
use Mouf\Mvc\Splash\Exception\PageNotFoundException;
use Mouf\Mvc\Splash\HtmlResponse;
use Mouf\Security\Right;
use Mouf\Security\UserManagement\Api\RightDao;
use Mouf\Security\UserManagement\Api\RoleDao;
use Mouf\Security\UserManagement\Api\RoleRightDao;
use Mouf\Security\UserService\UserDaoInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use Zend\Diactoros\Response\RedirectResponse;

/**
 * Controller in charge of editing a role (change the name and the list of rights)
 *
 * @Right("CAN_EDIT_ROLE")
 */
class EditRoleController
{
    private $baseUrl;

    /**
     * The URL we are redirected to when the "Save" or the "Back" button is pressed.
     * This URL is relative to the "root" url.
     *
     * @var string
     */
    private $backUrl;

    /**
     * The logger used by this controller.
     *
     * @var LoggerInterface
     */
    private $logger;

    /**
     * The template used by this controller.
     *
     * @var TemplateInterface
     */
    private $template;

    /**
     * The main content block of the page.
     *
     * @var HtmlBlock
     */
    private $content;

    /**
     * @var UserDaoInterface
     */
    private $userDao;

    /**
     * @var RoleDao
     */
    private $roleDao;

    /**
     * @var RoleRightDao
     */
    private $roleRightDao;

    /**
     * @var RightDao
     */
    private $rightDao;

    /**
     * @param LoggerInterface $logger The logger
     * @param TemplateInterface $template The template used by this controller
     * @param HtmlBlock $content The main content block of the page
     * @param string $baseUrl The base URL for this container (defaults to "role_admin")
     */
    public function __construct(LoggerInterface $logger, TemplateInterface $template, HtmlBlock $content, RoleDao $roleDao, RoleRightDao $roleRightDao, RightDao $rightDao, string $backUrl = 'role_admin/', string $baseUrl = 'role_admin')
    {
        $this->logger = $logger;
        $this->template = $template;
        $this->content = $content;
        $this->roleDao = $roleDao;
        $this->roleRightDao = $roleRightDao;
        $this->rightDao = $rightDao;
        $this->backUrl = $backUrl;
        $this->baseUrl = $baseUrl;
    }

    /**
     * Displays the HTML screen allowing to edit a user roles.
     *
     * @URL("{$this->baseUrl}/{roleId}")
     * @Get()
     *
     * @param string $roleId
     *
     * @throws PageNotFoundException
     *
     * @return ResponseInterface
     */
    public function viewRole(string $roleId) : ResponseInterface
    {
        $role = $this->roleDao->getRoleById($roleId);
        if ($role === null) {
            throw new PageNotFoundException(sprintf('Could not find user with ID %s',$roleId));
        }

        $allRights = $this->rightDao->getAllRights();

        $roleRights = $this->roleRightDao->getRightsForRole($role);

        $roleRightsByName = [];
        foreach ($roleRights as $right) {
            $roleRightsByName[$right->getRightKey()] = $right;
        }

        $rights = [];
        foreach ($allRights as $right) {
            $rights[] = [
                'hasRight' => isset($roleRightsByName[$right->getName()]),
                'right' => $right
            ];
        }

        $view = new EditRoleView($role, $rights);

        $this->content->addHtmlElement($view);
        return new HtmlResponse($this->template);
    }

    /**
     *
     * @URL("{$this->baseUrl}/{roleId}")
     * @Post()
     *
     * @param string $roleId
     * @param string[] $rights
     *
     * @return ResponseInterface
     * @throws PageNotFoundException
     */
    public function editRole(string $roleId, array $rights = array()) : ResponseInterface
    {
        $role = $this->roleDao->getRoleById($roleId);
        if ($role === null) {
            throw new PageNotFoundException(sprintf('Could not find role with ID %s', $roleId));
        }

        $rightArr = [];

        foreach ($rights as $rightName) {
            $rightArr[] = $this->rightDao->get($rightName);
        }

        $this->roleRightDao->setRightsForRole($role, $rightArr);

        return new RedirectResponse('../../'.ltrim($this->backUrl,'/'));
    }
}
