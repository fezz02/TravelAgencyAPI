<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTravelRequest;
use App\Http\Requests\UpdateTravelRequest;
use App\Http\Resources\TravelResource;
use App\Models\Travel;
use App\Services\TravelService;
use Illuminate\Http\Request;

class TravelController extends Controller
{
    public function index()
    {
        $travels = Travel::query()
            ->onlyPublic()
            ->paginate(config('crud.pagination.per_page.default'));

        return TravelResource::collection($travels);
    }

    public function store(StoreTravelRequest $request, TravelService $service)
    {
        $travel = $service->store($request->validated());
        return response()->json(TravelResource::make($travel), 200);
    }
    
    public function update(UpdateTravelRequest $request, Travel $travel, TravelService $service)
    {
        $travel = $service->update($request->validated(), $travel);
        return response()->json(TravelResource::make($travel), 200);
    }
}
