<?php

use App\Models\Role;
use \App\Models\Travel;
use \App\Models\Tour;
use Database\Seeders\RoleSeeder;
use Symfony\Component\HttpFoundation\Response;

test('guest can access tours using public travel slug', function () {
    $this->seed(RoleSeeder::class);
    $guest = \App\Models\User::factory()->create();

    $travel = Travel::factory(['is_public' => true])->create();
    $response = $this->actingAs($guest)->get(route('v1.travels.tours.index', ['travel' => $travel->slug]));

    $response->assertStatus(200);
});


test('tours list is paginated correctly', function () {
    Travel::factory(config('crud.pagination.per_page.default') + 1)->create(['is_public' => true]);
    $response = $this->get(route('v1.travels.index'));

    $response->assertJsonCount(config('crud.pagination.per_page.default'), 'data');
    $response->assertJsonPath('meta.current_page', 1);
    $response->assertJsonPath('meta.last_page', 2);
});

test('guest can see formatted tours price', function () {
    $travel = Travel::factory(['is_public' => true])->create();
    Tour::factory()->create([
        'price' => 241.65
    ]);

    $response = $this->get(route('v1.travels.tours.index', ['travel' => $travel->slug]));

    $response->assertJsonCount(1, 'data');
    $response->assertJsonFragment(['price' => '241.65']);
});

test('tours can be filtered by price', function () {
    $travel = Travel::factory(['is_public' => true])->create();
    Tour::factory()->create([
        'price' => 85
    ]);
    Tour::factory(10)->create([
        'price' => random_int(100, 500)
    ]);

    $params = http_build_query([
        'priceFrom' => 84,
        'priceTo' => 86,
    ]);
    $response = $this->get(route('v1.travels.tours.index', ['travel' => $travel->slug]).'?'.$params);

    $response->assertStatus(Response::HTTP_OK);
    $response->assertJsonCount(1, 'data');
});

test('tours can be filtered by date', function () {
    $travel = Travel::factory(['is_public' => true])->create();
    $tour = Tour::factory()->create();
    Tour::factory(10)->create([
        'price' => random_int(100, 500)
    ]);

    $params = http_build_query([
        'dateFrom' => $tour->starting_date,
        'dateTo' => $tour->ending_date,
    ]);
    $response = $this->get(route('v1.travels.tours.index', ['travel' => $travel->slug]).'?'.$params);

    $response->assertStatus(Response::HTTP_OK);
    $response->assertJsonFragment([
        'starting_date' => $tour->starting_date,
        'ending_date' => $tour->ending_date
    ]);
});

test('tours validation works', function () {
    $travel = Travel::factory(['is_public' => true])->create();
    Tour::factory()->create();

    $params = http_build_query([
        'orderBy' => 'test',
    ]);
    $response = $this->getJson(route('v1.travels.tours.index', ['travel' => $travel->slug]).'?'.$params);

    $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
});

test('unauthenticated user cannot access tour store', function () {
    $travel = Travel::factory(['is_public' => true])->create();
    Tour::factory()->create();

    $params = http_build_query([
        'name' => 'test tour',
        'starting_date' => now()->format('Y-m-d'),
        'ending_date' => now()->addWeek()->format('Y-m-d'),
        'price' => random_int(20, 100)
    ]);

    $response = $this->postJson(route('v1.travels.tours.store', ['travel' => $travel->slug]).'?'.$params);

    $response->assertStatus(Response::HTTP_UNAUTHORIZED);
});

test('user guest cannot access tour store', function () {
    $this->seed(RoleSeeder::class);
    $guest = \App\Models\User::factory()->create();
    $guest->roles()->sync([]);
    
    $travel = Travel::factory(['is_public' => true])->create();
    Tour::factory()->create();

    $params = http_build_query([
        'name' => 'test tour',
        'starting_date' => now()->format('Y-m-d'),
        'ending_date' => now()->addWeek()->format('Y-m-d'),
        'price' => random_int(20, 100)
    ]);

    $response = $this->actingAs($guest)
        ->postJson(route('v1.travels.tours.store', ['travel' => $travel->slug]).'?'.$params);

    $response->assertStatus(Response::HTTP_FORBIDDEN);
});

test('user editor cannot access tour store', function () {
    $this->seed(RoleSeeder::class);
    $editor = \App\Models\User::factory()->create();
    $editor->roles()->sync([]);
    $editor->assignRole('editor');

    $travel = Travel::factory(['is_public' => true])->create();
    Tour::factory()->create();

    $params = http_build_query([
        'name' => 'test tour',
        'starting_date' => now()->format('Y-m-d'),
        'ending_date' => now()->addWeek()->format('Y-m-d'),
        'price' => random_int(20, 100)
    ]);

    $response = $this->actingAs($editor)
        ->postJson(route('v1.travels.tours.store', ['travel' => $travel->slug]).'?'.$params);

    $response->assertStatus(Response::HTTP_FORBIDDEN);
});

test('user admin can access tour store', function () {
    $this->seed(RoleSeeder::class);
    $admin = \App\Models\User::factory()->create();
    $admin->roles()->sync([]);
    $admin->assignRole('admin');

    $travel = Travel::factory(['is_public' => true])->create();
    Tour::factory()->create();

    $params = http_build_query([
        'name' => 'test tour',
        'starting_date' => now()->format('Y-m-d'),
        'ending_date' => now()->addWeek()->format('Y-m-d'),
        'price' => random_int(20, 100)
    ]);

    $response = $this->actingAs($admin)
        ->postJson(route('v1.travels.tours.store', ['travel' => $travel->slug]).'?'.$params);

    $response->assertStatus(Response::HTTP_OK);
});

test('tour store ending_date should be after starting_date', function () {
    $this->seed(RoleSeeder::class);
    $admin = \App\Models\User::factory()->create();
    $admin->roles()->sync([]);
    $admin->assignRole('admin');

    $travel = Travel::factory(['is_public' => true])->create();
    Tour::factory()->create();

    $params = http_build_query([
        'name' => 'test tour',
        'starting_date' => now()->format('Y-m-d'),
        'ending_date' => now()->subWeek()->format('Y-m-d'),
        'price' => random_int(20, 100)
    ]);

    $response = $this->actingAs($admin)
        ->postJson(route('v1.travels.tours.store', ['travel' => $travel->slug]).'?'.$params);

    $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
});

test('created new tour is in database', function () {
    $this->seed(RoleSeeder::class);
    $admin = \App\Models\User::factory()->create();
    $admin->roles()->sync([]);
    $admin->assignRole('admin');
    
    $travel = Travel::factory(['is_public' => true])->create();
    Tour::factory()->create();

    $params = http_build_query([
        'name' => 'test tour',
        'starting_date' => now()->format('Y-m-d'),
        'ending_date' => now()->subWeek()->format('Y-m-d'),
        'price' => random_int(20, 100)
    ]);

    $response = $this->actingAs($admin)
        ->postJson(route('v1.travels.tours.store', ['travel' => $travel->slug]).'?'.$params);

    $this->assertDatabaseHas('travels', $travel->toArray());
});
