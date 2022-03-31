<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AddCommentTest extends TestCase
{
    public function testAddFirstLvlComment() {
        $data = [
            'name' => 'John Doe',
            'body' => 'This is a first level test comment.',
            'comment_level' => 1,
            'reply_id' => 0
        ];

        $this->json('POST', 'v1/comments', $data, ['Content-Type' => 'application/json', 'Accept' => 'application/json'])
        ->assertStatus(200);
    }

    public function testAddSecondLvlComment() {
        $data = [
            'name' => 'John Doe',
            'body' => 'This is a second level test comment.',
            'comment_level' => 2,
            'reply_id' => 1
        ];

        $this->json('POST', 'v1/comments', $data, ['Content-Type' => 'application/json', 'Accept' => 'application/json'])
        ->assertStatus(200);
    }

    public function testAddThirdLvlComment() {
        $data = [
            'name' => 'John Doe',
            'body' => 'This is a third level test comment.',
            'comment_level' => 3,
            'reply_id' => 2
        ];

        $this->json('POST', 'v1/comments', $data, ['Content-Type' => 'application/json', 'Accept' => 'application/json'])
        ->assertStatus(200);
    }
}
