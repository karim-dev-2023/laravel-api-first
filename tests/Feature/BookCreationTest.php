<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookCreationTest extends TestCase
{
    use RefreshDatabase;

    public function test_book_is_created_return_201()
    {

        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson(
            '/api/v2/books',
            [
                'title' => 'Test Book',
                'author' => 'John Doe',
                'summary' => 'Hello World',
                'isbn' => '0123456789',
            ]
        );

        $response->assertStatus(201);

        $this->assertDatabaseHas('books', [
            'title' => 'Test Book',
            'author' => 'John Doe',
            'isbn' => '0123456789',
        ]);
    }
    public function test_book_is_not_created_and_returns_422()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/api/v2/books', [
            'title' => 'Bo',
            'author' => 'John Doe',
            'summary' => 'Hello World',
            'isbn' => '0123456789',
        ]);

        $response->assertStatus(422);


        $this->assertDatabaseMissing('books', [
            'title' => 'Bo',
        ]);
    }

    public function test_book_is_not_created_user_is_not_authenticated_and_returns_401()
    { 

       
        $response = $this->postJson('/api/v2/books', [
            'title' => 'Test Book',
            'author' => 'John Doe',
            'summary' => 'Hello World',
            'isbn' => '0123456789',
        ]);
       
        $response->assertStatus(401);

        
        $this->assertDatabaseMissing('books', [
            'title' => 'Test Book',
            'isbn' => '0123456789',
        ]);
    }
}
