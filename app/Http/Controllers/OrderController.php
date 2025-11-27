<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItems;
use App\Models\Garment;
use App\Models\Fabric;
use App\Models\Customer;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        if ($request->wantsJson()) {
            // API response
            $orders = Order::with(['customer', 'items'])->paginate(15);
            return response()->json($orders);
        }

        // Web response
        return view('dashboard.orders.index');
    }

    public function create(Request $request)
    {
        if ($request->wantsJson()) {
            // API response - return necessary data
            $garments = Garment::all();
            $fabrics = Fabric::all();
            $customers = Customer::all();
            return response()->json([
                'garments' => $garments,
                'fabrics' => $fabrics,
                'customers' => $customers
            ]);
        }

        // Web response
        $garments = Garment::all();
        $fabrics = Fabric::all();
        return view('dashboard.orders.create', compact('garments', 'fabrics'));
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'customer_id' => 'required|exists:customers,id',
                'order_date' => 'required|date',
                'advance_paid' => 'nullable|numeric|min:0',
                'remarks' => 'nullable|string',
                'items' => 'required|array|min:1',
                'items.*.product_name' => 'required|string|max:255',
                'items.*.garment_type' => 'required|string|max:255',
                'items.*.fabric_type' => 'nullable|string|max:255',
                'items.*.color' => 'nullable|string|max:255',
                'items.*.quantity' => 'required|integer|min:1',
                'items.*.unit_price' => 'required|numeric|min:0',
            ]);

            DB::beginTransaction();

            // Calculate total amount
            $totalAmount = collect($validated['items'])->sum(function ($item) {
                return $item['quantity'] * $item['unit_price'];
            });

            $order = Order::create([
                'customer_id' => $validated['customer_id'],
                'order_date' => $validated['order_date'],
                'advance_paid' => $validated['advance_paid'] ?? 0,
                'total_amount' => $totalAmount,
                'pending_amount' => $totalAmount - ($validated['advance_paid'] ?? 0),
                'remarks' => $validated['remarks'] ?? null,
            ]);

            // Create order items
            foreach ($validated['items'] as $item) {
                $order->items()->create($item);
            }

            DB::commit();

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Order created successfully',
                    'order' => $order->load('items', 'customer')
                ], 201);
            }

            return redirect()->route('dashboard.orders.index')->with('success', 'Order created successfully.');

        } catch (ValidationException $e) {
            DB::rollBack();
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $e->errors()
                ], 422);
            }
            throw $e;
        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred: ' . $e->getMessage()
                ], 500);
            }
            throw $e;
        }
    }

    public function show(Request $request, $id)
    {
        $order = Order::with(['customer', 'items'])->findOrFail($id);

        if ($request->wantsJson()) {
            return response()->json($order);
        }

        return view('dashboard.orders.show', compact('order'));
    }

    public function update(Request $request, $id)
    {
        try {
            $order = Order::findOrFail($id);

            $validated = $request->validate([
                'customer_id' => 'required|exists:customers,id',
                'order_date' => 'required|date',
                'advance_paid' => 'nullable|numeric|min:0',
                'remarks' => 'nullable|string',
                'items' => 'required|array|min:1',
                'items.*.product_name' => 'required|string|max:255',
                'items.*.garment_type' => 'required|string|max:255',
                'items.*.fabric_type' => 'nullable|string|max:255',
                'items.*.color' => 'nullable|string|max:255',
                'items.*.quantity' => 'required|integer|min:1',
                'items.*.unit_price' => 'required|numeric|min:0',
            ]);

            DB::beginTransaction();

            // Calculate total amount
            $totalAmount = collect($validated['items'])->sum(function ($item) {
                return $item['quantity'] * $item['unit_price'];
            });

            $order->update([
                'customer_id' => $validated['customer_id'],
                'order_date' => $validated['order_date'],
                'advance_paid' => $validated['advance_paid'] ?? 0,
                'total_amount' => $totalAmount,
                'pending_amount' => $totalAmount - ($validated['advance_paid'] ?? 0),
                'remarks' => $validated['remarks'] ?? null,
            ]);

            // Remove existing items and create new ones
            $order->items()->delete();
            foreach ($validated['items'] as $item) {
                $order->items()->create($item);
            }

            DB::commit();

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Order updated successfully',
                    'order' => $order->load('items', 'customer')
                ]);
            }

            return redirect()->route('dashboard.orders.index')->with('success', 'Order updated successfully.');

        } catch (ValidationException $e) {
            DB::rollBack();
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $e->errors()
                ], 422);
            }
            throw $e;
        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred: ' . $e->getMessage()
                ], 500);
            }
            throw $e;
        }
    }

    public function destroy(Request $request, $id)
    {
        try {
            $order = Order::findOrFail($id);
            $order->delete();

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Order deleted successfully'
                ]);
            }

            return redirect()->route('dashboard.orders.index')->with('success', 'Order deleted successfully.');

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
}
