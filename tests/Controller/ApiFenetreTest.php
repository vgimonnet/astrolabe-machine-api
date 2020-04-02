<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ApiFenetreTest extends WebTestCase 
{
    public function test_Get_Fenetre(){
        $client = static::createClient();
        $client->request('GET', '/api/fenetre/');
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), 'Route injoignable');        
    }

    public function test_Get_Fenetre_Veille(){
        $client = static::createClient();
        $client->request('GET', '/api/fenetre/veille');
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), 'Route injoignable');        
    }

    public function test_Get_Fenetre_Veille_By_Id(){
        $client = static::createClient();
        $client->request('GET', '/api/fenetre/1');
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), 'Route injoignable');        
    }

    public function test_Post_Fenetre(){
        $client = static::createClient();
        $client->request('POST', '/api/fenetre/');
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), 'Route injoignable');        
    }

    public function test_Put_Fenetre(){
        $client = static::createClient();
        $client->request('PUT', '/api/fenetre/0');
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), 'Route injoignable');        
    }

    public function test_Delete_Fenetre(){
        $client = static::createClient();
        $client->request('DELETE', '/api/fenetre/0');
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), 'Route injoignable');        
    }
}
