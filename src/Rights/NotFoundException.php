<?php

namespace Mouf\Security\UserManagement\Rights;


class NotFoundException extends \Exception
{
    public static function create(string $name): self
    {
        return new self("Right not found: $name");
    }
}