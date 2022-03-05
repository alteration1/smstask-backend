<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Description of RegisterControllerTest
 *
 * @author Rossana Pencheva <rossana.ruseva@gmail.com>
 */
class RegisterControllerTest extends WebTestCase
{

    public function testRegisterUserPOST()
    {
        $data = array(
            "email" => str_shuffle('abcdef') . "@test.com",
            "phone" => "35987385347",
            "password" => "123456",
        );
        $client = static::createClient();
        $client->request(
            'POST',
            '/api/register/user',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($data)
        );
        $this->assertEquals(201, $client->getResponse()->getStatusCode());
    }

    public function testRegisterUserMissingPOST()
    {
        $data = array(
            "phone" => "35987385347",
            "password" => "123456",
        );
        $client = static::createClient();
        $client->request(
            'POST',
            '/api/register/user',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($data)
        );
        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }
}
