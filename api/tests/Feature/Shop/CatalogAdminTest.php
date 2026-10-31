<?php

namespace Tests\Feature\Shop;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Support\Enums\UserRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CatalogAdminTest extends TestCase
{
    use RefreshDatabase;

    public function testCustomersCannotUseTheCatalogAdmin()
    {
        $product = Product::factory()->create();

        $this->beUser();
        $this->getJson('admin/products')->assertForbidden();
        $this->postJson('admin/categories', ['name' => 'Sneaky'])->assertForbidden();
        $this->putJson("admin/products/{$product->id}", ['price' => 1])->assertForbidden();
        $this->deleteJson("admin/products/{$product->id}")->assertForbidden();
    }

    public function testGuestsMustSignIn()
    {
        $this->getJson('admin/products')->assertUnauthorized();
    }

    public function testStaffCanManageTheCatalog()
    {
        $this->be($this->createUserWithRole(UserRole::Staff));

        $category = $this->postJson('admin/categories', ['name' => 'Dresses'])
            ->assertCreated()
            ->assertJsonPath('data.slug', 'dresses')
            ->json('data');

        $brand = $this->postJson('admin/brands', ['name' => 'Luxe House'])->assertCreated()->json('data');

        $this->postJson('admin/products', [
            'name' => 'Floral Maxi Dress',
            'sku' => 'LFC-0001',
            'category_id' => $category['id'],
            'brand_id' => $brand['id'],
            'price' => 1999,
            'sale_price' => 1499,
            'stock' => 12,
            'status' => 'active',
            'options' => [
                ['name' => 'Size', 'values' => ['S', 'M']],
                ['name' => 'Color', 'values' => ['Rose', 'Black']],
            ],
            'images' => [['url' => 'https://cdn.test/a.jpg'], ['url' => 'https://cdn.test/b.jpg', 'alt' => 'Back']],
        ])
            ->assertCreated()
            ->assertJsonPath('data.slug', 'floral-maxi-dress')
            ->assertJsonPath('data.current_price', 1499)
            ->assertJsonPath('data.images.1.alt', 'Back')
            ->assertJsonPath('data.status', 'active');
    }

    public function testSalePriceMustBeBelowPrice()
    {
        $this->beAdmin();
        $category = Category::factory()->create();

        $this->postJson('admin/products', [
            'name' => 'Shirt', 'sku' => 'X1', 'category_id' => $category->id, 'price' => 999, 'sale_price' => 1200, 'stock' => 1,
        ])->assertJsonValidationErrors(['sale_price']);
    }

    public function testUpdatingImagesReplacesTheGallery()
    {
        $this->beAdmin();
        $product = Product::factory()->create();
        $product->images()->create(['url' => 'https://cdn.test/old.jpg']);

        $this->putJson("admin/products/{$product->id}", ['images' => [['url' => 'https://cdn.test/new.jpg']]])
            ->assertSuccessful()
            ->assertJsonCount(1, 'data.images')
            ->assertJsonPath('data.images.0.url', 'https://cdn.test/new.jpg');
    }

    public function testDuplicateNamesGetUniqueSlugs()
    {
        $this->beAdmin();

        $this->postJson('admin/brands', ['name' => 'Elegance'])->assertJsonPath('data.slug', 'elegance');
        $this->postJson('admin/brands', ['name' => 'Elegance'])->assertJsonPath('data.slug', 'elegance-2');
    }

    public function testCategoryWithProductsCannotBeDeleted()
    {
        $this->beAdmin();
        $product = Product::factory()->create();

        $this->deleteJson("admin/categories/{$product->category_id}")->assertStatus(422);
        $this->assertModelExists($product->category);
    }

    public function testDeletingABrandKeepsItsProducts()
    {
        $this->beAdmin();
        $brand = Brand::factory()->create();
        $product = Product::factory()->create(['brand_id' => $brand->id]);

        $this->deleteJson("admin/brands/{$brand->id}")->assertNoContent();
        $this->assertNull($product->fresh()->brand_id);
    }

    public function testAdminListIncludesDraftsAndCanFilterLowStock()
    {
        $this->beAdmin();
        Product::factory()->draft()->create(['stock' => 2]);
        Product::factory()->create(['stock' => 50]);

        $this->getJson('admin/products')->assertJsonCount(2, 'data');
        $this->getJson('admin/products?filter[low_stock]=1')->assertJsonCount(1, 'data');
        $this->getJson('admin/products?filter[status]=draft')->assertJsonCount(1, 'data');
    }

    public function testImagesCanBeUploaded()
    {
        Storage::fake('public');
        $this->beAdmin();

        $path = $this->post('admin/uploads', ['file' => UploadedFile::fake()->image('dress.jpg')], ['Accept' => 'application/json'])
            ->assertCreated()
            ->json('data.path');

        Storage::disk('public')->assertExists($path);
    }

    public function testUploadsMustBeImages()
    {
        Storage::fake('public');
        $this->beAdmin();

        $this->post('admin/uploads', ['file' => UploadedFile::fake()->create('notes.pdf', 10, 'application/pdf')], ['Accept' => 'application/json'])
            ->assertJsonValidationErrors(['file']);
    }

    public function testBannersNeedTheContentAbility()
    {
        $this->beUserAbleTo('manage catalog');
        $this->postJson('admin/banners', ['title' => 'Sale', 'image_url' => 'https://cdn.test/b.jpg'])->assertForbidden();

        $this->beUserAbleTo('manage content');
        $this->postJson('admin/banners', ['title' => 'Sale', 'image_url' => 'https://cdn.test/b.jpg'])->assertCreated();
    }
}
