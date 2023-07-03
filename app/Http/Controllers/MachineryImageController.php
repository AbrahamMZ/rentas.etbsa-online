<?php

namespace App\Http\Controllers;

use App\Models\Machinery;
use App\Models\MachineryImage;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Request;

class MachineryImageController extends Controller
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
        Request::validate([
            'machinery_id' => ['required'],
            'images' => ['array']
        ]);
        DB::beginTransaction();
        try {
            $machinery = Machinery::find(Request::get('machinery_id'));
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
        } catch (Exception $e) {
            DB::rollback();
            dd($e);
            return Redirect::back()->with('error', 'ERROR Servidor para subir Imagenes.');
        }
        DB::commit();

        return Redirect::back()->with('success', 'Imagenes Cargadas');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\MachineryImage  $machineryImage
     * @return \Illuminate\Http\Response
     */
    public function show(MachineryImage $machineryImage)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\MachineryImage  $machineryImage
     * @return \Illuminate\Http\Response
     */
    public function edit(MachineryImage $machineryImage)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\MachineryImage  $machineryImage
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, MachineryImage $machineryImage)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\MachineryImage  $machineryImage
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(MachineryImage $machineryImage)
    {

        DB::beginTransaction();
        try {
            $file_path = $machineryImage->full;
            if (Storage::exists($file_path))
                Storage::delete($file_path);
            $machineryImage->delete();
        } catch (Exception $e) {
            DB::rollback();
            return Redirect::back()->with('error', 'ERROR Servidor para el Documento.');
        }
        DB::commit();

        return Redirect::back()->with('success', 'Imagen Eliminada');
    }
}