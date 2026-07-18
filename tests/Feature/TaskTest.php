<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_create_task(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/tasks', [
            'title' => 'Learn Laravel Testing',
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('title', 'Learn Laravel Testing')
            ->assertJsonPath('user_id', $user->id);

        $this->assertDatabaseHas('tasks', [
            'title' => 'Learn Laravel Testing',
            'user_id' => $user->id,
        ]);
    }

    public function test_guest_cannot_create_task(): void
    {
        $response = $this->postJson('/api/tasks', [
            'title' => 'Unauthorized Task',
        ]);

        $response->assertStatus(401);
    }

    public function test_user_only_sees_their_own_tasks(): void
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();

        Task::factory()->create(['user_id' => $userA->id, 'title' => 'User A Task']);
        Task::factory()->create(['user_id' => $userB->id, 'title' => 'User B Task']);

        $response = $this->actingAs($userA, 'sanctum')->getJson('/api/tasks');

        $response->assertStatus(200)
            ->assertJsonCount(1)
            ->assertJsonFragment(['title' => 'User A Task'])
            ->assertJsonMissing(['title' => 'User B Task']);
    }

    public function test_user_cannot_view_another_users_task(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();

        $task = Task::factory()->create(['user_id' => $owner->id]);

        $response = $this->actingAs($otherUser, 'sanctum')->getJson("/api/tasks/{$task->id}");

        $response->assertStatus(404);
    }

    public function test_user_cannot_delete_another_users_task(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();

        $task = Task::factory()->create(['user_id' => $owner->id]);

        $response = $this->actingAs($otherUser, 'sanctum')->deleteJson("/api/tasks/{$task->id}");

        $response->assertStatus(404);

        $this->assertDatabaseHas('tasks', ['id' => $task->id]);
    }

    public function test_user_can_update_their_own_task(): void
    {
        $user = User::factory()->create();
        $task = Task::factory()->create(['user_id' => $user->id, 'status' => 'pending']);

        $response = $this->actingAs($user, 'sanctum')->putJson("/api/tasks/{$task->id}", [
            'status' => 'completed',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('status', 'completed');
    }
}
