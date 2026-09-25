<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ServiceUnit;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreServiceRequest;
use App\Http\Requests\Admin\UpdateServiceRequest;
use App\Models\LaundryService;
use App\Models\ServiceCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ServiceController extends Controller
{
    public function index(Request $request): View
    {
        $categoryId = $request->integer('category_id') ?: null;
        $services = LaundryService::query()
            ->with('category')
            ->when($categoryId, fn ($query) => $query->where('category_id', $categoryId))
            ->orderBy('name')
            ->get();

        return view('admin.catalog.services', [
            'services' => $services,
            'categories' => ServiceCategory::query()->where('is_active', true)->orderBy('sort_order')->get(),
            'selectedCategoryId' => $categoryId,
            'units' => ServiceUnit::cases(),
        ]);
    }

    public function store(StoreServiceRequest $request): RedirectResponse
    {
        $data = $request->validated();
        LaundryService::create([
            ...$data,
            'name' => trim($data['name']),
            'slug' => $this->uniqueSlug($data['name']),
            'is_active' => $request->boolean('is_active', true),
        ]);

        return back()->with('success', 'Layanan berhasil ditambahkan.');
    }

    public function update(UpdateServiceRequest $request, LaundryService $laundryService): RedirectResponse
    {
        $data = $request->validated();
        $laundryService->update([
            ...$data,
            'name' => trim($data['name']),
            'slug' => $laundryService->slug,
            'is_active' => $request->boolean('is_active'),
        ]);

        return back()->with('success', 'Layanan berhasil diperbarui.');
    }

    public function destroy(LaundryService $laundryService): RedirectResponse
    {
        if ($laundryService->orderItems()->exists()) {
            $laundryService->update(['is_active' => false]);

            return back()->with('success', 'Layanan telah digunakan pada pesanan, sehingga dinonaktifkan.');
        }

        $laundryService->delete();

        return back()->with('success', 'Layanan berhasil dihapus.');
    }

    private function uniqueSlug(string $name): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $counter = 2;

        while (LaundryService::query()->where('slug', $slug)->exists()) {
            $slug = $base.'-'.$counter++;
        }

        return $slug;
    }
}
