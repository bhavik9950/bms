@extends('layouts.app')
@section('title', 'Staff Dashboard')
@section('content')

<div class="container mx-auto px-2 py-2">
<div class="flex items-center justify-between mb-6">
    {{-- Header Title --}}
    <div>
        <h1 class="text-2xl font-semibold">Staff Directory</h1>
        <h5 class="text-xs text-gray-500">Manage and View all Staff Member</h5>
    </div>

{{-- New Order Button --}}
<!-- Full button (only visible on sm and above) -->
<a href="{{ route('dashboard.staff.create') }}"
   class="bg-green-400 hidden sm:flex items-center hover:bg-green-600 text-white px-4 py-2 rounded-md">
    <i class="ti ti-table-plus mr-2"></i>
    Add New Staff
</a>

<!-- Icon-only button (only visible below sm) -->
<a href="{{ route('dashboard.staff.create') }}"
   class="bg-green-400 sm:hidden hover:bg-green-600 text-white px-3 py-2 rounded-md">
    <i class="ti ti-table-plus"></i>
</a>     
</div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 text-center">
        <div class="bg-white p-6 rounded-lg shadow">
            <h2 class="text-xl font-medium mb-1">Total Staff</h2>
            <p class="text-3xl font-bold">{{$total}}</p>
        </div>
        
        <div class="bg-white p-6 rounded-lg shadow">
            <h2 class="text-xl font-medium mb-1">Active Staff</h2>
            <p class="text-3xl font-bold text-green-400">{{$activeStaff}}</p>
        </div>
        
        <div class="bg-white p-6 rounded-lg shadow">
            <h2 class="text-xl font-medium mb-4">Inactive Staff</h2>
            <p class="text-3xl font-bold text-red-500">{{$inactiveStaff}}</p>
        </div>
    </div>

    <!-- Fillters and export -->
<div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-4 bg-white mt-6 p-4 rounded-lg shadow">
    
    <!-- Search Bar -->
    <div class="lg:col-span-2">
        <input type="text" placeholder="Search staff..." 
               class="w-full border border-gray-300 rounded-2xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
    </div>

    <!-- Role Dropdown -->
    <div>
        {{-- Add Dynamically role/crate Role --}}
        <select class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
            <option value="" disabled>Roll Select</option>
            <option value="all">All Roles</option>
            <option value="master">Master</option>
            <option value="tailor">Tailor</option>
        </select>
    </div>

    <!-- Status Dropdown -->
    <div>
        <select class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
            <option value="">All Status</option>
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
        </select>
    </div>

    <!-- Excel Button -->
    <div>
        <button class="btn rounded-lg text-green-500 bg-white border border-green-500 px-2 py-2 hover:bg-green-100 min-w-max">
            <i class="ti ti-notes text-lg"></i>
            Excel
        </button>
         <button class="btn rounded-lg text-red-500 bg-white border border-red-500 px-2 py-2 hover:bg-red-100 min-w-max ml-2">
            <i class="ti ti-book text-lg"></i>
            PDF
        </button>
    </div>

   

</div>
{{-- Table --}}
    <div class="relative overflow-x-auto bg-white shadow-md sm:rounded-lg mt-6 p-2">
    {{-- Table --}}
    <table id="staff-table" class="table  bg-white table-bordered w-full">
        <thead class="text-xs text-gray-700  uppercase">
            <tr class="text-center  bg-gray-800 text-white">
                <th class="px-6 py-3">Staff</th>
                <th class="px-6 py-3">Contact</th>
                <th class="px-6 py-3">Role</th>
                <th class="px-6 py-3">Shift</th>
                <th class="px-6 py-3">Salary</th>
                <th class="px-6 py-3">Status</th>
                <th class="px-6 py-3">Action</th>
            </tr>
        </thead>
        <tbody>
           @foreach($stf as $s)
<tr id="row-{{ $s->id }}" class="bg-white border-b text-center">
   <td class="px-6 py-4 col-full-name flex items-center">
  <img id="profilePreview" class="w-10 h-10 p-1 rounded-full ring-1 ring-gray-300 dark:ring-gray-500 mr-2"
       src="{{ $s->profile_picture ? asset('storage/' . $s->profile_picture) : "https://avatar.iran.liara.run/public" }}" alt="Bordered avatar">
  <span>{{ $s->full_name }}</span>
</td>

    <td class="px-6 py-4 col-phone">{{ $s->phone }}</td>
    <td class="px-6 py-4 col-role">{{ $s->role->role }}</td>
      <td class="px-6 py-4 col-shift">{{ $s->shift_start_time }} AM -{{$s->shift_end_time}} PM</td>
      <td class="px-6 py-4 col-salary"> {{ $s->salary ? $s->salary->base_salary : 'N/A' }}</td>
       <td class="px-6 py-4 col-status user-select-none">
    @if($s->status == 1)
        <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-600">Active</span>
    @else
        <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-600">Inactive</span>
    @endif
</td>

     <td class="px-6 py-4 text-center">
  <div class="relative" x-data="{ open: false }">
    <button @click="open = !open" class="text-gray-500 hover:text-gray-700 p-2 rounded-full hover:bg-gray-100">
      <i class="ti ti-dots-vertical text-lg"></i>
    </button>

    <div x-show="open" @click.away="open = false" x-cloak
         class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg z-10 border border-gray-200">

      <!-- Edit -->
      <button onclick='editStaff(@json($s))'
              class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
        <i class="ti ti-edit mr-3 text-blue-500"></i>
        Edit Staff
      </button>

      <!-- Toggle Status -->
      <button onclick="toggleStaffStatus('{{ $s->id }}', {{ $s->status }})"
              class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
        <i class="ti ti-{{ $s->status ? 'x' : 'check' }} mr-3 {{ $s->status ? 'text-red-500' : 'text-green-500' }}"></i>
        {{ $s->status ? 'Deactivate' : 'Activate' }}
      </button>

      <!-- Send Credentials -->
      <button onclick="sendCredentials('{{ $s->id }}')"
              class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
        <i class="ti ti-mail mr-3 text-purple-500"></i>
        Send Credentials
      </button>

      <!-- Delete -->
      <div class="border-t border-gray-200"></div>
      <button data-id="{{ $s->id }}" onclick="deleteStaff('{{ $s->id }}')"
              class="flex items-center w-full px-4 py-2 text-sm text-red-600 hover:bg-red-50">
        <i class="ti ti-trash mr-3"></i>
        Delete Staff
      </button>     
    </div>
  </div>
</td>
</tr>
@endforeach

        </tbody>
    </table>

</div>
</div>
@endsection

