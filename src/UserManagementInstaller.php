<?php
declare(strict_types=1);

namespace Mouf\Security\UserManagement;

use Mouf\Actions\InstallUtils;
use Mouf\Html\Renderer\RendererUtils;
use Mouf\Installer\PackageInstallerInterface;
use Mouf\MoufManager;

class UserManagementInstaller implements PackageInstallerInterface
{
    /**
     * (non-PHPdoc).
     *
     * @see \Mouf\Installer\PackageInstallerInterface::install()
     */
    public static function install(MoufManager $moufManager)
    {
        // Let's create the renderer
        RendererUtils::createPackageRenderer($moufManager, 'mouf/security.user-management-ui');

        // TODO!

        // Let's rewrite the MoufComponents.php file to save the component
        $moufManager->rewriteMouf();
    }
}
