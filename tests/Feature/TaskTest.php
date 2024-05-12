<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TaskTest extends TestCase
{
    use RefreshDatabase;

    public function test_store()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/api/tasks', [
            'name' => 'Task 1',
        ]);

        $this->assertJson($response->getContent());

        $response->assertStatus(200); // Or assertCreated

        $this->assertDatabaseHas('tasks', [
            'name' => 'Task 1',
        ]);

        $this->assertDatabaseCount('tasks', 1);
    }

    public function test_index()
    {
        $user = User::factory()->create();

        $this->actingAs($user)->postJson('/api/tasks', [
            'name' => 'Task 1',
        ]);

        $this->actingAs($user)->postJson('/api/tasks', [
            'name' => 'Task 2',
        ]);

        $response = $this->get('/api/tasks');

        $this->assertJson($response->getContent());

        $this->assertIsArray(json_decode($response->getContent())->data);

        $response->assertSee([
            'name' => 'Task 2',
        ]);

        $this->assertCount(2, json_decode($response->getContent())->data);

        $response->assertStatus(200); // Or assertOk
    }

    public function test_show()
    {
        $user = User::factory()->create();

        $new_task = $this->actingAs($user)->postJson('/api/tasks', [
            'name' => 'Task 1',
        ]);

        $new_task_id = json_decode($new_task->getContent())->data->id;

        $response = $this->get('/api/tasks/' . $new_task_id);

        $this->assertJson($response->getContent());

        $response->assertSee([
            'name' => 'Task 1'
        ]);

        $response->assertOk();
    }

    public function test_update()
    {
        $user = User::factory()->create();

        $new_task = $this->actingAs($user)->postJson('/api/tasks', [
            'name' => 'Task 1',
        ]);

        $new_task_id = json_decode($new_task->getContent())->data->id;

        $this->actingAs($user)->putJson('/api/tasks/' . $new_task_id, [
            'name' => 'Task 1 (done)',
            'status' => 'done'
        ]);

        $response = $this->get('/api/tasks/' . $new_task_id);

        $this->assertJson($response->getContent());

        $response->assertSee([
            'name' => 'Task 1 (done)',
            'status' => 'done',
        ]);

        $this->assertDatabaseHas('tasks', [
            'name' => 'Task 1 (done)',
            'status' => 'done',
        ]);

        $response->assertOk();
    }
}
