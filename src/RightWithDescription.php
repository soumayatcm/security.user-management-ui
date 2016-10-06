<?php
declare(strict_types=1);

namespace Mouf\Security\UserManagement;


use Mouf\Security\RightsService\MoufRight;
use Mouf\Security\UserManagement\Api\DisplayableRight;

class RightWithDescription extends MoufRight implements DisplayableRight
{
    /**
     * The description for that right.
     *
     * @var string
     */
    private $description;

    /**
     * @Important
     * @param string $name
     * @param string|null $description
     */
    public function __construct(string $name, string $description = null) {
        parent::__construct($name);
        $this->description = $description;
    }

    /**
     * Returns the description of that right.
     *
     * @return string
     */
    public function getDescription() : string
    {
        return $this->description;
    }
}
