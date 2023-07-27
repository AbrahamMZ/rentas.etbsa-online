<?php

namespace App\Http\Controllers;

use App\Models\LeaseFees;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Response;

class LeaseFeesController extends Controller
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
        LeaseFees::create(
            Request::validate([
                'folio' => ['nullable'],
                'amount_income' => ['required'],
                'payment_date' => ['required'],
                'note' => ['nullable'],
                'lease_id' => ['required'],
            ])
        );

        return Redirect::back()
            ->with('success', 'Ingreso Agregado con Exito.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\LeaseFees  $leaseFees
     * @return \Illuminate\Http\RedirectResponse
     */
    public function show(LeaseFees $leaseFees)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\LeaseFees  $leaseFees
     * @return \Illuminate\Http\RedirectResponse
     */
    public function edit(LeaseFees $leaseFees)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\LeaseFees  $leaseFees
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, LeaseFees $leaseFees)
    {
        $leaseFees->update(
            Request::validate([
                'folio' => ['nullable'],
                'amount_income' => ['required'],
                'payment_date' => ['required'],
                'note' => ['nullable'],
                'lease_id' => ['required'],
            ])
        );

        return Redirect::back()
            ->with('success', 'Ingreso Actualizado con Exito.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\LeaseFees  $leaseFees
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(LeaseFees $leaseFees)
    {
        $leaseFees->delete();
        return Redirect::back()
            ->with('success', 'Ingreso Eliminado con Exito.');
    }

    // public function options()
    // {
    //     $machineries = Machinery::get()->map->only('id', 'name');
    //     return Response::json(compact('machineries'));
    // }
}