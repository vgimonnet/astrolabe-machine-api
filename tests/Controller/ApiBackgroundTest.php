<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ApiBackgroundTest extends WebTestCase 
{
    public function test_Get_Background(){
        $client = static::createClient();
        $client->request('GET', '/api/background/');
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), 'Route injoignable');        
    }

    public function test_Get_Background_Veille(){
        $client = static::createClient();
        $client->request('GET', '/api/background/veille/');
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), 'Route injoignable');        
    }

    public function test_Post_Background(){
        $client = static::createClient();
        $client->request('POST', '/api/background/');
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), 'Route injoignable');        
    }
}
