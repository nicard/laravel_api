<?php

namespace Tests\Feature\Models;

use App\Models\Category;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CategoryTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testList()
    {
        factory(Category::class, 1)->create();
        $list = Category::all();
        $this->assertCount(1, $list);

        $categoryKey = array_keys($list->first()->getAttributes());
        $this->assertEqualsCanonicalizing([
            'id',
            'name',
            'description',
            'is_active',
            'created_at',
            'updated_at',
            'deleted_at'
        ],$categoryKey);
    }

    public function testCreate(){
        $category = Category::create(['name' => 'testCreate']);
        $category->refresh();
        $this->assertEquals('testCreate', $category->name);
        $this->assertNull($category->description);
        $this->assertTrue($category->is_active);

        $category = Category::create([
            'name' => 'testCreate',
            'description' => "description"
        ]);
        $category->refresh();
        $this->assertEquals('description', $category->description);
        $this->assertRegExp('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-5][0-9a-f]{3}-[089ab][0-9a-f]{3}-[0-9a-f]{12}$/i',  $category->id);

        $category = Category::create([
            'name' => 'testCreate',
            'is_active' => false
        ]);
        $category->refresh();
        $this->assertFalse($category->is_active);

        $category = Category::create([
            'name' => 'testCreate',
            'is_active' => true
        ]);
        $category->refresh();
        $this->assertTrue($category->is_active);
    }

    public function testDelete(){
        $category = Category::create([
            'name' => 'testCreate',
            'is_active' => false
        ]);
        $category->refresh();
        $this->assertCount(1 , Category::All()->toArray());
        $category->delete();
        $this->assertCount(0 , Category::All()->toArray());
    }
}
