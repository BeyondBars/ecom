<?php

namespace App\Providers;

use App\Models\BlogPost;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Invoice;
use App\Models\Like;
use App\Models\Notification;
use App\Models\Order;
use App\Models\Product;
use App\Models\Role;
use App\Models\User;
use App\Models\Wishlist;
use App\Policies\BlogPostPolicy;
use App\Policies\BrandPolicy;
use App\Policies\CategoryPolicy;
use App\Policies\CommentPolicy;
use App\Policies\InvoicePolicy;
use App\Policies\LikePolicy;
use App\Policies\NotificationPolicy;
use App\Policies\OrderPolicy;
use App\Policies\ProductPolicy;
use App\Policies\RolePolicy;
use App\Policies\UserPolicy;
use App\Policies\WishlistPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        User::class => UserPolicy::class,
        Role::class => RolePolicy::class,
        Product::class => ProductPolicy::class,
        Category::class => CategoryPolicy::class,
        Brand::class => BrandPolicy::class,
        Order::class => OrderPolicy::class,
        BlogPost::class => BlogPostPolicy::class,
        Comment::class => CommentPolicy::class,
        Invoice::class => InvoicePolicy::class,
        Wishlist::class => WishlistPolicy::class,
        Like::class => LikePolicy::class,
        Notification::class => NotificationPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
    }
}
