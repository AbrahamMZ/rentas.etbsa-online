<?php

namespace App\Providers;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\UrlWindow;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;
use League\Glide\Server;

class AppServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {

        \Schema::defaultStringLength(191);

    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerGlide();
        $this->registerLengthAwarePaginator();
    }

    protected function registerGlide()
    {
        $this->app->bind(Server::class, function ($app) {
            return Server::create([
                'source' => Storage::getDriver(),
                'cache' => Storage::getDriver(),
                'cache_folder' => '.glide-cache',
                'base_url' => 'img',
            ]);
        });
    }

    protected function registerLengthAwarePaginator()
    {
        $this->app->bind(LengthAwarePaginator::class, function ($app, $values) {
            return new class (...array_values($values)) extends LengthAwarePaginator {
                public function only(...$attributes)
                {
                    return $this->transform(function ($item) use ($attributes) {
                        return $item->only($attributes);
                    });
                }

                public function transform($callback)
                {
                    $this->items->transform($callback);

                    return $this;
                }

                public function toArray()
                {
                    // return [
                    //     'data' => $this->items->toArray(),
                    //     'links' => $this->links(),
                    // ];
                    return [
                        'current_page' => $this->currentPage(),
                        'data' => $this->items->toArray(),
                        'first_page_url' => $this->url(1),
                        'from' => $this->firstItem(),
                        'last_page' => $this->lastPage(),
                        'last_page_url' => $this->url($this->lastPage()),
                        // 'links' => $this->linkCollection()->toArray(),
                        'links' =>  $this->links(),
                        'next_page_url' => $this->nextPageUrl(),
                        'path' => $this->path(),
                        'per_page' => $this->perPage(),
                        'prev_page_url' => $this->previousPageUrl(),
                        'to' => $this->lastItem(),
                        'total' => $this->total(),

                    ];


                }

                public function links($view = null, $data = [])
                {
                    $this->appends(Request::all());

                    $window = UrlWindow::make($this);

                    $elements = array_filter([
                        $window['first'],
                        is_array($window['slider']) ? '...' : null,
                        $window['slider'],
                        is_array($window['last']) ? '...' : null,
                        $window['last'],
                    ]);
                    return Collection::make($elements)->flatMap(function ($item) {
                        if (is_array($item)) {
                            return Collection::make($item)->map(function ($url, $page) {
                                return [
                                    'url' => $url,
                                    'label' => $page,
                                    'active' => $this->currentPage() === $page,
                                    'total' => $this->total,
                                    'perPage' => $this->perPage,
                                ];
                            });
                        } else {
                            return [
                                [
                                    'url' => null,
                                    'label' => '...',
                                    'active' => false,
                                ],
                            ];
                        }
                    });
                    // ->prepend([
                    //     'url' => $this->previousPageUrl(),
                    //     'label' => 'Previous',
                    //     'active' => false,
                    // ])->push([
                    //     'url' => $this->nextPageUrl(),
                    //     'label' => 'Next',
                    //     'active' => false,
                    // ]);
                }
            };
        });
    }
}