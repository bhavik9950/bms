<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Fabric;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;
class FabricController extends Controller
{
    public function index(Request $request)
    {
        if ($request->wantsJson()) {
            $fabrics = Fabric::paginate(15);
            return response()->json($fabrics);
        }

        // Existing web code
        $fabrics = Fabric::all();
        return view('dashboard.masters.fabrics', compact('fabrics'));
    }

    public function create(Request $request)
    {
        if ($request->wantsJson()) {
            return response()->json(['message' => 'Use POST /fabrics to create fabric']);
        }

        // Web response
        return view('dashboard.masters.fabrics');
    }

    public function createFabric(Request $request)
    {
        // Validate and create a new fabric entry
        $validated = $request->validate([
            'fabric' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $fabric = Fabric::create($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Fabric created successfully',
                'fabric' => $fabric
            ], 201);
        }

        return redirect()->route('dashboard.masters.fabrics')
                         ->with('success', 'Fabric created successfully.');
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'fabric' => 'required|string|max:255',
                'description' => 'nullable|string',
            ]);

            $fabric = Fabric::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Fabric created successfully!',
                'fabric'  => $fabric,
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors'  => $e->errors(),
            ], 422);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'fabric' => 'required|string|max:255',
                'description' => 'nullable|string',
            ]);

            $fabric = Fabric::findOrFail($id);
            $fabric->update($validated);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Fabric updated successfully',
                    'fabric' => $fabric
                ]);
            }

            return redirect()->route('dashboard.masters.fabrics')
                             ->with('success', 'Fabric updated successfully.');

        } catch (ValidationException $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => $e->errors()
                ], 422);
            }
            throw $e;
        }
    }

    public function updateFabric(Request $request, $id)
    {
        $request->validate([
            'fabric' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $fabric = Fabric::findOrFail($id);
        $fabric->update([
            'fabric' => $request->fabric,
            'description' => $request->description,
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Fabric updated successfully',
                'fabric' => $fabric
            ]);
        }

        return redirect()->route('dashboard.masters.fabrics')
                         ->with('success', 'Fabric updated successfully.');
    }

    public function show(Request $request, $id)
    {
        $fabric = Fabric::findOrFail($id);

        if ($request->wantsJson()) {
            return response()->json($fabric);
        }

        return view('dashboard.masters.fabric-detail', compact('fabric'));
    }

    public function destroy(Request $request, $id)
    {
        try {
            $fabric = Fabric::findOrFail($id);
            $fabric->delete();

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Fabric deleted successfully'
                ]);
            }

            return redirect()->route('dashboard.masters.fabrics')
                             ->with('success', 'Fabric deleted successfully.');

        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred: ' . $e->getMessage()
                ], 500);
            }
            throw $e;
        }
    }

    public function destroyFabric($id)
    {
        $fabric = Fabric::findOrFail($id);
        $fabric->delete();

        return response()->json([
            'success' => true,
            'message' => 'Fabric deleted successfully.'
        ]);
    }


    public function importFabrics(Request $request)
    {
                $request->validate([
            'file' => 'required|file|mimes:xlsx,csv,xls',
        ]);
$file=$request->file('file');
     $rows= Excel::toArray([],$file)[0]; // Get first sheet data
    foreach($rows as $index=>$row){
        if($index === 0) continue; // Skip header row
          Fabric::firstOrCreate(
            ['fabric' => $row[0]], // assuming first column = fabric
            ['description' => $row[1] ?? null] // second column = description
        );
    }

    return response()->json(['success' => count($rows)-1]); // minus header row

    }
}
