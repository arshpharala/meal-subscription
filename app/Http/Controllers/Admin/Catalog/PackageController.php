<?php

namespace App\Http\Controllers\Admin\Catalog;

use Illuminate\Http\Request;
use App\Models\Catalog\Package;
use App\Http\Controllers\Controller;
use App\Http\Requests\Catalog\PackageStoreRequest;
use App\Http\Requests\Catalog\PackageUpdateRequest;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class PackageController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (request()->ajax()) {
            $packages = Package::query();

            return datatables()->of($packages)
                ->addIndexColumn()
                ->editColumn('thumbnail', function ($row) {
                    return $row->thumbnail
                        ? '<img src="' . asset('storage/' . $row->thumbnail) . '" class="img-sm">'
                        : '';
                })
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
                    $compact['editUrl'] = route('admin.catalog.packages.edit', $row->id);
                    // $compact['deleteUrl'] = route('admin.catalog.packages.destroy', $row->id);
                    // $compact['restoreUrl'] = route('admin.catalog.packages.restore', $row->id);
                    $compact['editSidebar'] = true;


                    return view('theme.adminlte.components._table-actions', $compact)->render();
                })
                ->rawColumns(['thumbnail', 'status', 'action'])
                ->make(true);
        }

        return view('theme.adminlte.catalog.packages.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $data = [];
        $response['view'] = view('theme.adminlte.catalog.packages.create', $data)->render();

        return response()->json([
            'success' => true,
            'data' => $response
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PackageStoreRequest $request)
    {
        $validated = $request->validated();

        if (!empty($validated['is_active'])) {
            $validated['is_active'] = true;
        } else {
            $validated['is_active'] = false;
        }

        if ($request->hasFile('thumbnail')) {
            $validated['thumbnail'] = $request->file('thumbnail')->store('packages', 'public');
        }

        Package::create($validated);

        return response()->json([
            'success' => true,
            'message' => __('crud.created', ['name' => 'Package']),
            'redirect' => route('admin.catalog.packages.index')
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
        $package = Package::findOrFail($id);

        $this->authorize('update', $package);

        $data['package'] = $package;

        $response['view'] =  view('theme.adminlte.catalog.packages.edit', $data)->render();

        return response()->json([
            'success'   => true,
            'data'      => $response
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PackageUpdateRequest $request, string $id)
    {
        $package = Package::findOrFail($id);

        $this->authorize('update', $package);

        $validated = $request->validated();

        if (!empty($validated['is_active'])) {
            $validated['is_active'] = true;
        } else {
            $validated['is_active'] = false;
        }

        if ($request->hasFile('thumbnail')) {
            $validated['thumbnail'] = $request->file('thumbnail')->store('packages', 'public');
        }

        $package->update($validated);

        return response()->json([
            'success' => true,
            'message' => __('crud.updated', ['name' => 'Package']),
            'redirect' => route('admin.catalog.packages.index')
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
