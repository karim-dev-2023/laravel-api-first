<?php
use App\Models\User;
use Tests\TestCase;

class UserTest extends TestCase
{

    public function test_true()
    {

        $user = new User();
        $user->email = 'john@entreprise.com';

        $result = $user->usesProfessionalEmail();

        $this->assertTrue($result);
    }

    public function test_false()
    {

        $user = new User();
        $user->email = 'john@gmail.com';

        $result = $user->usesProfessionalEmail();

        $this->assertFalse($result);
    }
 
}