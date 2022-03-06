<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Description of SendCodeControllerTest
 *
 * @author Rossana Pencheva <rossana.ruseva@gmail.com>
 */
class SendSmsControllerTest extends WebTestCase
{
    public function testSendCodePOST()
    {
        $data = array(
            "phone" => "35987385347",
        );
        $client = static::createClient();
        $client->request(
            'POST',
            '/api/send/code',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($data)
        );
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testSendCodeMissingPOST()
    {
        $data = array();
        $client = static::createClient();
        $client->request(
            'POST',
            '/api/send/code',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($data)
        );
        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }
}
