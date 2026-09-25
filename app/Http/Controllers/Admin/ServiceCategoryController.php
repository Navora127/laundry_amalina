<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreServiceCategoryRequest;
use App\Http\Requests\Admin\UpdateServiceCategoryRequest;
use App\Models\ServiceCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ServiceCategoryController extends Controller
{
    public function index(): View
    {
        return view('admin.catalog.categories', [
            'categories' => ServiceCategory::query()->withCount('services')->orderBy('sort_order')->orderBy('name')->get(),
        ]);
    }

    public function store(StoreServiceCategoryRequest $request): RedirectResponse
    {
        $data = $request->validated();
        ServiceCategory::create([
            ...$data,
            'name' => trim($data['name']),
            'slug' => $this->uniqueSlug($data['name']),
            'sort_order' => $data['sort_order'] ?? 0,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return back()->with('success', 'Kategori layanan berhasil ditambahkan.');
    }

    public function update(UpdateServiceCategoryRequest $request, ServiceCategory $serviceCategory): RedirectResponse
    {
        $data = $request->validated();
        $serviceCategory->update([
            ...$data,
            'name' => trim($data['name']),
            'slug' => $serviceCategory->slug,
            'sort_order' => $data['sort_order'] ?? $serviceCategory->sort_order,
            'is_active' => $request->boolean('is_active'),
        ]);

        return back()->with('success', 'Kategori layanan berhasil diperbarui.');
    }

    public function destroy(ServiceCategory $serviceCategory): RedirectResponse
    {
        if ($serviceCategory->services()->exists()) {
            return back()->with('error', 'Kategori yang masih memiliki layanan tidak dapat dihapus. Nonaktifkan kategori sebagai gantinya.');
        }

        $serviceCategory->delete();

        return back()->with('success', 'Kategori layanan berhasil dihapus.');
    }

    private function uniqueSlug(string $name, ?ServiceCategory $ignore = null): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $counter = 2;

        while (ServiceCategory::query()->where('slug', $slug)->when($ignore, fn ($query) => $query->where('id', '!=', $ignore->getKey()))->exists()) {
            $slug = $base.'-'.$counter++;
        }

        return $slug;
    }
}
