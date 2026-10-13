<?php

namespace Tests\Feature\Shop;

use App\Models\Banner;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CatalogStorefrontTest extends TestCase
{
    use RefreshDatabase;

    public function testOnlyVisibleProductsAreListed()
    {
        $visible = Product::factory()->create();
        Product::factory()->draft()->create();
        Product::factory()->create(['category_id' => Category::factory()->inactive()]);

        $this->getJson('shop/products')
            ->assertSuccessful()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $visible->id)
            ->assertJsonMissingPath('data.0.cost')
            ->assertJsonMissingPath('data.0.status');
    }

    public function testDraftProductPageIsNotFound()
    {
        $product = Product::factory()->draft()->create();

        $this->getJson("shop/products/{$product->slug}")->assertNotFound();
    }

    public function testCategoryFilterIncludesSubcategories()
    {
        $women = Category::factory()->create(['name' => 'Women']);
        $dresses = Category::factory()->create(['name' => 'Dresses', 'parent_id' => $women->id]);
        Product::factory()->create(['category_id' => $dresses->id]);
        Product::factory()->create();

        $this->getJson('shop/products?filter[category]=women')->assertJsonCount(1, 'data');
    }

    public function testFiltersAndSortUseTheSalePrice()
    {
        Product::factory()->create(['name' => 'A', 'price' => 3000, 'sale_price' => 500]);
        Product::factory()->create(['name' => 'B', 'price' => 1000]);
        Product::factory()->create(['name' => 'C', 'price' => 2000]);

        $this->getJson('shop/products?sort=price')
            ->assertJsonPath('data.0.name', 'A')
            ->assertJsonPath('data.2.name', 'C');

        $this->getJson('shop/products?filter[max_price]=999')->assertJsonCount(1, 'data');
        $this->getJson('shop/products?filter[on_sale]=1')->assertJsonCount(1, 'data');
    }

    public function testBrandSearchAndStockFilters()
    {
        $brand = Brand::factory()->create(['name' => 'Luxe']);
        Product::factory()->create(['brand_id' => $brand->id, 'name' => 'Silk Blouse']);
        Product::factory()->outOfStock()->create(['name' => 'Linen Shirt']);

        $this->getJson('shop/products?filter[brand]=luxe')->assertJsonCount(1, 'data');
        $this->getJson('shop/products?filter[search]=silk')->assertJsonCount(1, 'data');
        $this->getJson('shop/products?filter[in_stock]=1')->assertJsonCount(1, 'data');
    }

    public function testRelatedProductsComeFromTheSameCategory()
    {
        $product = Product::factory()->create();
        Product::factory()->count(2)->create(['category_id' => $product->category_id]);
        Product::factory()->create();

        $this->getJson("shop/products/{$product->slug}/related")->assertJsonCount(2, 'data');
    }

    public function testOnlyLiveBannersAreShown()
    {
        Banner::factory()->create(['title' => 'Live']);
        Banner::factory()->create(['is_active' => false]);
        Banner::factory()->create(['starts_at' => now()->addDay()]);
        Banner::factory()->create(['ends_at' => now()->subDay()]);
        Banner::factory()->create(['placement' => 'home_promo']);

        $this->getJson('shop/banners?placement=home_hero')
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.title', 'Live');
    }

    public function testInactiveCategoriesAndBrandsAreHidden()
    {
        Category::factory()->create();
        Category::factory()->inactive()->create();
        Brand::factory()->create(['is_active' => false]);

        $this->getJson('shop/categories')->assertJsonCount(1, 'data');
        $this->getJson('shop/brands')->assertJsonCount(0, 'data');
    }
}
