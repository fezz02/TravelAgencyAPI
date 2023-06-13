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
