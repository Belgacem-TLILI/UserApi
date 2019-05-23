<?php

namespace Belga\Controller;

use Belga\Service\UserService;
use Belga\Serializer\ObjectSerializer;
use Belga\Validator\ValueValidator;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/v1/user")
 */
class UserController
{
    /**
     * @var UserService
     */
    private $userService;

    /**
     * Symfony Serializer component: used for serializing and deserializing to and from objects and different formats
     * (e.g. JSON or XML)
     *
     * @var ObjectSerializer
     */
    private $serializer;

    /**
     * @param User
     * @param ObjectSerializer $serializer
     */
    public function __construct(UserService $userService, ObjectSerializer $serializer)
    {
        $this->userService = $userService;
        $this->serializer = $serializer;
    }


    /**
     * Search a User by Id
     *
     * @Route("/{userId}", methods={"GET"})
     *
     * @return JsonResponse
     */
    public function searchUserById($userId)
    {
        ValueValidator::validateId($userId);

        $user = $this->userService->searchUserById($userId);

        $jsonContent = $this->serializer->serialize($user);

        return new JsonResponse($jsonContent, JsonResponse::HTTP_OK,[], true);
    }

    /**
     * Create a new User
     *
     * @Route(methods={"POST"})
     *
     * @return JsonResponse
     */
    public function createNewUser(Request $request)
    {
        $data = json_decode($request->getContent(), true);

        $user = $this->userService->createUser($data);

        $jsonContent = $this->serializer->serialize($user);

        return new JsonResponse($jsonContent, JsonResponse::HTTP_OK,[], true);
    }

    /**
     * Update a User by Id
     *
     * @Route("/{userId}", methods={"PUT"})
     *
     * @return JsonResponse
     */
    public function updateUser(Request $request, $userId)
    {
        ValueValidator::validateId($userId);

        $data = json_decode($request->getContent(), true);

        $user = $this->userService->updateUser($userId, $data);

        $jsonContent = $this->serializer->serialize($user);

        return new JsonResponse($jsonContent, JsonResponse::HTTP_OK,[], true);
    }

    /**
     * Delete a User by Id
     *
     * @Route("/{userId}", methods={"DELETE"})
     *
     * @return JsonResponse
     */
    public function delete($userId, UserService $userService)
    {
        ValueValidator::validateId($userId);

        $userService->deleteUser($userId);

        return new JsonResponse();
    }

}