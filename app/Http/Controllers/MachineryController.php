<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\ExpenseCatalog;
use App\Models\FixesCosts;
use App\Models\Machinery;
use App\Pivots\MachineryExpense;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class MachineryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     *
     */
    public function index()
    {
        return Inertia::render('Machinery/Index', [
            'filters' => Request::all(['search', 'trashed', 'page']),
            'items' => Machinery::orderByName()
                ->filter(Request::only(['search', 'trashed', 'folio']))
                ->paginate(10)
                ->transform(function ($machinery) {
                    return [
                        'id' => $machinery->id,
                        'category' => $machinery->category->name,
                        'name' => $machinery->name,
                        'equipment_serial' => $machinery->equipment_serial,
                        'economic_serial' => $machinery->economic_serial,
                        'engine_serial' => $machinery->engine_serial,
                        'description' => $machinery->description,
                        'cost_price' => $machinery->cost_price,
                        'acquisition_date' => $machinery->acquisition_date,
                        'deleted_at' => $machinery->deleted_at,
                        'total_expenses_amount' => $machinery->total_expenses_amount,
                        'total_service_expenses_amount' => $machinery->total_service_expenses_amount,
                        'total_cost_equipment' => $machinery->total_cost_equipment,
                    ];
                }),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     */
    public function create()
    {
        return Inertia::render('Machinery/Create', [
            'formOptions' => $this->getOptionsForm()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     * 
     */
    public function store(Request $request)
    {

        return DB::transaction(function () {
            tap(
                Machinery::create(
                    Request::validate([
                        'category_id' => ['required'],
                        'name' => ['required'],
                        'equipment_serial' => ['required', 'max:50', Rule::unique('machineries')],
                        'economic_serial' => ['required', 'max:50', Rule::unique('machineries')],
                        'engine_serial' => ['required', 'max:50', Rule::unique('machineries')],
                        'description' => ['nullable'],
                        'cost_price' => ['required'],
                        'acquisition_date' => ['nullable'],
                    ])
                ),
                function (Machinery $machinery) {
                    $this->createOrUpdateMachineryExpense(
                        $machinery,
                        Request::validate(['expenses' => ['array']])['expenses']
                    );
                    $this->attachServicesExpenses(
                        $machinery,
                        Request::validate(['services_expenses' => ['array']])['services_expenses']
                    );
                }
            );
            return Redirect::route('machineries')
                ->with('success', 'Maquinaria Registrada con Exito.');
        });

    }

    public function updateMachineryFixesCosts(Request $request, Machinery $machinery)
    {
        $this->syncFixesCosts(
            $machinery,
            Request::validate([
                'fixes_costs' => ['array'],
                'fixes_cost.amount' => ['numeric', 'gte:0']
            ])['fixes_costs']
        );

        return Redirect::back()->with('success', 'Gastos Fijos Actualizados con Exito.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Machinery  $machinery
     * 
     */
    public function show(Machinery $machinery)
    {

        return Inertia::render('Machinery/Show', [
            'item' => [
                'id' => $machinery->id,
                'category' => $machinery->category->name,
                'name' => $machinery->name,
                'equipment_serial' => $machinery->equipment_serial,
                'economic_serial' => $machinery->economic_serial,
                'engine_serial' => $machinery->engine_serial,
                'description' => $machinery->description,
                'cost_price' => $machinery->cost_price,
                'acquisition_date' => $machinery->acquisition_date,
                'deleted_at' => $machinery->deleted_at,
                'expenses' => MachineryExpense::with('expense')
                    ->where('machinery_id', $machinery->id)->get()
                    ->map(function ($item) {
                        return [
                            "id" => $item->id,
                            "expense" => [
                                "id" => $item->expense->id,
                                "name" => $item->expense->name
                            ],
                            "expense_id" => $item->expense_id,
                            'reference' => $item->reference,
                            'folio' => $item->folio,
                            'amount' => $item->amount,
                            'applied_date' => $item->applied_date,
                        ];
                    }),
                'serivces_expenses' => $machinery->servicesExpenses->map
                    ->only(
                        'id',
                        'reference',
                        'name',
                        'description',
                        'amount',
                        'applied_date',
                    ),
            ],
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Machinery  $machinery
     *
     */
    public function edit(Machinery $machinery)
    {
        return Inertia::render('Machinery/Edit', [
            'formOptions' => $this->getOptionsForm(),
            'item' => [
                'id' => $machinery->id,
                'category_id' => $machinery->category_id,
                'name' => $machinery->name,
                'equipment_serial' => $machinery->equipment_serial,
                'economic_serial' => $machinery->economic_serial,
                'engine_serial' => $machinery->engine_serial,
                'description' => $machinery->description,
                'cost_price' => $machinery->cost_price,
                'acquisition_date' => $machinery->acquisition_date,
                'deleted_at' => $machinery->deleted_at,
            ],
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Machinery  $machinery
     * 
     */
    public function update(Request $request, Machinery $machinery)
    {
        return DB::transaction(function () use ($machinery) {

            $machinery->update(
                Request::validate([
                    'category_id' => ['required'],
                    'name' => ['required'],
                    'equipment_serial' => [
                        'required',
                        'max:50', Rule::unique('machineries')->ignore($machinery->id)
                    ],
                    'engine_serial' => [
                        'required',
                        'max:50', Rule::unique('machineries')->ignore($machinery->id)
                    ],
                    'economic_serial' => [
                        'required',
                        'max:50',
                    ],
                    'description' => ['nullable'],
                    'cost_price' => ['required'],
                    'acquisition_date' => ['nullable'],
                ])
            );

            if (Request::has('fixes_costs')) {
                // return dd(
                //     $machinery,
                //     Request::validate(['fixes_costs' => ['array']])['fixes_costs']
                // );
            }

            return Redirect::back()->with('success', 'Maquinaria Actualizada con Exito ');
        });
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Machinery  $machinery
     * 
     */
    public function destroy(Machinery $machinery)
    {
        $machinery->delete();

        return Redirect::back()->with('success', 'Maquinaria Eliminada.');
    }

    /**
     * Restore the specified resource from storage.
     *
     * @param  \App\Models\Machinery  $machinery
     * 
     */
    public function restore(Machinery $machinery)
    {
        $machinery->restore();

        return Redirect::back()->with('success', 'Maquinaria restored.');
    }

    public function getOptionsForm()
    {
        return [
            'categories' => Category::all('id', 'name')->map(function ($item) {
                return ['value' => $item->id, 'text' => $item->name];
            }),
            // 'fixesCosts' => $this->getAllFixesCosts(),
            // 'expensesCatalogs' => ExpenseCatalog::get()->map(function ($expense) {
            //     return [
            //         'value' => $expense->id,
            //         'text' => $expense->name,
            //     ];
            // }),
            // 'servicesExpenses' => [],
            // 'expenses' => []
        ];
    }

    public function getAllFixesCosts()
    {
        return FixesCosts::get()->map(function ($item) {
            return [
                'id' => $item->id,
                'name' => $item->name,
                'amount' => 0,
            ];
        });
    }

    public function syncFixesCosts(Machinery $machinery, array $fixesCost): void
    {
        $syncArray = array_map(function ($item) {
            return ['amount' => $item];
        }, Arr::pluck($fixesCost, 'amount', 'id'));
        $machinery->fixesCosts()->sync($syncArray);
    }
    public function createOrUpdateMachineryExpense(Machinery $machinery, array $payload = []): void
    {
        if (!empty($payload)) {
            $result = array_map(function ($item) use ($machinery) {
                return [
                    'id' => $item['id'],
                    'machinery_id' => $machinery->id,
                    'expense_id' => $item['expense_id'],
                    'reference' => $item['reference'],
                    'folio' => $item['folio'],
                    'amount' => $item['amount'],
                    'applied_date' => $item['applied_date'],
                ];
            }, $payload);

            foreach ($result as $item) {
                $machineryExpense = MachineryExpense::find($item['id']);
                if (is_null($machineryExpense)) {
                    MachineryExpense::create($item);
                } else {
                    $machineryExpense->update($item);
                }
            }
        }
    }
    public function attachServicesExpenses(Machinery $machinery, array $payload = []): void
    {
        if (!empty($payload)) {
            $machinery->servicesExpenses()->createMany($payload);
        }
    }
}