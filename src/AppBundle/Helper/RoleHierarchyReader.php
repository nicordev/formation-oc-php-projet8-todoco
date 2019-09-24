<?php

namespace AppBundle\Helper;


use Symfony\Component\Security\Core\Role\RoleHierarchyInterface;

class RoleHierarchyReader
{
    public static function fetchRoleList(RoleHierarchyInterface $roleHierarchy)
    {
        $rolesMap = ObjectAccessor::getPrivateProperty($roleHierarchy, "map");
        $adminRole = array_key_first($rolesMap);
        $userRole = $rolesMap[$adminRole][0];

        return [
            "Utilisateur" => $userRole,
            "Administrateur" => $adminRole
        ];
    }
}