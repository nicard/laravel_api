<?php

namespace Tests\Feature\Htpp\Controllers\Api;

use App\Models\Category;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Support\Facades\Lang;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CategoryControllerTest extends TestCase
{
    use DatabaseMigrations;

    public function testIndex()
    {
        $category = factory(Category::class)->create();
        $response = $this->get(route('categories.index'));

        $response
            ->assertStatus(200)
            ->assertJson([$category->toArray()]);
    }

    public function testShow()
    {
        $category = factory(Category::class)->create();
        $response = $this->get(route('categories.show',['category'=>$category->id]));

        $response
            ->assertStatus(200)
            ->assertJson($category->toArray());
    }

    public function testeInvalidationData(){

        $response = $this->json('POST', route('categories.store'),[]);
        $this->assertInvalidationRequired($response);

        $response = $this->json('POST', route('categories.store'),[
            'name' => str_repeat('a', 300),
            'is_active' => 'a'

        ]);
        $this->assertInvalidationData($response);

        $category = factory(Category::class)->create();
        $response = $this->json('PUT', route('categories.update', ['category' => $category->id]),[]);
        $this->assertInvalidationRequired($response);

        $response = $this->json('PUT',
            route('categories.update', ['category' => $category->id]),
            [
            'name' => str_repeat('a', 300),
            'is_active' => 'a'
        ]);
        $this->assertInvalidationData($response);

    }

    public function testStore(){
        $response = $this->json('POST',
            route('categories.store'),
            [
                'name' => 'teste'
            ]);
        $id = $response->json('id');
        $category = Category::find($id);
        $response
            ->assertStatus(201)
            ->assertJson($category->toArray());
        $this->assertTrue($response->json('is_active'));
        $this->assertNull($response->json('description'));



        $response = $this->json('POST',
            route('categories.store'),
            [
                'name' => 'teste',
                'description' => 'description',
                'is_active' => false
            ]);
        $response->assertJsonFragment([
            'description' => 'description',
            'is_active' => false
        ]);
    }

    public function testUpdate(){
        $category = factory(Category::class)->create([
            'description' => 'description',
            'is_active' => false
        ]);
        $category->refresh();
        $response = $this->json('PUT',
            route('categories.update', ['category'=>$category->id]),
            [
                'name' => 'teste',
                'description' => 'teste',
                'is_active' => true
            ]);
        $id = $response->json('id');
        $category = Category::find($id);
        $response
            ->assertStatus(200)
            ->assertJson($category->toArray())
            ->assertJsonFragment([
                'description' => 'teste',
                'is_active' => true
            ]);

        $category->description = 'teste';
        $category->save();

        $response = $this->json('PUT',
            route('categories.update', ['category'=>$category->id]),
            [
                'name' => 'teste',
                'description' => ''
            ]);
        $response->assertJsonFragment([
                'description' => null
            ]);
    }

    public function assertInvalidationRequired(TestResponse $response){
        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['name'])
            ->assertJsonMissingValidationErrors(['is_active'])
            ->assertJsonFragment([\Lang::get('validation.required', ['attribute'=> 'name'])]);

    }

    public function assertInvalidationData(TestResponse $response){
        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'is_active'])
            ->assertJsonFragment([\Lang::get('validation.max.string', ['attribute'=> 'name' ,'max' => '255'])])
            ->assertJsonFragment([\Lang::get('validation.boolean', ['attribute'=> 'is active'])]);
    }
}
