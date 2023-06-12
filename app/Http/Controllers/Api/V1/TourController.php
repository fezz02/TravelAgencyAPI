<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTourRequest;
use App\Http\Resources\TourResource;
use App\Models\Tour;
use App\Models\Travel;
use App\Services\TourService;
use Illuminate\Http\Request;

class TourController extends Controller
{
    
    public function index(Travel $travel)
    {
        $tours = $travel->tours()
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
