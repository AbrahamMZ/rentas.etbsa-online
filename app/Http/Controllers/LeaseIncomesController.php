<?php

namespace App\Http\Controllers;

use App\Models\LeaseIncomes;
use App\Models\Machinery;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Response;

class LeaseIncomesController extends Controller
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
        LeaseIncomes::create(
            Request::validate([
                'contract_lease' => ['required'],
                'reference' => ['required'],
                'term_lease' => ['required'],
                'amount' => ['required'],
                'start_date' => ['required'],
                'end_date' => ['required'],
                'total_income' => ['required'],
                'machinery_id' => ['required'],
            ])
        );

        return Redirect::back()
            ->with('success', 'Ingreso Agregado con Exito.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\LeaseIncomes  $leaseIncomes
     * @return \Illuminate\Http\Response
     */
    public function show(LeaseIncomes $leaseIncomes)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\LeaseIncomes  $leaseIncomes
     * @return \Illuminate\Http\Response
     */
    public function edit(LeaseIncomes $leaseIncomes)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\LeaseIncomes  $leaseIncomes
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, LeaseIncomes $leaseIncomes)
    {
        $leaseIncomes->update(
            Request::validate([
                'contract_lease' => ['required'],
                'reference' => ['required'],
                'term_lease' => ['required'],
                'amount' => ['required'],
                'start_date' => ['required'],
                'end_date' => ['required'],
                'total_income' => ['required'],
                'machinery_id' => ['required'],
            ])
        );

        return Redirect::back()
            ->with('success', 'Ingreso Actualizado con Exito.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\LeaseIncomes  $leaseIncomes
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(LeaseIncomes $leaseIncomes)
    {
        $leaseIncomes->delete();
        return Redirect::back()
            ->with('success', 'Ingreso Eliminado con Exito.');
    }
    public function options()
    {
        $machineries = Machinery::get()->map->only('id', 'name');
        return Response::json(compact('machineries'));
    }
}