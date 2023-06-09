<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Inertia\Response
     */
    public function index()
    {
        return Inertia::render('CategoryCatalogs/Index', [
            'filters' => Request::all(['search', 'trashed', 'page']),
            'items' => Category::orderByName()
                ->filter(Request::only(['search', 'trashed', 'folio']))
                ->paginate(10)
                ->transform(function ($category) {
                    return [
                        'id' => $category->id,
                        'name' => $category->name,
                        'parent_id' => $category->parent_id,
                        'full_name' => $category->breadcrumbs_category,
                        'deleted_at' => $category->deleted_at,
                    ];
                }),
            'categories' => Category::all(['id', 'name', 'parent_id']),
            // 'treeCategories' => Category::getTreeCategories()
        ]);
    }

    // function getCategoryArray($category)
    // {
    //     $categoryArray = [
    //         'name' => $category->name,
    //         'id' => $category->id,
    //         'children' => $category->allChildCategories->map(function ($childCategory) {
    //             return $this->getCategoryArray($childCategory);
    //         }),
    //     ];

    //     return $categoryArray;
    // }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        Category::create(
            Request::validate([
                'name' => ['required', Rule::unique('expense_catalogs')],
                'parent_id' => ['numeric', 'nullable'],
            ])
        );
        return Redirect::back()
            ->with('success', 'Categoria Registrada con Exito.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function show(Category $category)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function edit(Category $category)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Category $category)
    {
        $category->update(
            Request::validate([
                'name' => ['required', Rule::unique('categories')],
                'parent_id' => ['numeric', 'nullable'],
            ])
        );

        return Redirect::back()
            ->with('success', 'Categoria Actualizado con Exito.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Category $category)
    {
        $category->delete();

        return Redirect::back()->with('success', 'Categoria Eliminada.');
    }

    /**
     * restore the specified resource from storage.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\RedirectResponse
     */
    public function restore(Category $category)
    {
        $category->restore();

        return Redirect::back()->with('success', 'Categoria restored.');
    }
    public function options()
    {
        $category_options = Category::get()->map->only('id', 'name');
        return Response::json(compact('category_options'));
    }
}