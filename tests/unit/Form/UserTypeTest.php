<?php

namespace App\Tests\unit\Form;

use App\Form\UserType;
use PHPUnit\Framework\TestCase;

class UserTypeTest extends TestCase
{
    public function testConstruct()
    {
        $securityRoleHierarchyRoles = [
            "ROLE_USER" => null,
            "ROLE_ADMIN" => ["ROLE_USER"],
            "ROLE_UNKNOWN" => null
        ];
        $this->expectException(\InvalidArgumentException::class);
        new UserType($securityRoleHierarchyRoles);
    }
}
