<?php

namespace App\Http\Controllers;

use App\Models\MachineryMonthlyExpenses;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;

class MachineryMonthlyExpensesController extends Controller
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
        MachineryMonthlyExpenses::create(
            Request::validate([
                'machinery_id' => ['required'],
                'monthly_expense_types_id' => ['required'],
                'description' => ['required'],
                'base_cost_type' => ['required'],
                'base_cost_amount' => ['nullable'],
                'percent' => ['nullable'],
                'amount' => ['required'],
            ])
        );
        return Redirect::back()->with('success', 'Gasto Mensual registrado con Exito.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\MachineryMonthlyExpenses  $machineryMonthlyExpenses
     * @return \Illuminate\Http\Response
     */
    public function show(MachineryMonthlyExpenses $machineryMonthlyExpenses)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\MachineryMonthlyExpenses  $machineryMonthlyExpenses
     * @return \Illuminate\Http\Response
     */
    public function edit(MachineryMonthlyExpenses $machineryMonthlyExpenses)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\MachineryMonthlyExpenses  $machineryMonthlyExpenses
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, MachineryMonthlyExpenses $machineryMonthlyExpenses)
    {

        $machineryMonthlyExpenses->update(
            array_merge(
                Request::validate([
                    'machinery_id' => ['required'],
                    'monthly_expense_types_id' => ['required'],
                    'description' => ['required'],
                    'base_cost_type' => ['required'],
                    'base_cost_amount' => ['nullable'],
                    'percent' => ['nullable'],
                    'amount' => ['required'],
                ])
                ,
                [
                    'machinery_id' => $machineryMonthlyExpenses->machinery_id
                ]
            )
        );
        return Redirect::back()->with('success', 'Gasto Mensual Actualizado con Exito.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\MachineryMonthlyExpenses  $machineryMonthlyExpenses
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(MachineryMonthlyExpenses $machineryMonthlyExpenses)
    {
        $machineryMonthlyExpenses->delete();
        return Redirect::back()->with('success', 'Gasto Mensual Eliminado con Exito.');
    }
}