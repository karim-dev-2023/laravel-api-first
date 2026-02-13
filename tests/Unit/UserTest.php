<?php
use App\Models\User;
use Tests\TestCase;

class UserTest extends TestCase
{

    public function testTrue()
    {

        $user = new User();
        $user->email = 'john@entreprise.com';

        $result = $user->usesProfessionalEmail();

        $this->assertTrue($result);
    }

    public function testFalse()
    {

        $user = new User();
        $user->email = 'john@gmail.com';

        $result = $user->usesProfessionalEmail();

        $this->assertFalse($result);
    }



}