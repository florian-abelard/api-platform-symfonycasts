<?php

namespace App\Tests\Functional;

use App\Entity\User;
use App\Test\CustomApiTestCase;
use Hautelook\AliceBundle\PhpUnit\ReloadDatabaseTrait;

class UserResourceTest extends CustomApiTestCase
{
    use ReloadDatabaseTrait;

    public function testCreateUser()
    {
        $client = self::createClient();

        $client->request('POST', '/api/users', [
            'json' => [
                'email' => 'cheeseplease@example.com',
                'username' => 'cheeseplease',
                'password' => 'brie',
            ],
        ]);
        $this->assertResponseStatusCodeSame(201);

        $this->logIn($client, 'cheeseplease@example.com', 'brie');
    }

    public function testUpdateUser()
    {
        $client = self::createClient();

        $user = $this->createUser('cheeseislife@example.com', 'cantal');

        $this->logIn($client, 'cheeseislife@example.com', 'cantal');

        $client->request('PUT', '/api/users/' . $user->getId(), [
            'json' => [
                'username' => 'cheezeislife',
            ],
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            'username' => 'cheezeislife',
        ]);
    }

    public function testGetUser()
    {
        $client = self::createClient();

        $user = $this->createUser('cheeseisbeautiful@example.com', 'cantal');
        $this->logIn($client, 'cheeseisbeautiful@example.com', 'cantal');

        $user->setPhoneNumber('0240403030');
        $em = $this->getEntityManager();
        $em->flush();

        $client->request('GET', '/api/users/' . $user->getId());

        $this->assertJsonContains([
            'email' => 'cheeseisbeautiful@example.com',
        ]);

        $data = $client->getResponse()->toArray();

        $this->assertArrayNotHasKey('phoneNumber', $data);

        $user = $em->getRepository(User::class)->find($user->getId());
        $user->setRoles(['ROLE_ADMIN']);
        $em->flush();

        $this->logIn($client, 'cheeseisbeautiful@example.com', 'cantal');

        $client->request('GET', '/api/users/' . $user->getId());

        $this->assertJsonContains([
            'phoneNumber' => '0240403030',
        ]);
    }
}
