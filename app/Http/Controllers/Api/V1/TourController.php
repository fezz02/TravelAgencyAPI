<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTourRequest;
use App\Http\Requests\TourRequest;
use App\Http\Resources\TourResource;
use App\Models\Tour;
use App\Models\Travel;
use App\Services\TourService;

class TourController extends Controller
{
    
    public function index(TourRequest $request, Travel $travel)
    {
        $tours = $travel->tours()
            ->when($request->priceFrom, function($query) use ($request) {
                $query->where('price', '>', $request->priceFrom * 100);
            })
            ->when($request->priceTo, function($query) use ($request) {
                $query->where('price', '<', $request->priceTo * 100);
            })
            ->when($request->dateFrom, function($query) use ($request) {
                $query->whereDate('starting_date', '>', $request->dateFrom);
            })
            ->when($request->dateTo, function($query) use ($request) {
                $query->whereDate('ending_date', '<', $request->dateTo);
            })
            ->when($request->sortBy && $request->sortOrder, function($query) use ($request) {
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
