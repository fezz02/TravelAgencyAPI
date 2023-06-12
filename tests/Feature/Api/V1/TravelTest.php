<?php
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
