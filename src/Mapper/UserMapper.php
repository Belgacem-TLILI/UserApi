<?php

namespace Belga\Mapper;

use Belga\Entity\User;

class UserMapper
{
    /**
     * This function will bind the given data array to the given User Object
     *
     * @param User $user
     * @param array $data
     *
     * @return User
     */
    public function mapUserObject(User $user, array $data) : User
    {
        if (isset($data['email'])) {
            $user->setEmail($data['email']);
        }

        if (isset($data['givenName'])) {
            $user->setGivenName($data['givenName']);
        }

        if (isset($data['familyName'])) {
            $user->setFamilyName($data['familyName']);
        }

        return $user;
    }
}