<?php


namespace Mouf\Security\UserManagement;


use Mouf\Html\HtmlElement\HtmlBlock;
use Mouf\Html\Template\TemplateInterface;
use Mouf\Mvc\Splash\Annotations\Get;
use Mouf\Mvc\Splash\Annotations\Post;
use Mouf\Mvc\Splash\Annotations\URL;
use Mouf\Mvc\Splash\Exception\PageNotFoundException;
use Mouf\Mvc\Splash\HtmlResponse;
use Mouf\Security\UserManagement\Impl\Role;
use Mouf\Security\Right;
use Mouf\Security\UserManagement\Api\RightDao;
use Mouf\Security\UserManagement\Api\RoleDao;
use Mouf\Security\UserManagement\Api\RoleRightDao;
use Mouf\Security\UserManagement\Rights\HierarchicalRightsRegistry;
use Mouf\Security\UserManagement\Rights\RightCategory;
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
     * @URL("{$this->baseUrl}/new")
     * @URL("{$this->baseUrl}/{roleId}")
     * @Get()
     *
     * @param string $roleId
     *
     * @throws PageNotFoundException
     *
     * @return ResponseInterface
     */
    public function viewRole(string $roleId = null) : ResponseInterface
    {
        $roleRightsByName = [];
        if ($roleId !== null) {
            $role = $this->roleDao->getRoleById($roleId);
            if ($role === null) {
                throw new PageNotFoundException(sprintf('Could not find user with ID %s',$roleId));
            }
            $roleRights = $this->roleRightDao->getRightsForRole($role);


            foreach ($roleRights as $right) {
                $roleRightsByName[$right->getName()] = $right;
            }
        } else {
            $role = new Role(null, '');
        }

        if ($this->rightDao instanceof HierarchicalRightsRegistry) {
            $rightsCategories = $this->rightDao->getRightCategories();
        } else {
            $rightsCategories = [
                new RightCategory("Rights", "", $this->rightDao->getAllRights())
            ];
        }




        $categories = [];
        foreach ($rightsCategories as $category) {
            $categoryArr = [];
            $categoryArr['name'] = $category->getName();
            $categoryArr['description'] = $category->getDescription();
            $rights = [];
            foreach ($category->getRights() as $right) {
                $rights[] = [
                    'hasRight' => isset($roleRightsByName[$right->getName()]),
                    'right' => $right
                ];
            }
            $categoryArr['rights'] = $rights;
            $categories[] = $categoryArr;
        }

        $view = new EditRoleView($role, $categories, $this->backUrl);

        $this->content->addHtmlElement($view);
        return new HtmlResponse($this->template);
    }

    /**
     *
     * @URL("{$this->baseUrl}/new")
     * @URL("{$this->baseUrl}/{roleId}")
     * @Post()
     *
     * @param string $roleId
     * @param string[] $rights
     *
     * @return ResponseInterface
     * @throws PageNotFoundException
     */
    public function editRole(string $roleId = null, string $label, array $rights = array()) : ResponseInterface
    {
        if ($roleId != null) {
            $role = $this->roleDao->getRoleById($roleId);
            if ($role === null) {
                throw new PageNotFoundException(sprintf('Could not find role with ID %s', $roleId));
            }
            if ($label !== $role->getLabel()) {
                $this->roleDao->renameRole($roleId, $label);
            }
        } else {
            $role = $this->roleDao->createRole($label);
        }


        $rightArr = [];

        foreach ($rights as $rightName) {
            $rightArr[] = $this->rightDao->get($rightName);
        }

        $this->roleRightDao->setRightsForRole($role, $rightArr);

        return new RedirectResponse('../../'.ltrim($this->backUrl,'/'));
    }

    /**
     *
     * @URL("{$this->baseUrl}/{roleId}/delete")
     *
     * @param string $roleId
     * @return ResponseInterface
     */
    public function deleteRole(string $roleId) : ResponseInterface
    {
        $role = $this->roleDao->getRoleById($roleId);
        if ($role === null) {
            throw new PageNotFoundException(sprintf('Could not find role with ID %s', $roleId));
        }
        $this->roleDao->deleteRole($roleId);

        return new RedirectResponse('../../'.ltrim($this->backUrl,'/'));
    }
}
