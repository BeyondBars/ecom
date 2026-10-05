<?php

namespace Tests\Feature;

use App\Models\Blog;
use App\Models\User;
use App\Support\Enums\BlogStatus;
use App\Support\Enums\UserRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BlogTest extends TestCase
{
    use RefreshDatabase;

    public function testCanListBlogs()
    {
        $user = $this->createStandardUser();
        $blogs = Blog::factory()->count(5)->create();

        $response = $this->be($user)->getJson('blogs');
        
        $response->assertSuccessful()
            ->assertJsonCount(5, 'data')
            ->assertJsonStructure([
                'data' => [[
                    'id',
                    'title',
                    'content',
                    'status',
                    'created_at',
                    'updated_at'
                ]],
                'meta' => [
                    'current_page',
                    'from',
                    'last_page',
                    'per_page',
                    'to',
                    'total'
                ]
            ]);
    }

    public function testCanFilterBlogsByStatus()
    {
        $user = $this->createStandardUser();
        Blog::factory()->count(3)->create(['status' => BlogStatus::Pending]);
        Blog::factory()->count(2)->create(['status' => BlogStatus::Enabled]);

        $response = $this->be($user)->getJson('blogs?status=pending');
        
        $response->assertSuccessful()
            ->assertJsonCount(3, 'data');

        $response = $this->be($user)->getJson('blogs?status=enabled');
        
        $response->assertSuccessful()
            ->assertJsonCount(2, 'data');
    }

    public function testCanSearchBlogs()
    {
        $user = $this->createStandardUser();
        Blog::factory()->create(['title' => 'Test Blog Post']);
        Blog::factory()->create(['title' => 'Another Post']);

        $response = $this->be($user)->getJson('blogs?search=Test');
        
        $response->assertSuccessful()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.title', 'Test Blog Post');
    }

    public function testCanCreateBlog()
    {
        $user = $this->createUserWithRole(UserRole::Blogger);
        $blogData = [
            'title' => 'New Blog Post',
            'content' => 'Blog content here',
            'status' => BlogStatus::Pending->value
        ];

        $response = $this->be($user)->postJson('blogs', $blogData);
        
        $response->assertSuccessful();
        
        $this->assertDatabaseHas('blogs', [
            'title' => 'New Blog Post',
            'content' => 'Blog content here',
            'status' => BlogStatus::Pending->value
        ]);
    }

    public function testCannotCreateBlogWithInvalidData()
    {
        $user = $this->createUserWithRole(UserRole::Blogger);
        
        $response = $this->be($user)->postJson('blogs', [
            'title' => '',
            'content' => '',
            'status' => 'invalid'
        ]);
        
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title', 'content', 'status']);
    }

    public function testCanViewBlog()
    {
        $user = $this->createStandardUser();
        $blog = Blog::factory()->create();

        $response = $this->be($user)->getJson("blogs/{$blog->id}");
        
        $response->assertSuccessful()
            ->assertJson([
                'data' => [
                    'id' => $blog->id,
                    'title' => $blog->title,
                    'content' => $blog->content,
                    'status' => $blog->status->value
                ]
            ]);
    }

    public function testCanUpdateBlog()
    {
        $user = $this->createUserWithRole(UserRole::Blogger);
        $blog = Blog::factory()->create();
        
        $updateData = [
            'title' => 'Updated Title',
            'content' => 'Updated content',
            'status' => BlogStatus::Enabled->value
        ];

        $response = $this->be($user)->putJson("blogs/{$blog->id}", $updateData);
        
        $response->assertSuccessful();
        
        $this->assertDatabaseHas('blogs', [
            'id' => $blog->id,
            'title' => 'Updated Title',
            'content' => 'Updated content',
            'status' => BlogStatus::Enabled->value
        ]);
    }

    public function testCannotUpdateBlogWithInvalidData()
    {
        $user = $this->createUserWithRole(UserRole::Blogger);
        $blog = Blog::factory()->create();
        
        $response = $this->be($user)->putJson("blogs/{$blog->id}", [
            'title' => '',
            'content' => '',
            'status' => 'invalid'
        ]);
        
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title', 'content', 'status']);
    }

    public function testCanDeleteBlog()
    {
        $user = $this->createUserWithRole(UserRole::Blogger);
        $blog = Blog::factory()->create();

        $response = $this->be($user)->deleteJson("blogs/{$blog->id}");
        
        $response->assertSuccessful();
        
        $this->assertDatabaseMissing('blogs', ['id' => $blog->id]);
    }

    public function testCannotAccessNonExistentBlog()
    {
        $user = $this->createStandardUser();

        $response = $this->be($user)->getJson("blogs/999");
        
        $response->assertNotFound();
    }

    public function testCreatingABlogWithoutASlugGeneratesAUniqueOne()
    {
        Blog::factory()->create(['slug' => 'hello-world']);

        $this->be($this->createUserWithRole(UserRole::Blogger))
            ->postJson('blogs', ['title' => 'Hello World', 'content' => 'Body', 'status' => BlogStatus::Enabled->value])
            ->assertCreated()
            ->assertJsonPath('data.slug', 'hello-world-2');
    }

    public function testStandardUserCannotCreateUpdateOrDeleteBlogs()
    {
        $user = $this->createStandardUser();
        $blog = Blog::factory()->create();

        $this->be($user)->postJson('blogs', ['title' => 'T', 'content' => 'C', 'status' => 'pending'])->assertForbidden();
        $this->be($user)->putJson("blogs/{$blog->id}", ['title' => 'Changed'])->assertForbidden();
        $this->be($user)->deleteJson("blogs/{$blog->id}")->assertForbidden();

        $this->assertDatabaseHas('blogs', ['id' => $blog->id, 'title' => $blog->title]);
    }
}
