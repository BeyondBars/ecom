<?php

namespace Tests\Feature;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class NotificationApiTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        Sanctum::actingAs($this->user);
    }

    public function test_user_can_get_their_notifications(): void
    {
        // Create notifications for the user
        Notification::factory()->count(5)->create(['user_id' => $this->user->id]);
        
        // Create notifications for another user
        $anotherUser = User::factory()->create();
        Notification::factory()->count(3)->create(['user_id' => $anotherUser->id]);

        $response = $this->getJson('/api/notifications');

        $response->assertStatus(200);
        $response->assertJsonCount(5, 'data');
    }

    public function test_user_can_filter_notifications_by_type(): void
    {
        // Create notifications of different types
        Notification::factory()->count(3)->info()->create(['user_id' => $this->user->id]);
        Notification::factory()->count(2)->success()->create(['user_id' => $this->user->id]);
        Notification::factory()->count(1)->warning()->create(['user_id' => $this->user->id]);
        Notification::factory()->count(1)->error()->create(['user_id' => $this->user->id]);

        $response = $this->getJson('/api/notifications?type=info');

        $response->assertStatus(200);
        $response->assertJsonCount(3, 'data');
    }

    public function test_user_can_filter_notifications_by_read_status(): void
    {
        // Create read and unread notifications
        Notification::factory()->count(3)->read()->create(['user_id' => $this->user->id]);
        Notification::factory()->count(4)->unread()->create(['user_id' => $this->user->id]);

        $response = $this->getJson('/api/notifications?is_read=0');

        $response->assertStatus(200);
        $response->assertJsonCount(4, 'data');
    }

    public function test_user_can_view_a_notification(): void
    {
        $notification = Notification::factory()->create(['user_id' => $this->user->id]);

        $response = $this->getJson("/api/notifications/{$notification->id}");

        $response->assertStatus(200);
        $response->assertJson([
            'id' => $notification->id,
            'title' => $notification->title,
            'message' => $notification->message,
            'type' => $notification->type,
        ]);
    }

    public function test_user_cannot_view_another_users_notification(): void
    {
        $anotherUser = User::factory()->create();
        $notification = Notification::factory()->create(['user_id' => $anotherUser->id]);

        $response = $this->getJson("/api/notifications/{$notification->id}");

        $response->assertStatus(403);
    }

    public function test_user_can_mark_notification_as_read(): void
    {
        $notification = Notification::factory()->unread()->create(['user_id' => $this->user->id]);

        $response = $this->patchJson("/api/notifications/{$notification->id}/mark-as-read");

        $response->assertStatus(200);
        $response->assertJson([
            'id' => $notification->id,
            'is_read' => true,
        ]);
        $this->assertNotNull($response->json('read_at'));
    }

    public function test_user_can_mark_notification_as_unread(): void
    {
        $notification = Notification::factory()->read()->create(['user_id' => $this->user->id]);

        $response = $this->patchJson("/api/notifications/{$notification->id}/mark-as-unread");

        $response->assertStatus(200);
        $response->assertJson([
            'id' => $notification->id,
            'is_read' => false,
        ]);
        $this->assertNull($response->json('read_at'));
    }

    public function test_user_can_mark_all_notifications_as_read(): void
    {
        // Create unread notifications
        Notification::factory()->count(5)->unread()->create(['user_id' => $this->user->id]);

        $response = $this->patchJson("/api/notifications/mark-all-as-read");

        $response->assertStatus(200);
        
        // Check that all notifications are now read
        $this->assertDatabaseCount('notifications', 5);
        $this->assertDatabaseMissing('notifications', [
            'user_id' => $this->user->id,
            'is_read' => false,
        ]);
    }

    public function test_user_can_get_unread_notifications_count(): void
    {
        // Create read and unread notifications
        Notification::factory()->count(3)->read()->create(['user_id' => $this->user->id]);
        Notification::factory()->count(4)->unread()->create(['user_id' => $this->user->id]);

        $response = $this->getJson("/api/notifications/unread-count");

        $response->assertStatus(200);
        $response->assertJson([
            'count' => 4,
        ]);
    }

    public function test_user_can_delete_a_notification(): void
    {
        $notification = Notification::factory()->create(['user_id' => $this->user->id]);

        $response = $this->deleteJson("/api/notifications/{$notification->id}");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('notifications', ['id' => $notification->id]);
    }

    public function test_user_cannot_delete_another_users_notification(): void
    {
        $anotherUser = User::factory()->create();
        $notification = Notification::factory()->create(['user_id' => $anotherUser->id]);

        $response = $this->deleteJson("/api/notifications/{$notification->id}");

        $response->assertStatus(403);
        $this->assertDatabaseHas('notifications', ['id' => $notification->id]);
    }
}
