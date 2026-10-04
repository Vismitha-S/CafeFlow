<?php

namespace App\Http\Controllers;

use App\Models\Cafe;
use App\Http\Requests\StoreCafeRequest;
use App\Http\Requests\UpdateCafeRequest;
use App\Services\CafeService;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class CafeController extends Controller
{
    use AuthorizesRequests;

    protected $cafeService;

    public function __construct(CafeService $cafeService)
    {
        $this->cafeService = $cafeService;
    }

    public function index(Request $request)
    {
        $user = $request->user();
        
        $this->authorize('viewAny', Cafe::class);

        $query = Cafe::query()->with('hours');

        if ($user->isAdmin()) {
            // Can see all cafes
        } elseif ($user->isOwner()) {
            $query->where('owner_id', $user->id);
        } else {
            // Customer
            $query->where('status', 'active');
        }

        return response()->json($query->get());
    }

    public function show(Request $request, Cafe $cafe)
    {
        $this->authorize('view', $cafe);
        $cafe->load('hours');
        return response()->json($cafe);
    }

    public function store(StoreCafeRequest $request)
    {
        $this->authorize('create', Cafe::class);

        $cafe = $this->cafeService->createCafe($request->user(), $request->validated());

        return response()->json($cafe->load('hours'), 201);
    }

    public function update(UpdateCafeRequest $request, Cafe $cafe)
    {
        $this->authorize('update', $cafe);

        $cafe = $this->cafeService->updateCafe($request->user(), $cafe, $request->validated());

        return response()->json($cafe->load('hours'));
    }

    public function destroy(Request $request, Cafe $cafe)
    {
        $this->authorize('delete', $cafe);

        $cafe->delete();

        return response()->json(null, 204);
    }
}
