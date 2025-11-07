<?php

namespace App\Http\Controllers\Admin\Catalog;

use App\Models\Catalog\Meal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Requests\Catalog\MealStoreRequest;
use App\Http\Requests\Catalog\MealUpdateRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class MealController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny', Meal::class);

        if (request()->ajax()) {
            $meals = Meal::withJoins()
                ->withSelection();

            return datatables()->of($meals)
                ->addIndexColumn()
                ->addColumn('thumbnail', function ($row) {
                    return $row->thumbnail_file_path
                        ? '<img src="' . asset('storage/' . $row->thumbnail_file_path) . '" class="img-sm">'
                        : '';
                })
                ->addColumn('status', function ($row) {

                    if ($row->is_active) {
                        $status = '<span class="badge badge-success">Active</span>';
                    } else {
                        $status = '<span class="badge badge-secondary">Inactive</span>';
                    }

                    return $status;
                })
                ->editColumn('created_at', function ($row) {
                    return $row->created_at?->format('d-M-Y  h:m A');
                })
                ->addColumn('action', function ($row) {
                    $compact['row'] = $row;
                    $compact['editUrl'] = route('admin.catalog.meals.edit', $row->id);
                    // $dara['deleteUrl'] = route('admin.catalog.meals.destroy', $row->id);
                    // $compact['restoreUrl'] = route('admin.catalog.meals.restore', $row->id);


                    return view('theme.adminlte.components._table-actions', $compact)->render();
                })
                ->rawColumns(['thumbnail', 'status', 'action'])
                ->make(true);
        }

        return view('theme.adminlte.catalog.meals.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', Meal::class);

        $data = [];
        $response['view'] = view('theme.adminlte.catalog.meals.create', $data)->render();

        return response()->json([
            'success' => true,
            'data' => $response
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(MealStoreRequest $request)
    {
        $this->authorize('create', Meal::class);

        $validated = $request->validated();

        DB::beginTransaction();
        try {

            if (!empty($validated['is_active'])) {
                $validated['is_active'] = true;
            } else {
                $validated['is_active'] = false;
            }

            if ($request->hasFile('sample_menu_file')) {
                $validated['sample_menu_file'] = $request->file('sample_menu_file')->store('meals', 'public');
            }

            $meal = Meal::create($validated);

            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $meal->attachments()->create([
                        'file_path' => $file->store('attachments', 'public'),
                        'file_type' => $file->getMimeType(),
                        'file_name' => $file->getClientOriginalName(),
                    ]);
                }
            }
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }

        return response()->json([
            'success' => true,
            'message' => __('crud.created', ['name' => 'Meal']),
            'redirect' => route('admin.catalog.meals.index')
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $meal = Meal::findOrFail($id);

        $this->authorize('update', $meal);

        $data['meal'] = $meal;

        return view('theme.adminlte.catalog.meals.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(MealUpdateRequest $request, string $id)
    {
        $meal = Meal::findOrFail($id);

        $this->authorize('update', $meal);

        $validated = $request->validated();

        DB::beginTransaction();
        try {
            if (!empty($validated['is_active'])) {
                $validated['is_active'] = true;
            } else {
                $validated['is_active'] = false;
            }

            if ($request->hasFile('sample_menu_file')) {
                if ($meal->sample_menu_file && Storage::exists($meal->sample_menu_file)) {
                    Storage::disk('public')->delete($meal->sample_menu_file);
                }
                $validated['sample_menu_file'] = $request->file('sample_menu_file')->store('meals', 'public');
            }

            $meal->update($validated);

            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $meal->attachments()->create([
                        'file_path' => $file->store('attachments', 'public'),
                        'file_type' => $file->getMimeType(),
                        'file_name' => $file->getClientOriginalName(),
                    ]);
                }
            }

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }

        return response()->json([
            'success' => true,
            'message' => __('crud.updated', ['name' => 'Meal']),
            'redirect' => route('admin.catalog.meals.edit', ['meal' => $meal])
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
