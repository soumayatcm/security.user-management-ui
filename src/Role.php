<?php

namespace Mouf\Security\UserManagement;

use Mouf\Security\UserManagement\Api\RoleInterface;

class Role implements RoleInterface {

  private $id;
  private $label;

  public function __construct($id = null, string $label = "") {
    $this->id = $id;
    $this->label = $label;
  }

    /**
     * Returns the ID of a role.
     *
     * @return mixed
     */
    public function getId() {
      return $this->id;
    }

    /**
     * The label associated to a role.
     *
     * @return string
     */
    public function getLabel() : string {
      return $this->label;
    }
}
