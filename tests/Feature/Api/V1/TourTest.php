<?php
use \App\Models\Travel;
use \App\Models\Tour;
use Symfony\Component\HttpFoundation\Response;

test('guest can access tours using public travel slug', function () {
    $travel = Travel::factory(['is_public' => true])->create();
    $response = $this->get(route('v1.travels.tours.index', ['travel' => $travel->slug]));

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
