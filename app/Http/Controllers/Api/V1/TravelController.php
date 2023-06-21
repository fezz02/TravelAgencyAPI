<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTravelRequest;
use App\Http\Requests\UpdateTravelRequest;
use App\Http\Resources\TravelResource;
use App\Models\Travel;
use App\Services\TravelService;

/**
 * @group Travel endpoints
 */
class TravelController extends Controller
{
    /**
     * GET Travels
     *
     * Returns paginated list of travels.
     *
     * @queryParam page integer Page number. Example: 1
     *
     * @response {"data":[{"id":"9958e389-5edf-48eb-8ecd-e058985cf3ce","name":"First travel", ...}}
     */
    public function index()
    {
        $travels = Travel::query()
            ->onlyPublic()
            ->paginate(config('crud.pagination.per_page.default'));

        return TravelResource::collection($travels);
    }

    /**
     * POST Travel
     *
     * Creates a new Travel record.
     *
     * @authenticated
     *
     * @response {"data":{"id":"996a36ca-2693-4901-9c55-7136e68d81d5","name":"My new travel 234","slug":"my-new-travel-234", ...}
     * @response 422 {"message":"The name has already been taken.","errors":{"name":["The name has already been taken."]}}
     */
    public function store(StoreTravelRequest $request, TravelService $service)
    {
        $travel = $service->store($request->validated());

        return response()->json(TravelResource::make($travel), 200);
    }

    /**
     * PUT Travel
     *
     * Updates new Travel record.
     *
     * @authenticated
     *
     * @response {"data":{"id":"996a36ca-2693-4901-9c55-7136e68d81d5","name":"My new travel 234", ...}
     * @response 422 {"message":"The name has already been taken.","errors":{"name":["The name has already been taken."]}}
     */
    public function update(UpdateTravelRequest $request, Travel $travel, TravelService $service)
    {
        $travel = $service->update($request->validated(), $travel);

        return response()->json(TravelResource::make($travel), 200);
    }
}
