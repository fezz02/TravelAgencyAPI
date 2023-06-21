<?php

use App\Models\Role;
use \App\Models\Travel;
use Symfony\Component\HttpFoundation\Response;

test('guest can access travels index', function () {
    $response = $this->get(route('v1.travels.index'));

    $response->assertStatus(Response::HTTP_OK);
});

test('travels list is paginated correctly', function () {
    Travel::factory(config('crud.pagination.per_page.default') + 1)->create(['is_public' => true]);
    $response = $this->get(route('v1.travels.index'));

    $response->assertJsonCount(config('crud.pagination.per_page.default'), 'data');
    $response->assertJsonPath('meta.last_page', 2);
});

test('travels list shows only public travels', function () {
    Travel::factory(config('crud.pagination.per_page.default'))->create(['is_public' => false]);
    Travel::factory(1)->create(['is_public' => true]);
    $response = $this->get(route('v1.travels.index'));

    $response->assertJsonCount(1, 'data');
    $response->assertJsonPath('meta.last_page', 1);
});

test('user guest cannot access travel store', function () {
    $this->seed(RoleSeeder::class);
    $guest = \App\Models\User::factory()->create();
    $guest->roles()->sync([]);

    $params = http_build_query([
        'is_public' => true,
        'name' => 'travel test',
        'description' => 'a test travel',
        'number_of_days' => random_int(1, 14)
    ]);

    $response = $this->actingAs($guest)
        ->postJson(route('v1.travels.store').'?'.$params);

    $response->assertStatus(Response::HTTP_FORBIDDEN);
});

test('user editor cannot access travel store', function () {
    $this->seed(RoleSeeder::class);
    $editor = \App\Models\User::factory()->create();
    $editor->roles()->sync([]);
    $editor->assignRole('editor');

    $params = http_build_query([
        'is_public' => true,
        'name' => 'travel test',
        'description' => 'a test travel',
        'number_of_days' => random_int(1, 14)
    ]);

    $response = $this->actingAs($editor)
        ->postJson(route('v1.travels.store').'?'.$params);

    $response->assertStatus(Response::HTTP_FORBIDDEN);
});

test('user admin can access travel store', function () {
    $this->seed(RoleSeeder::class);
    $admin = \App\Models\User::factory()->create();
    $admin->roles()->sync([]);
    $admin->assignRole('admin');

    $params = http_build_query([
        'is_public' => true,
        'name' => 'travel test',
        'description' => 'a test travel',
        'number_of_days' => random_int(1, 14)
    ]);

    $response = $this->actingAs($admin)
        ->postJson(route('v1.travels.store').'?'.$params);

    $response->assertStatus(Response::HTTP_OK);
});

test('created new travel is in database', function () {
    $this->seed(RoleSeeder::class);
    $admin = \App\Models\User::factory()->create();
    $admin->roles()->sync([]);
    $admin->assignRole('admin');

    $params = http_build_query([
        'is_public' => true,
        'name' => 'travel test',
        'description' => 'a test travel',
        'number_of_days' => random_int(1, 14)
    ]);

    $response = $this->actingAs($admin)
        ->postJson(route('v1.travels.store').'?'.$params);

    $response = $this->get(route('v1.travels.index'));

    $response->assertJsonFragment(['name' => 'travel test']);
});


test('user guest role cannot access travel update', function () {
    $this->seed(RoleSeeder::class);
    $guest = \App\Models\User::factory()->create();
    $guest->roles()->sync([]);

    $params = http_build_query([
        'is_public' => true,
        'name' => 'travel test',
        'description' => 'a test travel',
        'number_of_days' => random_int(1, 14)
    ]);

    $travel = Travel::factory()->create();

    $response = $this->actingAs($guest)
        ->putJson(route('v1.travels.update', $travel).'?'.$params);

    $response->assertStatus(Response::HTTP_FORBIDDEN);
});

test('user editor can access travel update', function () {
    $this->seed(RoleSeeder::class);
    $editor = \App\Models\User::factory()->create();
    $editor->roles()->sync([]);
    $editor->assignRole('editor');


    $params = http_build_query([
        'is_public' => true,
        'name' => 'travel test',
        'description' => 'a test travel',
        'number_of_days' => random_int(1, 14)
    ]);

    $travel = Travel::factory()->create();

    $response = $this->actingAs($editor)
        ->putJson(route('v1.travels.update', $travel).'?'.$params);

    $response->assertStatus(Response::HTTP_OK);
});

test('user admin can access travel update', function () {
    $this->seed(RoleSeeder::class);
    $admin = \App\Models\User::factory()->create();
    $admin->roles()->sync([]);
    $admin->assignRole('admin');

    $params = http_build_query([
        'is_public' => true,
        'name' => 'travel test',
        'description' => 'a test travel',
        'number_of_days' => random_int(1, 14)
    ]);

    $travel = Travel::factory()->create();

    $response = $this->actingAs($admin)
        ->putJson(route('v1.travels.update', $travel).'?'.$params);

    $response->assertStatus(Response::HTTP_OK);
});