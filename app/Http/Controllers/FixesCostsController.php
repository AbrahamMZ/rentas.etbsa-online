<?php

namespace App\Http\Controllers;

use App\Models\FixesCosts;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class FixesCostsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     */
    public function index()
    {
        return Inertia::render('FixesCosts/Index', [
            'filters' => Request::all(['search', 'trashed', 'page']),
            'items' => FixesCosts::orderByName()
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
    // public function create()
    // {
    //     //
    // }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * 
     */
    public function store(Request $request)
    {
        FixesCosts::create(
            Request::validate([
                'name' => ['required', Rule::unique('fixes_costs')],
            ])
        );

        return Redirect::route('fixes-costs')
            ->with('success', 'Gasto Fijo Registrada con Exito.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\FixesCosts  $fixesCosts
     * @return \Illuminate\Http\Response
     */
    // public function show(FixesCosts $fixesCosts)
    // {
    //     //
    // }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\FixesCosts  $fixesCosts
     * @return \Illuminate\Http\Response
     */
    // public function edit(FixesCosts $fixesCosts)
    // {
    //     //
    // }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\FixesCosts  $fixesCosts
     */
    public function update(Request $request, FixesCosts $fixesCosts)
    {
        $fixesCosts->update(
            Request::validate([
                'name' => ['required', Rule::unique('fixes_costs')],
            ])
        );

        return Redirect::route('fixes-costs')
            ->with('success', 'Gasto Fijo Actualizado con Exito.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\FixesCosts  $fixesCosts
     */
    public function destroy(FixesCosts $fixesCosts)
    {
        $fixesCosts->delete();

        return Redirect::back()->with('success', 'Gasto Fijo Eliminada.');
    }

    public function restore(FixesCosts $fixesCosts)
    {
        $fixesCosts->restore();

        return Redirect::back()->with('success', 'Gasto Fijo restored.');
    }
}