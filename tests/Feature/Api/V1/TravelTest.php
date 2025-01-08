<?php

declare(strict_types=1);

use App\Models\Travel;
use Database\Seeders\RoleSeeder;
use Symfony\Component\HttpFoundation\Response;

test('guest can access travels index', function (): void {
    $response = $this->get(route('v1.travels.index'));

    expect($response->status())->toBe(Response::HTTP_OK);
});

test('travels list is paginated correctly', function (): void {
    Travel::factory(config('crud.pagination.per_page.default') + 1)->create(['is_public' => true]);
    $response = $this->get(route('v1.travels.index'));

    expect($response->json('data'))->toHaveCount(config('crud.pagination.per_page.default'));
    expect($response->json('meta.last_page'))->toBe(2);
});

test('travels list shows only public travels', function (): void {
    Travel::factory(config('crud.pagination.per_page.default'))->create(['is_public' => false]);
    Travel::factory(1)->create(['is_public' => true]);
    $response = $this->get(route('v1.travels.index'));

    expect($response->json('data'))->toHaveCount(1);
    expect($response->json('meta.last_page'))->toBe(1);
});

test('user guest cannot access travel store', function (): void {
    $this->seed(RoleSeeder::class);
    $guest = App\Models\User::factory()->create();
    $guest->roles()->sync([]);

    $response = $this->actingAs($guest)
        ->postJson(route('v1.travels.store'), [
            'is_public' => true,
            'name' => 'travel test',
            'description' => 'a test travel',
            'number_of_days' => random_int(1, 14),
        ]);

    expect($response->status())->toBe(Response::HTTP_FORBIDDEN);
});

test('user editor cannot access travel store', function (): void {
    $this->seed(RoleSeeder::class);
    $editor = App\Models\User::factory()->create();
    $editor->roles()->sync([]);
    $editor->assignRole('editor');

    $response = $this->actingAs($editor)
        ->postJson(route('v1.travels.store'), [
            'is_public' => true,
            'name' => 'travel test',
            'description' => 'a test travel',
            'number_of_days' => random_int(1, 14),
        ]);

    expect($response->status())->toBe(Response::HTTP_FORBIDDEN);
});

test('user admin can access travel store', function (): void {
    $this->seed(RoleSeeder::class);
    $admin = App\Models\User::factory()->create();
    $admin->roles()->sync([]);
    $admin->assignRole('admin');

    $response = $this->actingAs($admin)
        ->postJson(route('v1.travels.store'), [
            'is_public' => true,
            'name' => 'travel test',
            'description' => 'a test travel',
            'number_of_days' => random_int(1, 14),
        ]);

    expect($response->status())->toBe(Response::HTTP_OK);
});

test('created new travel is in database', function (): void {
    $this->seed(RoleSeeder::class);
    $admin = App\Models\User::factory()->create();
    $admin->roles()->sync([]);
    $admin->assignRole('admin');

    $response = $this->actingAs($admin)
        ->postJson(route('v1.travels.store'), [
            'is_public' => true,
            'name' => 'travel test',
            'description' => 'a test travel',
            'number_of_days' => random_int(1, 14),
        ]);

    $this->get(route('v1.travels.index'))
        ->assertJsonFragment(['name' => 'travel test']);
});

test('user guest role cannot access travel update', function (): void {
    $this->seed(RoleSeeder::class);
    $guest = App\Models\User::factory()->create();
    $guest->roles()->sync([]);

    $travel = Travel::factory()->create();

    $response = $this->actingAs($guest)
        ->putJson(route('v1.travels.update', $travel), [
            'is_public' => true,
            'name' => 'travel test',
            'description' => 'a test travel',
            'number_of_days' => random_int(1, 14),
        ]);

    expect($response->status())->toBe(Response::HTTP_FORBIDDEN);
});

test('user editor can access travel update', function (): void {
    $this->seed(RoleSeeder::class);
    $editor = App\Models\User::factory()->create();
    $editor->roles()->sync([]);
    $editor->assignRole('editor');

    $travel = Travel::factory()->create();

    $response = $this->actingAs($editor)
        ->putJson(route('v1.travels.update', $travel), [
            'is_public' => true,
            'name' => 'travel test',
            'description' => 'a test travel',
            'number_of_days' => random_int(1, 14),
        ]);

    expect($response->status())->toBe(Response::HTTP_OK);
});

test('user admin can access travel update', function (): void {
    $this->seed(RoleSeeder::class);
    $admin = App\Models\User::factory()->create();
    $admin->roles()->sync([]);
    $admin->assignRole('admin');

    $travel = Travel::factory()->create();

    $response = $this->actingAs($admin)
        ->putJson(route('v1.travels.update', $travel), [
            'is_public' => true,
            'name' => 'travel test',
            'description' => 'a test travel',
            'number_of_days' => random_int(1, 14),
        ]);

    expect($response->status())->toBe(Response::HTTP_OK);
})->skip();
