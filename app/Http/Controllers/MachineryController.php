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
                        'no_serie' => $machinery->no_serie,
                        'price' => $machinery->price,
                        'category' => $machinery->category->name,
                        'deleted_at' => $machinery->deleted_at,
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
                        'category_id' => ['required', 'max:100'],
                        'no_serie' => ['required', 'max:100', Rule::unique('machineries')],
                        'model' => ['required'],
                        'price' => ['required'],
                        'acquisition_date' => ['nullable'],
                        'description' => ['nullable'],

                    ])
                ),
                function (Machinery $machinery) {
                    // $this->syncFixesCosts(
                    //     $machinery,
                    //     Request::validate(['fixes_costs' => ['array']])['fixes_costs']
                    // );
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
                'category_id' => $machinery->category_id,
                'category' => $machinery->category->name,
                'no_serie' => $machinery->no_serie,
                'model' => $machinery->model,
                'description' => $machinery->description,
                'price' => $machinery->price,
                'acquisition_date' => $machinery->acquisition_date,
                'fixes_costs' => collect($this->getAllFixesCosts())->merge(
                    $machinery->fixesCosts->map(function ($item) {
                        return [
                            'id' => $item->id,
                            'name' => $item->name,
                            'amount' => $item->pivot->amount,
                        ];
                    })
                )->keyBy('id')->values()->all(),
                // 'expenses' => $machinery->expenses,
                'expenses' => MachineryExpense::with('expense')->where('machinery_id', $machinery->id)->get()
                    ->map(function ($item) {
                        return [
                            "id" => $item->id,
                            "expense" => [
                                "id" => $item->expense->id
                                ,
                                "name" => $item->expense->name
                            ],
                            "expense_id" => $item->expense_id,
                            "name" => $item->name,
                            "reference" => $item->reference,
                            "amount" => $item->amount,
                            "charge_date" => $item->charge_date
                        ];
                    }),
                // ->get()->map->only('id', 'name', 'amount'),
                'serivces_expenses' => $machinery->servicesExpenses,
                'deleted_at' => $machinery->deleted_at,
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
                'no_serie' => $machinery->no_serie,
                'model' => $machinery->model,
                'description' => $machinery->description,
                'price' => $machinery->price,
                'acquisition_date' => $machinery->acquisition_date,
                'fixes_costs' => $machinery->fixesCosts->only('id', 'name', 'pivot.amount'),
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
                    'category_id' => ['required', 'max:100'],
                    'no_serie' => ['required', 'max:100', Rule::unique('machineries')->ignore($machinery->id)],
                    'model' => ['required'],
                    'price' => ['required'],
                    'acquisition_date' => ['nullable'],
                    'description' => ['nullable'],
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
            'expensesCatalogs' => ExpenseCatalog::get()->map(function ($expense) {
                return [
                    'value' => $expense->id,
                    'text' => $expense->name,
                ];
            }),
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
                    'name' => $item['name'],
                    'reference' => $item['reference'],
                    'amount' => $item['amount'],
                    'charge_date' => $item['charge_date'],
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