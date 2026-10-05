<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCafeTableRequest;
use App\Http\Requests\UpdateCafeTableRequest;
use App\Models\Cafe;
use App\Models\CafeTable;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class CafeTableController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request, Cafe $cafe)
    {
        // To list tables for a cafe, the user must be able to view the cafe
        $this->authorize('view', $cafe);

        $query = $cafe->tables();

        if ($request->user()->isCustomer()) {
            $query->where('status', 'active');
        }

        return response()->json($query->get());
    }

    public function show(Request $request, CafeTable $cafeTable)
    {
        $this->authorize('view', $cafeTable);

        return response()->json($cafeTable);
    }

    public function store(StoreCafeTableRequest $request, Cafe $cafe)
    {
        // The user must be able to update the cafe to add tables to it (or create tables, but cafe update ensures ownership)
        $this->authorize('update', $cafe);

        $table = $cafe->tables()->create($request->validated());

        return response()->json($table, 201);
    }

    public function update(UpdateCafeTableRequest $request, CafeTable $cafeTable)
    {
        $this->authorize('update', $cafeTable);

        $cafeTable->update($request->validated());

        return response()->json($cafeTable);
    }

    public function destroy(Request $request, CafeTable $cafeTable)
    {
        $this->authorize('delete', $cafeTable);

        $cafeTable->delete();

        return response()->json(null, 204);
    }
}
