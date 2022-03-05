<?php

namespace App\Tests;

use App\Entity\Codes;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Description of VerifyPhoneControllerTest
 *
 * @author Rossana Pencheva <rossana.ruseva@gmail.com>
 */
class VerifyPhoneControllerTest extends WebTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $entityManager;

    private $code = 783456;

    private $phone = 3596862648;

    protected function setUp(): void
    {
        static::$kernel = static::createKernel();
        static::$kernel->boot();

        $this->entityManager = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();
        $code = new Codes($this->phone, $this->code);
        $this->entityManager->persist($code);
        $this->entityManager->flush();
    }

    public function testVerifyCodePOST()
    {
        //  $code = "3323138";
        //  $phone = "359173853238";

        $data = array(
            "code" => $this->code,
            "phone" => $this->phone,
        );
        $client = static::createClient();
        $client->request(
            'POST',
            '/api/verify/phone',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($data)
        );
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

    }

    public function testVerifyCodeMissingPOST()
    {
        $data = array();
        $client = static::createClient();
        $client->request(
            'POST',
            '/api/verify/phone',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($data)
        );
        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        // doing this is recommended to avoid memory leaks
        $this->entityManager->close();
        $this->entityManager = null;
    }
}
