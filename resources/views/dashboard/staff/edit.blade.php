<!-- CORRECTED edit.blade.php - Key Changes Highlighted -->

@extends('layouts.app')
@section('content')
<div class="container mx-auto px-2 py-2">

  <div class="flex items-center justify-between mb-6">
    {{-- Header Title --}}
    <div>
      <h1 class="text-2xl font-semibold">Edit Staff Member</h1>
      <h5 class="text-xs text-gray-500">Edit Employee Information</h5>
    </div>

    {{-- View List Buttons --}}
    <a href="{{ route('dashboard.staff.management') }}"
       class="bg-red-400 hidden sm:flex items-center hover:bg-red-600 text-white px-4 py-2 rounded-md">
      <i class="ti ti-list mr-2"></i>
      View List
    </a>
    <a href="{{ route('dashboard.staff.management') }}"
       class="bg-red-400 sm:hidden hover:bg-red-600 text-white px-3 py-2 rounded-md">
      <i class="ti ti-list"></i>
    </a>
  </div>

  <div class="grid grid-cols-3 gap-4">
    {{-- LEFT: Staff Form --}}
    <div class="bg-white col-span-3 md:col-span-2 rounded-lg p-4">
  
      <!-- ✅ FIX 1: Changed form ID from 'edit-staff-info' to 'staff-info' to match JS -->
      <form action="{{ route('dashboard.staff.update', $staff->id) }}" 
            method="POST" 
            class="lg:col-span-8 bg-white rounded-lg mb-4" 
            id="staff-info" 
            enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <div class="flex items-center justify-between mb-4">
          <h2 class="text-lg font-semibold flex items-center">
            <i class="ti ti-user mr-2 text-gray-600"></i>
            Personal Information
          </h2>
          <span class="w-48 rounded-lg shadow-sm sm:text-sm p-2 bg-gray-100 text-right inline-block">
            {{ $staff->staff_code }}
          </span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <label for="full_name" class="block text-sm font-medium text-gray-700">Full Name*</label>
            <input type="text" id="full_name" name="full_name"
                   value="{{ old('full_name', $staff->full_name) }}"
                   placeholder="Enter full name" required
                   class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 sm:text-sm p-2">
          </div>
          
          <div>
            <label for="phone" class="block text-sm font-medium text-gray-700">Phone Number*</label>
            <input type="text" id="phone" name="phone"
                   value="{{ old('phone', $staff->phone) }}"
                   placeholder="+91 55555-012325" required
                   class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 sm:text-sm p-2">
          </div>

          <div>
            <label for="email" class="block text-sm font-medium text-gray-700">Email (optional)</label>
            <input type="email" id="email" name="email"
                   value="{{ old('email', $staff->email) }}"
                   placeholder="Enter email address"
                   class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 sm:text-sm p-2">
          </div>

          {{-- Joining Date --}}
          <div x-data>
            <label for="joining_date" class="block text-sm font-medium text-gray-700">Joining Date*</label>
            <div class="relative mt-1">
              <input type="text" id="joining_date" name="joining_date"
                     value="{{ old('joining_date', $staff->joining_date ? $staff->joining_date->format('d-m-Y') : '') }}"
                     placeholder="DD-MM-YYYY" x-mask="99-99-9999" required
                     class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 sm:text-sm p-2 pr-10">
              <span class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-500 pointer-events-none">
                <i class="ti ti-calendar text-base font-medium"></i>
              </span>
            </div>
          </div>

          <div class="md:col-span-2">
            <label for="address" class="block text-sm font-medium text-gray-700">Address*</label>
            <input type="text" id="address" name="address"
                   value="{{ old('address', $staff->address) }}"
                   placeholder="Enter staff member address" required
                   class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 sm:text-sm p-2">
          </div>
        </div>

        <h2 class="text-lg font-semibold mt-4 mb-4 flex items-center">
          Work Information
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <label for="role_id" class="block text-sm font-medium text-gray-700">Role*</label>
            <select name="role_id" id="role_id"
              class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 sm:text-sm p-2" required>
              <option value="">Select a role</option>
              @foreach ($roles as $role)
                <option value="{{ $role->id }}" {{ old('role_id', $staff->role_id) == $role->id ? 'selected' : '' }}>
                  {{ $role->role }}
                </option>
              @endforeach
            </select>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label for="shift_start_time" class="block text-sm font-medium text-gray-700">Shift Start Time*</label>
              <input type="time" name="shift_start_time" id="shift_start_time"
                     value="{{ old('shift_start_time', $staff->shift_start_time) }}"
                     class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 sm:text-sm p-2"
                     required>
            </div>
            <div>
              <label for="shift_end_time" class="block text-sm font-medium text-gray-700">Shift End Time*</label>
              <input type="time" name="shift_end_time" id="shift_end_time"
                     value="{{ old('shift_end_time', $staff->shift_end_time) }}"
                     class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 sm:text-sm p-2"
                     required>
            </div>
          </div>

          <!-- ✅ FIX 2: Added salary field display for edit -->
          <div class="md:col-span-2">
            <label for="base_salary" class="block text-sm font-medium text-gray-700">Base Salary*</label>
            <input type="number" id="base_salary" name="base_salary"
                   value="{{ old('base_salary', $staff->salary?->base_salary ?? '') }}"
                   placeholder="Enter salary amount" 
                   step="0.01"
                   required
                   class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 sm:text-sm p-2">
          </div>

          <!-- ✅ FIX 3: Added status field (hidden, defaults to 1 for active) -->
          <input type="hidden" name="status" value="1">

          <!-- Password field for editing staff login credentials -->
          <div class="md:col-span-2">
            <label for="password" class="block text-sm font-medium text-gray-700">Password (leave blank to keep current)</label>
            <div class="relative">
              <input type="password" id="password" name="password"
                     placeholder="Enter new password"
                     class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 sm:text-sm p-2 pr-10">
              <i id="togglePassword"
                 class="fa-solid fa-eye-slash absolute inset-y-0 right-3 flex items-center cursor-pointer text-gray-500 pt-2"></i>
            </div>
            <p class="text-xs text-gray-500 mt-1">Leave blank if you don't want to change the password</p>
          </div>

          <!-- Password confirmation field -->
          <div class="md:col-span-2">
            <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm Password</label>
            <div class="relative">
              <input type="password" id="password_confirmation" name="password_confirmation"
                     placeholder="Confirm new password"
                     class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 sm:text-sm p-2 pr-10">
              <i id="togglePasswordConfirm"
                 class="fa-solid fa-eye-slash absolute inset-y-0 right-3 flex items-center cursor-pointer text-gray-500 pt-2"></i>
            </div>
          </div>
        </div>

        <h2 class="text-lg font-semibold mt-4 mb-4 flex items-center">Documents</h2>

        <p class="font-semibold mb-2">ID Proof (Optional)</p>
        <div class="relative border-2 border-dashed border-gray-300 rounded-lg hover:border-indigo-400 transition cursor-pointer bg-gray-50 p-6 extraOutline-id">
          <input type="file"
                 class="absolute inset-0 opacity-0 cursor-pointer staff-id-upload-input"
                 accept="image/*" name="id_proof">
          <span class="file-name-id hidden absolute inset-0 flex items-center justify-center text-base font-medium text-gray-700 bg-gray-50"></span>
          <div class="upload-instructions-id flex flex-col items-center bg-gray-50 justify-center pointer-events-none">
            <i class="fas fa-download text-indigo-500 text-3xl mb-2"></i>
            <p class="text-gray-600 text-sm mb-1">Click or drag to upload ID Proof</p>
            <small class="text-gray-400">(Max 2MB, JPG/PNG)</small>
          </div>
        </div>

        @if($staff->id_proof)
        <div class="mt-2">
          <p class="text-sm text-gray-600">Current ID Proof:</p>
          <img src="{{ asset('storage/' . $staff->id_proof) }}" alt="ID Proof" class="max-w-xs max-h-32">
        </div>
        @endif

        <div class="bg-white p-4 rounded-lg flex justify-end gap-3 mt-6">
          <button type="submit" id="submit-staff"
                  class="px-4 py-2 rounded-md bg-indigo-500 hover:bg-indigo-600 text-white flex items-center">
            <i class="ti ti-edit mr-2"></i>
            Update Staff Member
          </button>
          <button type="reset"
                  class="px-4 py-2 rounded-md bg-red-500 hover:bg-red-600 text-white flex items-center">
            <i class="ti ti-refresh mr-2"></i>
            Reset
          </button>
        </div>
      </form>
    </div>

    {{-- RIGHT: Profile Photo + Quick Info --}}
    <div class="col-span-3 md:col-span-1">
      <div class="bg-white mb-6 p-4 rounded-lg">
        <span class="text-lg font-semibold flex items-center">
          <i class="ti ti-camera text-2xl m-2"></i>
          Profile Photo
        </span>

        <div class="m-4 flex justify-center">
          <img id="profilePreview" 
               class="w-20 h-20 p-1 rounded-full ring-2 ring-gray-300 dark:ring-gray-500"
               src="{{ $staff->profile_picture ? asset('storage/' . $staff->profile_picture) : 'https://avatar.iran.liara.run/public' }}" 
               alt="Profile photo">
        </div>

        <!-- ✅ FIX 4: Updated form attribute to match corrected form ID -->
        <div class="relative border-2 border-dashed border-gray-300 rounded-lg hover:border-indigo-400 transition cursor-pointer bg-gray-50 p-6 extraOutline">
          <input type="file"
                 class="absolute inset-0 opacity-0 cursor-pointer staff-file-upload-input"
                 accept="image/*"
                 name="profile_picture"
                 form="staff-info">
          <span class="file-name hidden absolute inset-0 flex items-center justify-center text-base font-medium text-gray-700 bg-gray-50"></span>
          <div class="upload-instructions flex flex-col items-center bg-gray-50 justify-center pointer-events-none">
            <i class="fas fa-camera text-indigo-500 text-3xl mb-2"></i>
            <p class="text-gray-600 text-sm mb-1">Click or drag to upload photo</p>
            <small class="text-gray-400">(Max 2MB, JPG/PNG)</small>
          </div>
        </div>

        <button type="submit" form="staff-info"
                class="mt-4 w-full px-4 py-2 rounded-md bg-indigo-500 hover:bg-indigo-600 text-white flex items-center justify-center">
          <i class="ti ti-device-floppy mr-2"></i>
          Update
        </button>
      </div>

      {{-- Quick Information --}}
      <div class="bg-white p-4 rounded-lg">
        <h2 class="font-semibold text-lg">Quick Information</h2>
        <div class="flex justify-between mt-2">
          <span class="text-gray-600">Total Staff:</span>
          <span class="font-medium">20</span>
        </div>
        <div class="flex justify-between mt-2">
          <span class="text-gray-600">Active Roles:</span>
          <span class="font-medium">12</span>
        </div>
        <div class="flex justify-between mt-2">
          <span class="text-gray-600">This Month Joined:</span>
          <span class="font-medium">2</span>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
