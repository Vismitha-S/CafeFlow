<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCafeRequest;
use App\Http\Requests\UpdateCafeRequest;
use App\Models\Cafe;
use App\Services\CafeService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OwnerCafeController extends Controller
{
    use AuthorizesRequests;

    public function __construct(protected CafeService $cafeService) {}

    /**
     * Show the onboarding / Create Your Cafe page.
     */
    public function create(Request $request): View|RedirectResponse
    {
        $owner = $request->user();

        // If the owner already has a cafe, redirect to the dashboard
        if ($owner->hasCafe()) {
            return redirect()->route('owner.dashboard')
                ->with('info', 'You already have an active cafe registered with CafeFlow.');
        }

        $this->authorize('create', Cafe::class);

        return view('owner.cafe.create', compact('owner'));
    }

    /**
     * Store a newly created cafe for the authenticated owner.
     */
    public function store(StoreCafeRequest $request): RedirectResponse
    {
        $owner = $request->user();

        if ($owner->hasCafe()) {
            return redirect()->route('owner.dashboard')
                ->with('error', 'You can only manage one cafe per owner account.');
        }

        $this->authorize('create', Cafe::class);

        $cafe = $this->cafeService->createCafe($owner, $request->validated());

        return redirect()->route('owner.dashboard')
            ->with('success', "Congratulations! '{$cafe->name}' has been created successfully. Welcome to your CafeFlow Owner Dashboard.");
    }

    /**
     * Show the My Cafe edit/management page.
     */
    public function edit(Request $request): View|RedirectResponse
    {
        $owner = $request->user();
        $cafe = $owner->cafe()->with('hours')->first();

        if (! $cafe) {
            return redirect()->route('owner.cafe.create')
                ->with('info', 'Please set up your cafe details first.');
        }

        $this->authorize('update', $cafe);

        $hours = $cafe->hours()->orderBy('day_of_week', 'asc')->get()->keyBy('day_of_week');

        return view('owner.cafe.edit', compact('owner', 'cafe', 'hours'));
    }

    /**
     * Update the owner's cafe details and operating hours.
     */
    public function update(UpdateCafeRequest $request): RedirectResponse
    {
        $owner = $request->user();
        $cafe = $owner->cafe;

        if (! $cafe) {
            return redirect()->route('owner.cafe.create');
        }

        $this->authorize('update', $cafe);

        $this->cafeService->updateCafe($owner, $cafe, $request->validated());

        return redirect()->route('owner.dashboard')
            ->with('success', 'Cafe details and operating hours updated successfully.');
    }
}
