<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\ServiceCategory;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CatalogController extends Controller
{
    public function index(Request $request): View
    {
        $categorySlug = $request->string('category')->toString();

        $categories = ServiceCategory::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $selectedCategory = $categorySlug !== ''
            ? $categories->firstWhere('slug', $categorySlug)
            : null;

        $services = $selectedCategory
            ? $selectedCategory->services()->with('category')->where('is_active', true)->orderBy('name')->get()
            : ServiceCategory::query()
                ->where('is_active', true)
                ->with(['services' => fn ($query) => $query->with('category')->where('is_active', true)->orderBy('name')])
                ->get()
                ->flatMap(fn (ServiceCategory $category): mixed => $category->services);

        return view('public.catalog', compact('categories', 'selectedCategory', 'services'));
    }
}
