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
use Mouf\Security\UserManagement\Api\RoleDao;
use Mouf\Security\UserService\UserDaoInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use Zend\Diactoros\Response\RedirectResponse;

/**
 * Controller in charge of assigning roles to a given user.
 *
 * @Right("CAN_ASSIGN_ROLES")
 */
class AssignRolesController
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
     * @param LoggerInterface $logger The logger
     * @param TemplateInterface $template The template used by this controller
     * @param HtmlBlock $content The main content block of the page
     * @param string $baseUrl The base URL for this container (defaults to "user_admin")
     */
    public function __construct(LoggerInterface $logger, TemplateInterface $template, HtmlBlock $content, RoleDao $roleDao, UserDaoInterface $userDao, string $backUrl = 'user_admin/', string $baseUrl = 'user_admin')
    {
        $this->logger = $logger;
        $this->template = $template;
        $this->content = $content;
        $this->roleDao = $roleDao;
        $this->userDao = $userDao;
        $this->backUrl = $backUrl;
        $this->baseUrl = $baseUrl;
    }

    /**
     * Displays the HTML screen allowing to edit a user roles.
     *
     * @URL("{this->baseUrl}/{userId}/roles")
     * @Get()
     *
     * @param string $user_id
     *
     * @throws PageNotFoundException
     */
    public function viewRoles(string $userId) : ResponseInterface
    {
        $user = $this->userDao->getUserById($userId);
        if ($user === null) {
            throw new PageNotFoundException(sprintf('Could not find user with ID %s',$userId));
        }

        $allRoles = $this->roleDao->getAllRoles();

        $userRoles = $this->roleDao->getRoles($user);

        $userRolesById = [];
        foreach ($userRoles as $role) {
            $userRolesById[$role->getId()] = $role;
        }

        $roles = [];
        foreach ($allRoles as $role) {
            $roles[] = [
                'hasRole' => isset($userRolesById[$role->getId()]),
                'role' => $role
            ];
        }

        $view = new AssignRolesView($user, $roles);

        $this->content->addHtmlElement($view);
        return new HtmlResponse($this->template);
    }

    /**
     *
     * @URL("{this->baseUrl}/{userId}/roles")
     * @Post()
     *
     * @param string $userId
     * @param array $roles
     */
    public function editRoles(string $userId, array $roles = array())
    {
        $user = $this->userDao->getUserById($userId);
        if ($user === null) {
            throw new PageNotFoundException(sprintf('Could not find user with ID %s',$userId));
        }

        $roleArr = [];

        foreach ($roles as $roleId) {
            $roleArr[] = $this->roleDao->getRoleById($roleId);
        }

        $this->roleDao->setRoles($user, $roleArr);

        return new RedirectResponse('../../'.ltrim($this->backUrl,'/'));
    }
}
