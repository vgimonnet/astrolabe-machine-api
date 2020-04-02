<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ApiUserTest extends WebTestCase 
{
    public function test_Login(){
        $client = static::createClient();
        $client->request('POST', '/api/user/login');
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), 'Route injoignable');        
    }

    // public function test_Create_User(){
    //     $client = static::createClient();
    //     $client->request('POST', '/api/user/create');
    //     $this->assertEquals(200, $client->getResponse()->getStatusCode(), 'Route injoignable');        
    // }

    public function test_Change_Password(){
        $client = static::createClient();
        $client->request('POST', '/api/user/changepassword');
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), 'Route injoignable');        
    }
}
