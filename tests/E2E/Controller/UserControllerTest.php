<?php

namespace Belga\Tests\E2E\Controller;

use Belga\Entity\User;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Client;

class UserControllerTest extends WebTestCase
{
    /**
     * helper to access EntityManager
     * @var EntityManager
     */
    protected $em;
    /**
     * Helper to access test Client
     * @var Client
     */
    protected $client;

    const ENDPOINT_URL = '/v1/user';

    const API_KEY_TEST = '2s5cmQoEwKwpnwhcnab5dOxOWozPyTqj';

    const USER_EMAIL = 'testuser@email.cm';
    const USER_GIVEN_NAME = 'givenName';
    const USER_FAMILY_NAME = 'familyName';

    public function setUp()
    {
        $this->client = static::createClient();
        $this->client->disableReboot();
        $this->em = $this->client->getContainer()->get('doctrine.orm.entity_manager');
        $this->em->beginTransaction();
        $this->em->getConnection()->setAutoCommit(false);
        $this->em->getRepository(User::class)->removeAll();
        $this->em->commit();
    }

    public function tearDown()
    {
        $this->em->rollback();
    }

    public function testSearchUserByIdWithWrongApiKeyReturnsError()
    {
        $uri = self::ENDPOINT_URL . '/1';

        $headers = ['HTTP_X-API-KEY' => self::API_KEY_TEST . 'wrong'];

        $this->client->request(Request::METHOD_GET, $uri, [], [], $headers);

        $response = $this->client->getResponse();

        $this->assertEquals(401, $response->getStatusCode());

        $this->assertEquals('{"code":2,"message":"Incorrect API key","errors":[]}', $response->getContent());
    }

    public function testSearchUserByIdWithWrongApiKeyReturnsNotFoundUser()
    {
        $uri = self::ENDPOINT_URL . '/1090909090900099';

        $headers = ['HTTP_X-API-KEY' => self::API_KEY_TEST];

        $this->client->request(Request::METHOD_GET, $uri, [], [], $headers);

        $response = $this->client->getResponse();

        $this->assertEquals(404, $response->getStatusCode());

        $this->assertEquals('{"code":1,"message":"User with id 1090909090900099 not found","errors":[]}', $response->getContent());
    }

    public function testCreateNewUserSuccess()
    {
        $data = [
            'email' => self::USER_EMAIL,
            'givenName' => self::USER_GIVEN_NAME,
            'familyName' => self::USER_FAMILY_NAME,
        ];

        $uri = self::ENDPOINT_URL;

        $headers = ['HTTP_X-API-KEY' => self::API_KEY_TEST];

        $this->client->request(Request::METHOD_POST, $uri, [], [], $headers, json_encode($data));

        $response = $this->client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());

        $receivedData = json_decode($response->getContent(), true);

        $this->assertArrayHasKey('id', $receivedData);
        $this->assertArrayHasKey('email', $receivedData);
        $this->assertArrayHasKey('givenName', $receivedData);
        $this->assertArrayHasKey('familyName', $receivedData);
        $this->assertArrayHasKey('createdAt', $receivedData);
        $this->assertEquals(self::USER_EMAIL, $receivedData['email']);
        $this->assertEquals(self::USER_GIVEN_NAME, $receivedData['givenName']);
        $this->assertEquals(self::USER_FAMILY_NAME, $receivedData['familyName']);
    }

    public function testCreateNewUserWithEmptyDataReturnsErrors()
    {
        $data = [];

        $uri = self::ENDPOINT_URL;

        $headers = ['HTTP_X-API-KEY' => self::API_KEY_TEST];

        $this->client->request(Request::METHOD_POST, $uri, [], [], $headers, json_encode($data));

        $response = $this->client->getResponse();

        $this->assertEquals(400, $response->getStatusCode());

        $this->assertEquals('{"code":6,"message":"Validation failed.","errors":[{"field":"email","message":"Email should not be blank."},{"field":"givenName","message":"Given name should not be blank."},{"field":"familyName","message":"Family name should not be blank."}]}', $response->getContent());
    }

    public function testCreateNewUserWithWrongDataReturnsValidationErrors()
    {
        $data = [
            'email' => 'not_validEmail',
            'givenName' => 'very_long_givenName_123123123123123',
            'familyName' => 'very_long_familyName_123123123123123',
        ];

        $uri = self::ENDPOINT_URL;

        $headers = ['HTTP_X-API-KEY' => self::API_KEY_TEST];

        $this->client->request(Request::METHOD_POST, $uri, [], [], $headers, json_encode($data));

        $response = $this->client->getResponse();

        $this->assertEquals(400, $response->getStatusCode());

        $this->assertEquals('{"code":6,"message":"Validation failed.","errors":[{"field":"email","message":"The email \'\"not_validEmail\"\' is not a valid email."},{"field":"givenName","message":"Given name cannot be longer than 30 characters"},{"field":"familyName","message":"Family name cannot be longer than 30 characters"}]}', $response->getContent());

    }

    public function testUpdateUserWithWrongDataReturnsValidationErrors()
    {
        $newUser = $this->createUserObject();

        $data = [
            'email' => 'not_validEmail',
            'givenName' => 'very_long_givenName_123123123123123',
            'familyName' => 'very_long_familyName_123123123123123',
        ];

        $uri = self::ENDPOINT_URL . '/' . $newUser['id'];

        $headers = ['HTTP_X-API-KEY' => self::API_KEY_TEST];

        $this->client->request(Request::METHOD_PUT, $uri, [], [], $headers, json_encode($data));

        $response = $this->client->getResponse();

        $this->assertEquals(400, $response->getStatusCode());

        $this->assertEquals('{"code":6,"message":"Validation failed.","errors":[{"field":"email","message":"The email \'\"not_validEmail\"\' is not a valid email."},{"field":"givenName","message":"Given name cannot be longer than 30 characters"},{"field":"familyName","message":"Family name cannot be longer than 30 characters"}]}', $response->getContent());

    }

    public function testUpdateUserWithValidDataReturnsSuccess()
    {
        $newUser = $this->createUserObject();

        $data = [
            'email' => 'myEmail@gmail.com',
            'givenName' => 'myNewGivenName',
            'familyName' => 'myNewFamilyName',
        ];

        $uri = self::ENDPOINT_URL . '/' . $newUser['id'];

        $headers = ['HTTP_X-API-KEY' => self::API_KEY_TEST];

        $this->client->request(Request::METHOD_PUT, $uri, [], [], $headers, json_encode($data));

        $response = $this->client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());

        $receivedData = json_decode($response->getContent(), true);

        $this->assertArrayHasKey('id', $receivedData);
        $this->assertArrayHasKey('email', $receivedData);
        $this->assertArrayHasKey('givenName', $receivedData);
        $this->assertArrayHasKey('familyName', $receivedData);
        $this->assertArrayHasKey('createdAt', $receivedData);
        $this->assertEquals('myEmail@gmail.com', $receivedData['email']);
        $this->assertEquals('myNewGivenName', $receivedData['givenName']);
        $this->assertEquals('myNewFamilyName', $receivedData['familyName']);

    }


    private function createUserObject()
    {
        $data = [
            'email' => self::USER_EMAIL,
            'givenName' => self::USER_GIVEN_NAME,
            'familyName' => self::USER_FAMILY_NAME,
        ];

        $client = static::createClient();

        $uri = self::ENDPOINT_URL;

        $headers = ['HTTP_X-API-KEY' => self::API_KEY_TEST];

        $client->request(Request::METHOD_POST, $uri, [], [], $headers, json_encode($data));

        $response = $client->getResponse();

        return json_decode($response->getContent(), true);
    }

}