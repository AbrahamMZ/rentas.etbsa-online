<?php

namespace App\Http\Controllers;

use App\Models\MachineryServiceExpenses;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\Rule;

class MachineryServiceExpensesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\RedirectResponse
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
        MachineryServiceExpenses::create(
            Request::validate([
                'machinery_id' => ['required'],
                'name' => ['required'],
                'description' => ['required'],
                'amount' => ['required'],
            ])
        );

        return Redirect::back()
            ->with('success', 'Cargo de Servicio Agregado con Exito.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\MachineryServiceExpenses  $machineryServiceExpenses
     * @return \Illuminate\Http\RedirectResponse
     */
    public function show(MachineryServiceExpenses $machineryServiceExpenses)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\MachineryServiceExpenses  $machineryServiceExpenses
     * @return \Illuminate\Http\RedirectResponse
     */
    public function edit(MachineryServiceExpenses $machineryServiceExpenses)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\MachineryServiceExpenses  $machineryServiceExpenses
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, MachineryServiceExpenses $machineryServiceExpenses)
    {
        $machineryServiceExpenses->update(
            Request::validate([
                'machinery_id' => ['required'],
                'name' => ['required'],
                'description' => ['required'],
                'amount' => ['required'],
            ])
        );

        return Redirect::back()
            ->with('success', 'Cargo de Servicio Actualizado con Exito.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\MachineryServiceExpenses  $machineryServiceExpenses
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(MachineryServiceExpenses $machineryServiceExpenses)
    {
        $machineryServiceExpenses->forceDelete();
        return Redirect::back()
            ->with('success', 'Cargo de Servicio Eliminado con Exito.');
    }
}