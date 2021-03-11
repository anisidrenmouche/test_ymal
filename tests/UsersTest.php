<?php

namespace App\Tests;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\User;

class UsersTest extends ApiTestCase
{
    public function testCreateUser(): void
    {
        $response = static::createClient()->request('POST', 'api/users', ['json' => [
            'nom' => 'rif',
            'prenom' => 'rif',
            'age' => 17,
        ]]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertJsonContains([
            '@context' => '/api/contexts/User',
            '@type' => 'User',
            'nom' => 'rif',
            'prenom' => 'rif',
            'age' => 17
        ]);
    }
    public function testDeleteUser(): void
    {
        $client = static::createClient();
        $iri = $this->findIriBy(User::class, ['nom' => 'rif']);

        $client->request('DELETE', $iri);

        $this->assertResponseStatusCodeSame(204);
        $this->assertNull(
            // Through the container, you can access all your services from the tests, including the ORM, the mailer, remote API clients...
            static::$container->get('doctrine')->getRepository(User::class)->findOneBy(['nom' => 'rif'])
        );
    }   
}