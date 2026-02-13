<?php
use App\Models\User;

test('Vérification mail pro renvoie vrai', function () {
    
    $user = new User();
    $user->email = 'john@entreprise.com';
 
    $result = $user->usesProfessionalEmail();
 
    $this->assertTrue($result); 
});

test('Vérification mail pro renvoie faux', function () {
    
    $user = new User();
    $user->email = 'john@gmail.com';
 
    $result = $user->usesProfessionalEmail();
 
    $this->assertFalse($result); 
});



