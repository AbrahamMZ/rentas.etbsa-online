<?php

namespace App\Http\Controllers;

use App\Models\ExpenseCatalog;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class ExpenseCatalogController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Inertia\Response
     */
    public function index()
    {
        return Inertia::render('ExpenseCatalogs/Index', [
            'filters' => Request::all(['search', 'trashed', 'page']),
            'items' => ExpenseCatalog::orderByName()
                ->filter(Request::only(['search', 'trashed', 'folio']))
                ->paginate(10)
                ->transform(function ($fixes_cost) {
                    return [
                        'id' => $fixes_cost->id,
                        'name' => $fixes_cost->name,
                        'deleted_at' => $fixes_cost->deleted_at,
                    ];
                }),
        ]);
    }

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
        ExpenseCatalog::create(
            Request::validate([
                'name' => ['required', Rule::unique('expense_catalogs')],
            ])
        );
        return Redirect::route('expenses')
            ->with('success', 'Gasto Fijo Registrada con Exito.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ExpenseCatalog  $expense
     * @return \Illuminate\Http\Response
     */
    public function show(ExpenseCatalog $expense)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ExpenseCatalog  $expense
     * @return \Illuminate\Http\Response
     */
    public function edit(ExpenseCatalog $expense)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ExpenseCatalog  $expense
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, ExpenseCatalog $expense)
    {
        $expense->update(
            Request::validate([
                'name' => ['required', Rule::unique('fixes_costs')],
            ])
        );

        return Redirect::back()
            ->with('success', 'Gasto Fijo Actualizado con Exito.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ExpenseCatalog  $expense
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(ExpenseCatalog $expense)
    {
        $expense->delete();

        return Redirect::back()->with('success', 'Gasto Fijo Eliminada.');
    }

    /**
     * restore the specified resource from storage.
     *
     * @param  \App\Models\ExpenseCatalog  $expense
     * @return \Illuminate\Http\RedirectResponse
     */
    public function restore(ExpenseCatalog $expense)
    {
        $expense->restore();

        return Redirect::back()->with('success', 'Gasto Fijo restored.');
    }
    public function options()
    {
        $expense_options = ExpenseCatalog::get()->map->only('id', 'name');
        return Response::json(compact('expense_options'));
    }
}