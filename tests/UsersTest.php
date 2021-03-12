<?php

namespace App\Tests;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\User;
use Symfony\Component\Yaml\Yaml;

class UsersTest extends ApiTestCase
{

    // cette function me permet de creer une function 
    public function testCreateUser(): void
    {
        // ajouter un utilisateur POST sur l'api user, sans passer par le fichier 
        $response = static::createClient()->request('POST', 'api/users', ['json' => [
            'nom' => 'rif',
            'prenom' => 'rif',
            'age' => 17,
        ]]);
// ici je demande si elle existe / bravo elle etait creer 
        $this->assertResponseStatusCodeSame(201);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        // je verifie que la reponse json, correspond bien a ce que j'ai ecrit 
        $this->assertJsonContains([
            '@context' => '/api/contexts/User',
            '@type' => 'User',
            'nom' => 'rif',
            'prenom' => 'rif',
            'age' => 17
        ]);
    }
    
    // pour observer les valeurs des variables
    /*public function testYamlUser(): void
    {
        $value = Yaml::parseFile('tests/users_tests.yaml');
        
    }
    public function testYamlUsers(): void
    {
        $value = Yaml::parseFile('tests/users_tests.yaml');
        if ($value ["array_tests"]["success_ok"] == 200);
        dump($value);
    }*/

    public function testCreateYamlUser(): void
    {
        $valuename = Yaml::parseFile('tests/users_tests.yaml');
        $response = static::createClient()->request('POST', 'api/users', ['json' => [
            'nom' => $valuename ["array_tests"]["nom"],
            'prenom' => $valuename ["array_tests"]["prenom"],
            'age' => $valuename ["array_tests"]["age"],
        ]]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertJsonContains([
            '@context' => '/api/contexts/User',
            '@type' => 'User',
            'nom' => $valuename ["array_tests"]["nom"],
            'prenom' => $valuename ["array_tests"]["prenom"],
            'age' => $valuename ["array_tests"]["age"]
        ]);
    }
    // cette function c'est pour supprimer le USer
    public function testDeleteUser(): void
    {
        $valuename = Yaml::parseFile('tests/users_tests.yaml');
        // le http client 
        $client = static::createClient();
        // je creer ma varible iri je recuprer l'object nom
        $iri = $this->findIriBy(User::class, ['nom' => $valuename ["array_tests"]["nom"]]);
        // request delete ici
        $client->request('DELETE', $iri);
        // ici j'attend une reponse
        $this->assertResponseStatusCodeSame(204);
        $this->assertNull(
            /// A travers le container, vous pouvez accéder à tous vos services depuis les tests, y compris l'ORM, le mailer, les clients API distants...
            static::$container->get('doctrine')->getRepository(User::class)->findOneBy(['nom' => $valuename ["array_tests"]["nom"]])
        );
    }

    public function testGetUser(): void
    {
        // go to fichier yml
        $tests = Yaml::parseFile('tests/users_tests_plus.yaml');
        // http client
        $client = static::createClient();

        // boucle pour aler chercher les infos de tests
        foreach ($tests as $test) {
            // recuperer les infos
            // 1er parametre la methode, le secondc'est l'URL, et le 3eme parametre c'est les données
            $client->request($test['method'], $test['url'], ['json' => $test['data']]);
            // attente d'une reponse...
            $this->assertResponseIsSuccessful();
            // attente de la bonne reponse pour confirmation du POST ou GET
            $this->assertResponseStatusCodeSame($test['statut']);
            // $this->assertJsonContains($test['jsonResponse']);
        }
    }
}