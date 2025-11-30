@extends('layouts.app')
@section('title', 'Salary History')
@section('content')

<div class="container mx-auto py-2 px-2">
    <div class="flex items-center justify-between mb-6">
        {{-- Header Title --}}
        <div>
            <h1 class="text-2xl font-semibold">Salary Payment History</h1>
            <h5 class="text-xs text-gray-500">View payment history for staff member</h5>
        </div>

        <a href="{{ route('dashboard.staff.salary') }}"
            class="bg-red-400 hover:bg-red-600 text-white px-4 py-2 rounded-md">
            <i class="ti ti-arrow-left mr-2"></i>
            Back to Salary
        </a>
    </div>

    {{-- Staff Info --}}
    <div class="bg-gradient-to-r from-green-100 to-green-50 p-4 rounded-lg border border-gray-200 shadow mb-6">
        <div class="grid grid-cols-1 md:grid-cols-6 gap-2  ">
            <div>
                 <img class="w-10 h-10 p-1 rounded-full ring-1 ring-gray-300 dark:ring-gray-500"
       src="{{ $salary->staff->profile_picture ? asset('storage/' . $salary->staff->profile_picture) : "https://avatar.iran.liara.run/public" }}" alt="Bordered avatar">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-600">Name</label>
                <p class="text-lg">{{ $staff->full_name }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-600">Role</label>
                <p class="text-lg">{{ $staff->role->role }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-600">Base Salary</label>
                <p class="text-lg">₹{{ number_format($salary->base_salary) }}</p>
            </div>
             <div>
                 <label class="block text-sm font-medium text-gray-600">Joining Date</label>
                 <p class="text-lg">{{ $staff->joining_date ? $staff->joining_date->format('d-m-Y') : 'N/A' }}</p>
             </div>
             <div>
                <button class="text-white btn bg-green-500 hover:bg-green-600 rounded-lg px-3 py-1 border-0"id="pay-button-{{ $salary->staff->id }}"  onclick="document.getElementById('my_modal_1').showModal();">
            <i class="ti ti-cash"></i> Pay Now
        </button>
            </div>
        </div>
    </div>


     <div>
            <h3 class="text-2xl font-semibold">Monthly Salary  History</h1>
         
        </div>
    {{-- Payment History Table --}}
    <div class="bg-white shadow-md sm:rounded-lg p-2">
        <table class="table bg-white table-bordered w-full">
            <thead class="text-xs text-gray-700 uppercase bg-gray-800 text-white">
                <tr class="text-center">
                          <th class="px-6 py-3">Month</th>
                    <th class="px-6 py-3">Days Worked/Total Days</th>
                    <th class="px-6 py-3">Bonuses</th>
                    <th class="px-6 py-3">Deduction</th>
                     <th class="px-6 py-3">Final Salary</th>
                    <th class="px-6 py-3">Amount Paid</th>
                    <th class="px-6 py-3">Balance</th>
                    <th class="px-6 py-3">Detail</th>
                </tr>
            </thead>
            <tbody>
                {{-- @foreach($payments as $payment)
                <tr class="bg-white border-b text-center">
                    <td class="px-6 py-4">{{ $payment->payment_date->format('d-m-Y') }}</td>
                    <td class="px-6 py-4">₹{{ number_format($payment->amount) }}</td>
                    <td class="px-6 py-4">{{ ucfirst($payment->payment_method) }}</td>
                    <td class="px-6 py-4">{{ $payment->notes ?? 'N/A' }}</td>
                </tr>
                @endforeach --}}
                <tr class="bg-white border-b text-center">
                    <td colspan="4" class="px-6 py-4 text-gray-500">No payment history available</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>


{{-- Add Payment Modal --}}
<dialog id="my_modal_1" class="modal">
    <div class="modal-box bg-white text-gray-800 rounded-2xl shadow-xl w-full max-w-xl p-6 relative">

        {{-- Close Button --}}
        <button type="button"
            onclick="document.getElementById('my_modal_1').close()"
            class="btn btn-sm btn-circle btn-ghost absolute right-3 top-3">✕</button>

        {{-- Title --}}
        <h3 class="text-2xl font-semibold mb-6">Add Payment</h3>

        <form id="payForm" class="space-y-6">
            @csrf
            <input type="hidden" id="staff-id" name="id">

            {{-- Total Paid + Balance --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Total Paid</label>
                    <input type="text" id="total_paid" name="total_paid" placeholder="0"
                        class="w-full input  input-bordered rounded-xl focus:ring-2 focus:ring-green-400 text-gray-700  bg-white" readonly >
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Balance</label>
                    <input type="text" id="balance" name="balance" placeholder="0"
                        class="w-full input  bg-white input-bordered rounded-xl focus:ring-2 focus:ring-green-400 bg-gray-100 cursor-not-allowed text-gray-700"
                    readonly>
                </div>
            </div>

            {{-- Amount Being Paid Now --}}
            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1">Amount Being Paid Now</label>
                <input type="text" id="amount" name="amount" placeholder="1000"
                    class="input input-bordered  bg-white w-full rounded-xl focus:ring-2 focus:ring-green-400 text-gray-800">
            </div>

            {{-- Payment Date --}}
            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1">Payment Date</label>
                <div class="relative">
                    <input type="date" id="payment_date" name="payment_date"
                        class="input  bg-white input-bordered w-full rounded-xl focus:ring-2 focus:ring-green-400">
                </div>
            </div>

            {{-- Payment Method --}}
            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1">Payment Method</label>
                <select id="payment_method" name="payment_method"
                    class="input input-bordered  bg-white w-full rounded-xl focus:ring-2 focus:ring-green-400">
                    <option value="" class="bg-white" selected disabled>Select payment method</option>
                    <option value="cash">Cash</option>
                    <option value="online">Online</option>
                    <option value="upi">UPI</option>
                </select>
            </div>

            {{-- Notes --}}
            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1">Notes (Optional)</label>
                <textarea id="notes" name="notes" rows="3"
                    class="textarea textarea-bordered w-full bg-white rounded-xl focus:ring-2 focus:ring-green-400"></textarea>
            </div>

            {{-- Buttons --}}
            <div class="flex justify-end gap-3 pt-6 border-t">
                <button type="button"
                    onclick="document.getElementById('my_modal_1').close()"
                    class="btn bg-gray-200 text-gray-700 hover:bg-gray-300 border-none rounded-lg px-6">
                    Cancel
                </button>

                <button type="submit"
                    class="btn bg-green-500 text-white hover:bg-green-600 border-none rounded-lg px-6">
                    Save Payment
                </button>
            </div>
        </form>
    </div>
</dialog>

@endsection