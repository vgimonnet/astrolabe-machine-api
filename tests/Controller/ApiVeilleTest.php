<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ApiVeilleTest extends WebTestCase 
{
    public function test_Get_Temps_Veille(){
        $client = static::createClient();
        $client->request('GET', '/api/veille/');
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), 'Route injoignable');        
    }

    public function test_Post_Fenetre(){
        $client = static::createClient();
        $client->request('POST', '/api/veille/');
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), 'Route injoignable');        
    }
}
