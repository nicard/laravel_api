<?php

namespace Tests\Feature\Models;

use App\Models\Genre;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GenreTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testList()
    {
        factory(Genre::class, 1)->create();
        $list = Genre::all();
        $this->assertCount(1, $list);

        $categoryKey = array_keys($list->first()->getAttributes());
        $this->assertEqualsCanonicalizing([
            'id',
            'name',
            'is_active',
            'created_at',
            'updated_at',
            'deleted_at'
        ],$categoryKey);
    }

    public function testCreate(){
        $genre = Genre::create(['name' => 'testCreate']);
        $genre->refresh();
        $this->assertEquals('testCreate', $genre->name);
        $this->assertTrue($genre->is_active);
        $this->assertRegExp('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-5][0-9a-f]{3}-[089ab][0-9a-f]{3}-[0-9a-f]{12}$/i',  $genre->id);

        $genre = Genre::create([
            'name' => 'testCreate',
            'is_active' => false
        ]);
        $genre->refresh();
        $this->assertFalse($genre->is_active);

        $genre = Genre::create([
            'name' => 'testCreate',
            'is_active' => true
        ]);
        $genre->refresh();
        $this->assertTrue($genre->is_active);
    }

    public function testDelete(){
        $genre = Genre::create([
            'name' => 'testCreate',
            'is_active' => false
        ]);
        $genre->refresh();
        $this->assertCount(1 , Genre::All()->toArray());
        $genre->delete();
        $this->assertCount(0 , Genre::All()->toArray());
    }
}
