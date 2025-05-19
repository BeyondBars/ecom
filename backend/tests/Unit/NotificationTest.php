<?php

namespace Tests\Unit;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_notification_belongs_to_user(): void
    {
        $user = User::factory()->create();
        $notification = Notification::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(User::class, $notification->user);
        $this->assertEquals($user->id, $notification->user->id);
    }

    public function test_user_has_many_notifications(): void
    {
        $user = User::factory()->create();
        $notifications = Notification::factory()->count(3)->create(['user_id' => $user->id]);

        $this->assertCount(3, $user->notifications);
        $this->assertInstanceOf(Notification::class, $user->notifications->first());
    }

    public function test_notification_can_be_marked_as_read(): void
    {
        $notification = Notification::factory()->unread()->create();

        $this->assertFalse($notification->is_read);
        $this->assertNull($notification->read_at);

        $notification->update([
            'is_read' => true,
            'read_at' => now(),
        ]);

        $this->assertTrue($notification->is_read);
        $this->assertNotNull($notification->read_at);
    }

    public function test_notification_can_be_marked_as_unread(): void
    {
        $notification = Notification::factory()->read()->create();

        $this->assertTrue($notification->is_read);
        $this->assertNotNull($notification->read_at);

        $notification->update([
            'is_read' => false,
            'read_at' => null,
        ]);

        $this->assertFalse($notification->is_read);
        $this->assertNull($notification->read_at);
    }

    public function test_notification_has_correct_type(): void
    {
        $infoNotification = Notification::factory()->info()->create();
        $successNotification = Notification::factory()->success()->create();
        $warningNotification = Notification::factory()->warning()->create();
        $errorNotification = Notification::factory()->error()->create();

        $this->assertEquals('info', $infoNotification->type);
        $this->assertEquals('success', $successNotification->type);
        $this->assertEquals('warning', $warningNotification->type);
        $this->assertEquals('error', $errorNotification->type);
    }
}
