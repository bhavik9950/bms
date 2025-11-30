@extends('layouts.app')
@section('title', 'Sallery Management')
@section('content')
<style>
#payForm select {
    background-color: white !important;
}
</style>

<div class="container mx-auto py-2 px-2">
    <div class="flex items-center justify-between mb-6">
        {{-- Header Title --}}
        <div>
            <h1 class="text-2xl font-semibold">Staff Salary Management</h1>
            <h5 class="text-xs text-gray-500">Overview of all staff salaries and payment status</h5>
        </div>
        <div class="flex space-x-2">
            {{-- New Order Button --}}
            <a href="#"
   class="text-red-400  border-1 border-solid outline-red-400 hidden sm:flex items-center  px-4 py-2 rounded-lg hover:ourline-red-600 hover:text-red-600">
    <i class="ti ti-file-type-pdf mr-2"></i>
    Export PDF
</a>

<!-- Icon-only button (only visible below sm) -->
<a href="#"
   class="sm:hidden text-red-400  border-1 border-solid outline-red-400 px-3 py-2 rounded-lg hover:ourline-red-600 hover:text-red-600">
    <i class="ti ti-file-type-pdf"></i>
</a>
<a href="#"
   class="text-green-400  border-1 border-solid outline-green-400 hidden sm:flex items-center  px-4 py-2 rounded-lg hover:ourline-green-600 hover:text-green-600">
    <i class="ti ti-upload mr-2"></i>
    Export Excel
</a>

<!-- Icon-only button (only visible below sm) -->
<a href="#"
   class="text-green-400  border-1 border-solid outline-green-400 sm:hidden  px-3 py-2 rounded-lg hover:ourline-green-600 hover:text-green-600">
    <i class="ti ti-upload"></i>
</a>
        </div>
    </div>
   <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-3 mb-6 ">
    <div class="bg-white p-4 pt-3 rounded-lg shadow">
        <h2 class="text-lg font-medium mb-1">Total Staff</h2>
        <div class="flex items-center justify-between mt-4">
            <p class="text-2xl font-medium text-gray-600">10</p>
            <i class="ti ti-users text-3xl text-gray-600 px-4 py-2 bg-gray-50 rounded-lg"></i>
        </div>
    </div>

    <div class="bg-white p-4 pt-3 rounded-lg shadow">
        <h2 class="text-lg font-medium mb-1">Total Budget</h2>
        <div class="flex items-center justify-between mt-4">
            <p class="text-2xl font-medium text-indigo-400">₹102K</p>
            <i class="ti ti-currency-rupee text-3xl text-indigo-400 px-3 py-2 bg-indigo-50 rounded-lg"></i>
        </div>
    </div>

    <div class="bg-white p-4 pt-3 rounded-lg shadow">
        <h2 class="text-lg font-medium mb-1">Paid</h2>
        <div class="flex items-center justify-between mt-4">
            <p class="text-2xl font-medium text-green-400">40</p>
            <i class="ti ti-cash text-3xl text-green-400 px-4 py-2 bg-green-50 rounded-lg"></i>
        </div>
    </div>

    <div class="bg-white p-4 pt-3 rounded-lg shadow">
        <h2 class="text-lg font-medium mb-1">Unpaid</h2>
        <div class="flex items-center justify-between mt-4 ">
            <p class="text-2xl font-medium text-red-500">70</p>
            <i class="ti ti-circle-x text-3xl text-red-500 px-4 py-2 bg-red-50 rounded-lg"></i>
        </div>
    </div>

    <div class="bg-white p-4 pt-3 rounded-lg shadow">
        <h2 class="text-lg font-medium mb-1">Avg Attendence</h2>
        <div class="flex items-center justify-between mt-4 ">
            <p class="text-2xl font-medium text-yellow-500">90%</p>
            <i class="ti ti-trending-up text-3xl text-yellow-500 px-4 py-2 bg-yellow-50 rounded-lg"></i>
        </div>
    </div>
</div>
{{-- Search & Filters --}}
<div class="bg-white flex flex-wrap items-center justify-between gap-4 p-4 rounded-lg shadow">

    <!-- Search Box -->
    <div class="relative flex-1 min-w-[200px]">
        <i class="ti ti-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
        <input 
            type="text" 
            placeholder="Search staff by name or role..." 
            class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring focus:border-indigo-400"
            id="search-staff"
        >
    </div>

    <!-- Filters & Clear -->
    <div class="flex items-center gap-3">
        <!-- Role Filter -->
        <select 
            id="role-filter"
            class="border border-gray-300 py-2 px-3 rounded-md text-gray-700
                   focus:outline-none focus:ring focus:border-indigo-400"
        >
        <option value="all" disabled selected>All Roles</option>
        @foreach ($roles as $role)
                <option value="{{ $role->id }}">{{ $role->role }}</option>
              @endforeach
        </select>

        <!-- Clear Button -->
        <button 
            class="flex items-center gap-1 px-3 py-2 border border-gray-300 rounded-md text-gray-600 hover:bg-gray-100"
            id="clear-filters"
        >
            <i class="ti ti-filter-off"></i> Clear
        </button>
    </div>

</div>

  <div class="relative overflow-x-auto bg-white shadow-md sm:rounded-lg mt-6 p-2">
    {{-- Table --}}
    <table id="salary-table" class="table  bg-white table-bordered w-full">
        <thead class="text-xs text-gray-700  uppercase">
            <tr class="text-center  bg-gray-800 text-white">
                <th class="px-6 py-3">Staff Id</th>
                <th class="px-6 py-3">Name</th>
                <th class="px-6 py-3">Role</th>
                <th class="px-6 py-3">Base Salary</th>
                <th class="px-6 py-3">Balance</th>
                <th class="px-6 py-3">Pay</th>
                <th class="px-6 py-3">View</th>
            </tr>
        </thead>
        <tbody>
           @foreach($salaries as $salary)
<tr id="row-{{ $salary->staff->id }}" class="bg-white border-b text-center">
   <td class="px-6 py-4">{{ $salary->staff->staff_code }}</td>
   <td class="px-6 py-4 col-full-name flex items-center justify-center">
  <img class="w-10 h-10  rounded-full ring-1 ring-gray-300 dark:ring-gray-500 mr-2"
       src="{{ $salary->staff->profile_picture ? asset('storage/' . $salary->staff->profile_picture) : "https://avatar.iran.liara.run/public" }}" alt="Bordered avatar">
  <span>{{ $salary->staff->full_name }}</span>
</td>

    <td class="px-6 py-4 col-role">{{ $salary->staff->role->role }}</td>
      <td class="px-6 py-4">₹{{ number_format($salary->base_salary) }}</td>
      <td class="px-6 py-4">₹{{ number_format($salary->pending_amount) }}</td>
      <td class="px-6 py-4">
        <button class="text-white btn bg-green-500 hover:bg-green-600 rounded-lg px-3 py-1 border-0"id="pay-button-{{ $salary->staff->id }}"  onclick="document.getElementById('my_modal_1').showModal();">
            <i class="ti ti-cash"></i> Pay
        </button>
      </td>
      <td class="px-6 py-4 text-center">
        <a href="{{ route('dashboard.staff.salary.view', $salary->staff->id) }}"
            class="text-white btn bg-gray-400 hover:bg-gray-600 rounded-lg px-3 py-1 border-0">
            <p>View History</p>
        </a>
    </td>
</tr>
@endforeach

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