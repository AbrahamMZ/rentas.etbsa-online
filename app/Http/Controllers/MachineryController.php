<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\FixesCosts;
use App\Models\LeaseIncomes;
use App\Models\Machinery;
use App\Models\MachineryMonthlyExpenses;
use App\Pivots\MachineryExpense;
use Carbon\Carbon;
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

        $year = Request::get('year') ?? Carbon::now()->year;
        return Inertia::render('Machinery/Index', [
            'filters' => Request::all(['search', 'trashed', 'page', 'per_page', 'category_ids', 'year']),
            'optionsFilters' => $this->getOptionsForm(),
            'items' => Machinery::orderByName()
                ->filter(Request::only(['search', 'trashed', 'category_ids', 'folio']))
                ->paginate(Request::get('per_page') == -1 ? 999999 : Request::get('per_page') ?? 10)
                ->transform(function ($machinery) {
                    return [
                        'id' => $machinery->id,
                        'category' => $machinery->category_name,
                        'name' => $machinery->name,
                        'equipment_serial' => $machinery->equipment_serial,
                        'economic_serial' => $machinery->economic_serial,
                        'engine_serial' => $machinery->engine_serial,
                        'description' => $machinery->description,
                        'cost_price' => $machinery->cost_price,
                        'acquisition_date' => $machinery->acquisition_date,
                        'deleted_at' => $machinery->deleted_at,
                        'total_expenses_amount' => $machinery->total_expenses_amount,
                        'total_monthly_expenses_amount' => $machinery->total_monthly_expenses_amount,
                        'total_service_expenses_amount' => $machinery->total_service_expenses_amount,
                        'total_cost_equipment' => $machinery->total_cost_equipment,
                        'months_used' => $machinery->months_used,
                        'images' => $machinery->images,
                        'current_sale_price' => $machinery->current_sale_price,
                        'ocupancy_rate' => $machinery->ocupancy_rate,
                    ];
                }),
            'calendar' => Machinery::with([
                'leaseIncomes' => function ($query) use ($year) {
                    $query->whereRaw('YEAR(start_date) <= ?', $year);
                }
                ,
                'leaseIncomes.leaseFees'
            ])->filter(Request::only(['search', 'trashed', 'category_ids', 'folio']))
                ->select('id', 'name', 'equipment_serial', 'description')
                ->selectSub(function ($query) use ($year) {
                    $query->selectRaw('SUM(balance)')
                        ->from('lease_incomes')
                        ->whereColumn('machineries.id', 'lease_incomes.machinery_id')
                        ->whereRaw('YEAR(lease_incomes.start_date) <= ?', $year);
                }, 'total_balance')
                ->selectSub(function ($query) use ($year) {
                    // $query->selectRaw('amount * 12')
                    $query->selectRaw('SUM(total_income)')
                        ->from('lease_incomes')
                        ->whereColumn('machineries.id', 'lease_incomes.machinery_id')
                        ->whereRaw('YEAR(lease_incomes.start_date) <= ?', $year);
                }, 'total_income')
                ->selectSub(function ($query) use ($year) {
                    $query->selectRaw('SUM(term_in_days)')
                        ->from('lease_incomes')
                        ->whereColumn('machineries.id', 'lease_incomes.machinery_id')
                        ->whereRaw('YEAR(lease_incomes.start_date) <= ?', $year);
                }, 'total_days')
                ->groupBy('id', 'name', 'equipment_serial', 'description')
                ->orderByName()
                ->get()
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
                        'engine_serial' => ['nullable', 'max:50', Rule::unique('machineries')],
                        'description' => ['nullable'],
                        'cost_price' => ['required'],
                        'value_price' => ['nullable'],
                        'invoice' => ['nullable'],
                        'percent_depreciation' => ['required'],
                        'acquisition_date' => ['required'],
                        'warranty_date' => ['nullable'],
                        'images' => ['array', 'nullable'],
                    ])
                ),
                function (Machinery $machinery) {
                    if ($images = Request::file('images') ?? []) {
                        foreach ($images as $image) {
                            $machinery->images()->create([
                                'full' => $image
                                ? $machinery->uploadOne(
                                    $image,
                                    $machinery->getFolderPath(),
                                    's3', $image->getClientOriginalName()
                                )
                                : null
                            ]);
                        }
                    }
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
                'current_sale_price' => $machinery->current_sale_price,
                'id' => $machinery->id,
                'category' => $machinery->category->name,
                'name' => $machinery->name,
                'equipment_serial' => $machinery->equipment_serial,
                'economic_serial' => $machinery->economic_serial,
                'engine_serial' => $machinery->engine_serial,
                'description' => $machinery->description,
                'cost_price' => $machinery->cost_price,
                'value_price' => $machinery->value_price,
                'total_cost_amount' => $machinery->total_cost_equipment,
                'invoice' => $machinery->invoice,
                'hours_work' => $machinery->hours_work,
                'acquisition_date' => $machinery->acquisition_date,
                'deleted_at' => $machinery->deleted_at,
                'months_used' => $machinery->months_used,
                'monthly_depreciation' => $machinery->monthly_depreciation,
                'percent_depreciation' => $machinery->percent_depreciation,
                'images' => $machinery->images,
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
                'monthly_expenses' => MachineryMonthlyExpenses::with('expenseType')
                    ->where('machinery_id', $machinery->id)->get()
                    ->map(function ($item) {
                        return [
                            "id" => $item->id,
                            "expense" => [
                                "id" => $item->expenseType->id,
                                "name" => $item->expenseType->name
                            ],
                            "monthly_expense_types_id" => $item->monthly_expense_types_id,
                            'description' => $item->description,
                            'base_cost_type' => $item->base_cost_type,
                            'base_cost_amount' => $item->base_cost_amount,
                            'percent' => $item->percent,
                            'amount' => $item->amount,
                        ];
                    }),
                'services_expenses' => $machinery->servicesExpenses->map
                    ->only(
                        'id',
                        'reference',
                        'name',
                        'description',
                        'amount',
                        'applied_date',
                    ),
                'leases_incomes' => $machinery->leaseIncomes->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'machinery_id' => $item->machinery_id,
                        'contract_lease' => $item->contract_lease,
                        'reference' => $item->reference,
                        'term_lease' => $item->term_lease,
                        'daily_fee' => $item->daily_fee,
                        'term_in_days' => $item->term_in_days,
                        'amount' => $item->amount,
                        'balance' => $item->balance,
                        'start_date' => $item->start_date,
                        'end_date' => $item->end_date,
                        'total_income' => $item->total_income,
                        'lease_fees' => $item->leaseFees,
                        'customer' => [
                            'avatar' => null,
                            'name' => $item->reference,
                            'email' => "mail@example.com",
                        ],
                    ];
                }),
                'occupancy_rate' => $machinery->ocupancy_rate,
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
                'value_price' => $machinery->value_price,
                'invoice' => $machinery->invoice,
                'hours_work' => $machinery->hours_work,
                'percent_depreciation' => $machinery->percent_depreciation * 100,
                'acquisition_date' => $machinery->acquisition_date,
                'warranty_date' => $machinery->warranty_date,
                'images' => $machinery->images,
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
                        'nullable',
                        'max:50', Rule::unique('machineries')->ignore($machinery->id)
                    ],
                    'economic_serial' => [
                        'required',
                        'max:50',
                    ],
                    'description' => ['nullable'],
                    'cost_price' => ['required'],
                    'value_price' => ['required'],
                    'invoice' => ['nullable'],
                    'hours_work' => ['nullable'],
                    'percent_depreciation' => ['required'],
                    'acquisition_date' => ['nullable'],
                    'warranty_date' => ['nullable'],
                    'images' => ['array', 'nullable'],
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
            'categories' => Category::all('id', 'name', 'parent_id')
            // 'treeCategories' => Category::getTreeCategories()
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

            MachineryExpense::upsert(
                $result,
                ['id', 'machinery_id', 'expense_id'],
                ['folio', 'amount', 'applied_date', 'reference']
            );
        }
    }
    public function attachServicesExpenses(Machinery $machinery, array $payload = []): void
    {
        if (!empty($payload)) {
            $machinery->servicesExpenses()->createMany($payload);
        }
    }
    public function attachLeaseIncome(Machinery $machinery, array $payload = []): void
    {
        if (!empty($payload)) {
            $machinery->leaseIncomes()->createMany($payload);
        }
    }
}
