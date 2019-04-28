<?php

namespace Belga\Tests\Unit\Mapper;

use Belga\Entity\User;
use Belga\Mapper\UserMapper;
use PHPUnit\Framework\TestCase;

class UserMapperTest extends TestCase
{
    public function testMapUserObject()
    {
        $user = new User();
        $data = [
            'email' => 'myEmail@gmail.com',
            'givenName' => 'myNewGivenName',
            'familyName' => 'myNewFamilyName',
        ];

        $mapper = new UserMapper();

        $user = $mapper->mapUserObject($user, $data);

        $this->assertEquals("myEmail@gmail.com", $user->getEmail());
        $this->assertEquals("myNewGivenName", $user->getGivenName());
        $this->assertEquals("myNewFamilyName", $user->getFamilyName());
    }
}