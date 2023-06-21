<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTourRequest;
use App\Http\Requests\TourRequest;
use App\Http\Resources\TourResource;
use App\Models\Travel;
use App\Services\TourService;

/**
 * @group Public endpoints
 */
class TourController extends Controller
{
    /**
     * GET Travel Tours
     *
     * Returns paginated list of tours by travel slug.
     *
     * @urlParam travel_slug string Travel slug. Example: "first-travel"
     *
     * @bodyParam priceFrom number. Example: "123.45"
     * @bodyParam priceTo number. Example: "234.56"
     * @bodyParam dateFrom date. Example: "2023-06-01"
     * @bodyParam dateTo date. Example: "2023-07-01"
     * @bodyParam sortBy string. Example: "price"
     * @bodyParam sortOrder string. Example: "asc" or "desc"
     *
     * @response {"data":[{"id":"9958e389-5edf-48eb-8ecd-e058985cf3ce","name":"Tour on Sunday","starting_date":"2023-06-11","ending_date":"2023-06-16", ...}
     *
     */
    public function index(TourRequest $request, Travel $travel)
    {
        $tours = $travel->tours()
            ->when($request->priceFrom, function ($query) use ($request) {
                $query->where('price', '>', $request->priceFrom * 100);
            })
            ->when($request->priceTo, function ($query) use ($request) {
                $query->where('price', '<', $request->priceTo * 100);
            })
            ->when($request->dateFrom, function ($query) use ($request) {
                $query->whereDate('starting_date', '>', $request->dateFrom);
            })
            ->when($request->dateTo, function ($query) use ($request) {
                $query->whereDate('ending_date', '<', $request->dateTo);
            })
            ->when($request->sortBy && $request->sortOrder, function ($query) use ($request) {
                $query->orderBy($request->orderBy, $request->orderDirection);
            })
            ->orderBy('starting_date')
            ->paginate(config('crud.pagination.per_page.default'));

        return TourResource::collection($tours);
    }

    public function store(StoreTourRequest $request, Travel $travel, TourService $service)
    {
        $tour = $service->store($request->validated(), $travel);

        return response()->json(TourResource::make($tour), 200);
    }
}
