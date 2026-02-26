<?php

namespace App\Http\Controllers;

use App\Models\Hotel;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class HotelController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Hotel::class);
        
        $query = Hotel::query();
        
        // Filtres
        if ($request->has('city')) {
            $query->where('city', $request->city);
        }
        
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }
        
        $hotels = $query->orderBy('city')->orderBy('name')->paginate(15);
        
        return view('hotels.index', compact('hotels'));
    }

    public function create(): View
    {
        Gate::authorize('create', Hotel::class);
        
        return view('hotels.create');
    }

    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('create', Hotel::class);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'city' => 'required|in:mecca,medina',
            'stars' => 'required|integer|min:1|max:5',
            'distance_haram' => 'nullable|numeric|min:0',
            'main_image' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
            'room_images.*' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
        ]);
        
        // Upload main image
        if ($request->hasFile('main_image')) {
            $validated['main_image'] = $request->file('main_image')->store('hotels/main', 'public');
        }
        
        // Upload room images
        if ($request->hasFile('room_images')) {
            $roomImages = [];
            foreach ($request->file('room_images') as $image) {
                $roomImages[] = $image->store('hotels/rooms', 'public');
            }
            $validated['room_images'] = $roomImages;
        }
        
        Hotel::create($validated);
        
        return redirect()->route('hotels.index')
            ->with('success', 'Hôtel créé avec succès.');
    }

    public function show(Hotel $hotel): View
    {
        Gate::authorize('view', $hotel);
        
        return view('hotels.show', compact('hotel'));
    }

    public function edit(Hotel $hotel): View
    {
        Gate::authorize('update', $hotel);
        
        return view('hotels.edit', compact('hotel'));
    }

    public function update(Request $request, Hotel $hotel): RedirectResponse
    {
        Gate::authorize('update', $hotel);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'city' => 'required|in:mecca,medina',
            'stars' => 'required|integer|min:1|max:5',
            'distance_haram' => 'nullable|numeric|min:0',
            'main_image' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
            'room_images.*' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
        ]);
        
        // Upload main image
        if ($request->hasFile('main_image')) {
            // Delete old image if exists
            if ($hotel->main_image) {
                Storage::disk('public')->delete($hotel->main_image);
            }
            $validated['main_image'] = $request->file('main_image')->store('hotels/main', 'public');
        }
        
        // Upload new room images
        if ($request->hasFile('room_images')) {
            $existingImages = $hotel->room_images ?? [];
            $newImages = [];
            foreach ($request->file('room_images') as $image) {
                $newImages[] = $image->store('hotels/rooms', 'public');
            }
            $validated['room_images'] = array_merge($existingImages, $newImages);
        }
        
        $hotel->update($validated);
        
        return redirect()->route('hotels.index')
            ->with('success', 'Hôtel mis à jour avec succès.');
    }

    public function destroy(Hotel $hotel): RedirectResponse
    {
        Gate::authorize('delete', $hotel);
        
        $hotel->delete();
        
        return redirect()->route('hotels.index')
            ->with('success', 'Hôtel supprimé avec succès.');
    }
}
