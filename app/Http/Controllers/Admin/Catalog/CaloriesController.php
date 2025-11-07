<?php

namespace App\Http\Controllers\Admin\Catalog;

use Illuminate\Http\Request;
use App\Models\Catalog\Calorie;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Requests\Catalog\CalorieStoreRequest;
use App\Http\Requests\Catalog\CalorieUpdateRequest;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class CaloriesController extends Controller
{
    use AuthorizesRequests;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny', Calorie::class);

        if (request()->ajax()) {

            $query = Calorie::query();

            return datatables()->of($query)
                ->addIndexColumn()
                ->editColumn('status', function ($row) {

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
                    $compact['editUrl'] = route('admin.catalog.calories.edit', $row->id);
                    // $dara['deleteUrl'] = route('admin.catalog.calorie.destroy', $row->id);
                    // $compact['restoreUrl'] = route('admin.catalog.calorie.restore', $row->id);
                    $compact['editSidebar'] = true;

                    return view('theme.adminlte.components._table-actions', $compact)->render();
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        }

        return view('theme.adminlte.catalog.calories.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', Calorie::class);

        $data = [];
        $response['view'] = view('theme.adminlte.catalog.calories.create', $data)->render();

        return response()->json([
            'success' => true,
            'data' => $response
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CalorieStoreRequest $request)
    {
        $this->authorize('create', Calorie::class);

        $validated = $request->validated();

        DB::beginTransaction();
        try {

            if (!empty($validated['is_active'])) {
                $validated['is_active'] = true;
            } else {
                $validated['is_active'] = false;
            }

            Calorie::create($validated);


            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }

        return response()->json([
            'success' => true,
            'message' => __('crud.created', ['name' => 'Calorie']),
            'redirect' => route('admin.catalog.calories.index')
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
        $calorie = Calorie::findOrFail($id);

        $this->authorize('update', $calorie);

        $data['calorie'] = $calorie;

        $response['view'] =  view('theme.adminlte.catalog.calories.edit', $data)->render();

        return response()->json([
            'success'   => true,
            'data'      => $response
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CalorieUpdateRequest $request, string $id)
    {
        $calorie = Calorie::findOrFail($id);

        $this->authorize('update', $calorie);

        $validated = $request->validated();

        if (!empty($validated['is_active'])) {
            $validated['is_active'] = true;
        } else {
            $validated['is_active'] = false;
        }

        $calorie->update($validated);

        return response()->json([
            'success' => true,
            'message' => __('crud.updated', ['name' => 'Calorie']),
            'redirect' => route('admin.catalog.calories.index')
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
