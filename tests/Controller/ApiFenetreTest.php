<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ApiFenetreTest extends WebTestCase 
{
    public function test_GetFenetre_without_authentication(){
        $client = static::createClient();
        $client->request('GET', '/api/fenetre/');
        $this->assertEquals('{"eror":"X-Auth-Token est requis"}', $client->getResponse()->getContent(), 'Echec du get le X-Auth-Token est requis');        
    }
}
