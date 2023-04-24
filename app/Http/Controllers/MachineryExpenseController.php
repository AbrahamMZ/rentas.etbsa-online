<?php

namespace App\Http\Controllers;

use App\Pivots\MachineryExpense;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Illuminate\Validation\Rule;

class MachineryExpenseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
        MachineryExpense::create(
            Request::validate([
                'machinery_id' => ['required'],
                'expense_id' => ['required'],
                'name' => ['required'],
                'reference' => ['required'],
                'amount' => ['required'],
                'charge_date' => ['required'],
            ])
        );
        return Redirect::back()->with('success', 'Gasto registrado con Exito.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Pivots\MachineryExpense  $machineryExpense
     * @return \Illuminate\Http\Response
     */
    public function show(MachineryExpense $machineryExpense)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Pivots\MachineryExpense  $machineryExpense
     * @return \Illuminate\Http\Response
     */
    public function edit(MachineryExpense $machineryExpense)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Pivots\MachineryExpense  $machineryExpense
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, MachineryExpense $machineryExpense)
    {

        $machineryExpense->update(
            array_merge(
                Request::validate([
                    'expense_id' => ['required'],
                    'name' => ['required'],
                    'reference' => ['required'],
                    'amount' => ['required'],
                    'charge_date' => ['required'],
                ])
                ,
                [
                    'machinery_id' => $machineryExpense->machinery_id
                ]
            )
        );
        return Redirect::back()->with('success', 'Gasto  Actualizado con Exito.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Pivots\MachineryExpense  $machineryExpense
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(MachineryExpense $machineryExpense)
    {
        $machineryExpense->delete();
        return Redirect::back()->with('success', 'Gasto  Eliminado con Exito.');

    }
}