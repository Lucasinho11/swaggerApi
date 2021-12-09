<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class TasksTest extends TestCase
{

    public function createUser()
    {
        $userData = [
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'password' => Hash::make($this->faker->password(8)),
        ];

        return $user = User::create($userData);
    }

    public function test_show_tasks_unauthorized()
    {
        $response = $this->postJson('/api/tasks');

        $response->assertStatus(405);
    }

    
    public function test_create_task_success()
    {
        $user = $this->createUser();
        $token = $user->createToken('ios')->plainTextToken;

        $postForm = [
            'body' => $this->faker->text,
            'user_id'=> $user->id
        ];

        $response = $this->actingAs($user)->postJson('/api/createTask', $postForm);
        $response->assertStatus(201);
    }
    public function test_create_task_no_login()
    {

        $postForm = [
            'body' => $this->faker->text
        ];

        $response = $this->postJson('/api/createTask', $postForm);
        $response->assertStatus(401);
    }
    public function test_create_task_no_input()
    {
        $user = $this->createUser();
        $token = $user->createToken('ios')->plainTextToken;

        $postForm = [
  
        ];

        $response = $this->actingAs($user)->postJson('/api/createTask', $postForm);
        $response->assertStatus(422);
    }
    public function test_delete_task_success()
    {
        $user = $this->createUser();
        $token = $user->createToken('ios')->plainTextToken;

        $postForm = [
            'id' => 1,
            'body' => $this->faker->text,
            'user_id'=> $user->id
        ];

        $response = $this->actingAs($user)->postJson('/api/createTask', $postForm);
        $response = $this->actingAs($user)->deleteJson('/api/deleteTask/1');
        
        $response->assertStatus(200);
    }
    public function test_delete_no_login()
    {

        $response = $this->deleteJson('/api/deleteTask/1');
        
        $response->assertStatus(401);
    }
    public function test_delete_not_autorise()
    {

        $user = $this->createUser();
        $token = $user->createToken('ios')->plainTextToken;

        $postForm = [
            'id' => 120,
            'body' => $this->faker->text,
            'user_id'=> 1234567
        ];

        $response = $this->actingAs($user)->postJson('/api/createTask', $postForm);
        $response = $this->actingAs($user)->deleteJson('/api/deleteTask/120');
        
        $response->assertStatus(403);
    }
    public function test_delete_not_found()
    {

        $user = $this->createUser();
        $token = $user->createToken('ios')->plainTextToken;

        $response = $this->actingAs($user)->deleteJson('/api/deleteTask/123456789123456789');
        
        $response->assertStatus(404);
    }
    public function test_show_tasks_success()
    {
        $user = $this->createUser();
        $token = $user->createToken('ios')->plainTextToken;

        $response = $this->actingAs($user)->getJson('/api/tasks');
        $response->assertStatus(200);
    }
    
    public function test_show_tasks_no_login()
    {

        $response = $this->getJson('/api/tasks');
        $response->assertStatus(401);
    }
    public function test_edit_task_success()
    {
        $user = $this->createUser();
        $token = $user->createToken('ios')->plainTextToken;

        $postForm = [
            'id' => 1,
            'body' => $this->faker->text,
            'user_id'=> $user->id
        ];
        

        $response = $this->actingAs($user)->postJson('/api/createTask', $postForm);
        $editForm = [
            'body' => $this->faker->text,
        ];
        
        $response = $this->actingAs($user)->putJson('/api/updateTask/1', $editForm);
        
        $response->assertStatus(200);
    }
    public function test_edit_task_no_login()
    {

        $editForm = [
            'body' => $this->faker->text,
        ];
        $response = $this->putJson('/api/updateTask/1', $editForm);
        
        $response->assertStatus(401);
    }
    public function test_edit_task_not_access()
    {
        $user = $this->createUser();
        $token = $user->createToken('ios')->plainTextToken;

        $postForm = [
            'id' => 1245,
            'body' => $this->faker->text,
            'user_id'=> 1456
        ];

        $response = $this->actingAs($user)->postJson('/api/createTask', $postForm);
        $editForm = [
            'body' => $this->faker->text,
        ];
        $response = $this->actingAs($user)->putJson('/api/updateTask/1245', $editForm);
        
        $response->assertStatus(403);
    }
    public function test_edit_task_not_found()
    {
        $user = $this->createUser();
        $token = $user->createToken('ios')->plainTextToken;

        $editForm = [
            'body' => $this->faker->text,
        ];
        $response = $this->actingAs($user)->putJson('/api/updateTask/99', $editForm);
        
        $response->assertStatus(404);
    }
    public function test_complete_task_success()
    {
        $user = $this->createUser();
        $token = $user->createToken('ios')->plainTextToken;

        $postForm = [
            'id' => 1,
            'body' => $this->faker->text,
            'user_id'=> $user->id
        ];

        $response = $this->actingAs($user)->postJson('/api/createTask', $postForm);

        $response = $this->actingAs($user)->getJson('/api/completeTask/1');
        
        $response->assertStatus(200);
    }


    

}
