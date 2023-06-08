<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    public function version(Request $request)
    {
        return parent::version($request);
    }

    /**
     * Defines the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function share(Request $request)
    {
        return array_merge(parent::share($request), [
            'auth' => function () use ($request) {
                return [
                    'user' => $request->user() ? [
                        'id' => $request->user()->id,
                        'name' => $request->user()->name,
                        'first_name' => $request->user()->first_name,
                        'last_name' => $request->user()->last_name,
                        'email' => $request->user()->email,
                        'role' => $request->user()->role,
                        'photo' => $request->user()->photoUrl(['w' => 40, 'h' => 40, 'fit' => 'crop']),
                        'admin' => $request->user()->owner,
                        // 'permissions' => $request->user()->permissions,
                        'account' => [
                            'id' => $request->user()->account->id,
                            'name' => $request->user()->account->name,
                        ],
                    ] : null,
                ];
            },
            'navigation' => [
                'menu' => [
                    ['name' => 'Expedientes', 'route' => 'expedients', "icon" => 'mdi-folder', 'enabled' => true],
                    ['name' => 'Plantillas', 'route' => 'templates', "icon" => 'mdi-content-copy', 'enabled' => $request->user()->owner ?? false],
                    ['name' => 'Requisitos', 'route' => 'requirements', "icon" => 'mdi-alert-octagon', 'enabled' => $request->user()->owner ?? false],
                    ['name' => 'Maquinarias', 'route' => 'machineries', "icon" => 'mdi-tractor', 'enabled' => $request->user()->owner ?? false],
                    [
                        'name' => 'Admin',
                        "icon" => 'mdi-view-headline',
                        'enabled' => $request->user()->owner ?? false,
                        'children' => [
                            ['name' => 'Catalogo Gastos', 'route' => 'expenses', "icon" => 'mdi-alert-octagon', 'enabled' => $request->user()->owner ?? false],
                            ['name' => 'Categorias', 'route' => 'categories', "icon" => 'mdi-shape-plus', 'enabled' => $request->user()->owner ?? false],
                            ['name' => 'Usuarios', 'route' => 'users', "icon" => 'mdi-account-box-multiple', 'enabled' => $request->user()->owner ?? false],
                        ]
                    ],
                ]
            ],
            'flash' => function () use ($request) {
                return [
                    'success' => $request->session()->get('success'),
                    'error' => $request->session()->get('error'),
                ];
            },
        ]);
    }
}