<?php

namespace Belga\Tests\Unit\Service;

use Belga\Exception\EntityValidationException;
use Belga\Repository\UserRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityNotFoundException;
use Mockery;
use PHPUnit\Framework\TestCase;
use Belga\Mapper\UserMapper;
use Belga\Entity\User;
use Belga\Service\UserService;
use Symfony\Bridge\Monolog\Logger;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\TraceableValidator;

class UserServiceTest extends TestCase
{
    protected $entityManager;
    protected $validator;
    protected $userMapper;
    protected $logger;
    protected $repository;

    public function setUp()
    {
        parent::setUp();

        $this->entityManager = Mockery::mock(EntityManager::class);
        $this->validator = Mockery::mock(TraceableValidator::class);
        $this->userMapper = Mockery::mock(UserMapper::class);
        $this->logger = Mockery::mock(Logger::class);
        $this->repository = Mockery::mock(UserRepository::class);
    }

    public function tearDown()
    {
        Mockery::close();
    }

    public function testSearchUserByIdThrowsEntityNotFoundException()
    {
        $this->expectException(EntityNotFoundException::class);
        $this->expectExceptionMessage('User with id 1 not found');

        $this->repository->shouldReceive('find')->once()->with(1)->andReturn(null);

        $this->entityManager->shouldReceive('getRepository')->once()->with(User::class)->andReturn($this->repository);

        $service = new UserService($this->entityManager, $this->userMapper, $this->validator);

        $service->searchUserById(1);
    }

    public function testSearchUserByIdReturnObject()
    {
        $user = new User();

        $this->repository->shouldReceive('find')->once()->with(122)->andReturn($user);

        $this->entityManager->shouldReceive('getRepository')->once()->with(User::class)->andReturn($this->repository);

        $service = new UserService($this->entityManager, $this->userMapper, $this->validator);

        $result = $service->searchUserById(122);

        $this->assertInstanceOf(User::class, $result);
    }

    public function testCreateUserThrowsEntityValidationException()
    {
        $this->expectException(EntityValidationException::class);

        $user = new User();
        $data = [];
        $errors = new ConstraintViolationList([
            new ConstraintViolation('test', 'test', ['test'], 'test', 'test', 'test'),
        ]);

        $this->userMapper->shouldReceive('mapUserObject')->once()->with(Mockery::type(User::class), $data)->andReturn($user);

        $this->validator->shouldReceive('validate')->once()->with($user)->andReturn($errors);

        $service = new UserService($this->entityManager, $this->userMapper, $this->validator);

        $service->createUser([]);
    }

    public function testCreateUserReturnsObject()
    {
        $user = new User();
        $data = [
            'email' => 'myEmail@gmail.com',
            'givenName' => 'myNewGivenName',
            'familyName' => 'myNewFamilyName',
        ];

        $this->userMapper->shouldReceive('mapUserObject')->once()->with(Mockery::type(User::class), $data)->andReturn($user);

        $this->validator->shouldReceive('validate')->once()->with($user)->andReturn([]);

        $this->entityManager->shouldReceive('persist')->once()->with($user)->andReturn(null);

        $this->entityManager->shouldReceive('flush')->once();


        $service = new UserService($this->entityManager, $this->userMapper, $this->validator);

        $service->createUser($data);
    }

    public function testUpdateUserThrowsEntityNotFoundException()
    {
        $this->expectException(EntityNotFoundException::class);
        $this->expectExceptionMessage('User with id 1 not found');

        $this->repository->shouldReceive('find')->once()->with(1)->andReturn(null);

        $this->entityManager->shouldReceive('getRepository')->once()->with(User::class)->andReturn($this->repository);

        $service = new UserService($this->entityManager, $this->userMapper, $this->validator);

        $service->updateUser(1, []);
    }

    public function testUpdateUserThrowsEntityValidationException()
    {
        $this->expectException(EntityValidationException::class);

        $user = new User();
        $data = ['email' => 'wrong_email'];
        $errors = new ConstraintViolationList([
            new ConstraintViolation('test', 'test', ['test'], 'test', 'test', 'test'),
        ]);

        $this->entityManager->shouldReceive('getRepository')->once()->with(User::class)->andReturn($this->repository);

        $this->repository->shouldReceive('find')->once()->with(122)->andReturn($user);

        $this->userMapper->shouldReceive('mapUserObject')->once()->with(Mockery::type(User::class), $data)->andReturn($user);

        $this->validator->shouldReceive('validate')->once()->with($user)->andReturn($errors);

        $service = new UserService($this->entityManager, $this->userMapper, $this->validator);

        $service->updateUser(122, $data);
    }

    public function testUpdateUserReturnsObject()
    {
        $user = new User();
        $data = [
            'email' => 'myEmail@gmail.com',
            'givenName' => 'myNewGivenName',
            'familyName' => 'myNewFamilyName',
        ];

        $this->entityManager->shouldReceive('getRepository')->once()->with(User::class)->andReturn($this->repository);

        $this->repository->shouldReceive('find')->once()->with(122)->andReturn($user);

        $this->userMapper->shouldReceive('mapUserObject')->once()->with(Mockery::type(User::class), $data)->andReturn($user);

        $this->validator->shouldReceive('validate')->once()->with($user)->andReturn([]);

        $this->entityManager->shouldReceive('flush')->once();

        $service = new UserService($this->entityManager, $this->userMapper, $this->validator);

        $service->updateUser(122, $data);
    }

    public function testDeleteUserWithExistingUserReturnsNothing()
    {
        $user = new User();

        $this->entityManager->shouldReceive('getRepository')->once()->with(User::class)->andReturn($this->repository);

        $this->repository->shouldReceive('find')->once()->with(122)->andReturn($user);

        $this->entityManager->shouldReceive('remove')->with($user)->once()->andReturn(null);

        $this->entityManager->shouldReceive('flush')->once();

        $service = new UserService($this->entityManager, $this->userMapper, $this->validator);

        $service->deleteUser(122);
    }

    public function testDeleteUserWithNonExistingUserReturnsNothing()
    {
        $this->entityManager->shouldReceive('getRepository')->once()->with(User::class)->andReturn($this->repository);

        $this->repository->shouldReceive('find')->once()->with(122)->andReturn(null);

        $service = new UserService($this->entityManager, $this->userMapper, $this->validator);

        $service->deleteUser(122);
    }
}
