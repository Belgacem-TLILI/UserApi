<?php

namespace Belga\Service;

use Belga\Error\ErrorCode;
use Belga\Entity\User;
use Belga\Exception\EntityValidationException;
use Belga\Mapper\UserMapper;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityNotFoundException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserService
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var UserMapper
     */
    private $userMapper;

    /**
     * Symfony Validator component
     *
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager, UserMapper $userMapper, ValidatorInterface $validator)
    {
        $this->entityManager = $entityManager;
        $this->userMapper = $userMapper;
        $this->validator = $validator;
    }


    /**
     * @param int $userId
     *
     * @return User
     *
     * @throws EntityNotFoundException
     */
    public function searchUserById($userId) : User
    {
        $user = $this->entityManager->getRepository(User::class)->find($userId);

        if (null === $user) {
            throw new EntityNotFoundException(sprintf('User with id %s not found', $userId), ErrorCode::USER_NOT_FOUND);
        }

        return $user;
    }

    /**
     * Method to create and validate a new User by the given array of data
     *
     * @param array $data
     *
     * @return User
     *
     */
    public function createUser(array $data) : User
    {
        $user = new User();
        $user = $this->userMapper->mapUserObject($user, $data);

        $this->validateUser($user);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }

    /**
     * Update User Object by the given data array
     *
     * @param int $userId
     * @param array $data new values for the User Object
     *
     * @return User
     *
     * @throws EntityNotFoundException
     * @throws \Symfony\Component\Validator\Exception\ValidatorException
     */
    public function updateUser($userId, array $data)
    {
        $user = $this->entityManager->getRepository(User::class)->find($userId);

        if (!$user) {
            throw new EntityNotFoundException(sprintf('User with id %s not found', $userId), ErrorCode::USER_NOT_FOUND);
        }

        // we will update, only if we receive some data for the update
        if (count($data)) {
            $user = $this->userMapper->mapUserObject($user, $data);

            $this->validateUser($user);

            $this->entityManager->flush();
        }

        return $user;
    }

    /**
     * @param int $userId
     *
     * @return void
     *
     */
    public function deleteUser($userId) : void
    {
        $user = $this->entityManager->getRepository(User::class)->find($userId);

        if ($user) {
            $this->entityManager->remove($user);
            $this->entityManager->flush();
        }
    }

    /**
     * Validate User entity, this will trigger validation based on assertion annotations in User model
     *
     * @param User $user
     *
     * @return void
     *
     * @throws \Symfony\Component\Validator\Exception\ValidatorException
     */
    private function validateUser(User $user)
    {
        $errors = $this->validator->validate($user);

        if (count($errors)) {
            $validationException = new EntityValidationException(
                EntityValidationException::ERROR_MSG,
                ErrorCode::VALIDATION_OF_DATA_ERROR
            );

            $validationException->setErrorList($errors);

            throw $validationException;
        }
    }
}