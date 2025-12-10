@extends('layouts.app')
@section('title', 'My Profile')
@section('content')

<div class="container mx-auto px-2 py-2">
    @if (session('success'))
        <div class="mb-4 px-4 py-2 bg-green-100 border border-green-400 text-green-700 rounded">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="mb-4 px-4 py-2 bg-red-100 border border-red-400 text-red-700 rounded">
            {{ session('error') }}
        </div>
    @endif

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-semibold">My Profile</h1>
            <h5 class="text-xs text-gray-500">Manage your personal information</h5>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Profile Information Form -->
        <div class="lg:col-span-2">
            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="text-lg font-medium mb-4">Personal Information</h3>

                <form action="{{ route('dashboard.staff.profile.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Read-only fields -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Staff ID</label>
                            <input type="text" value="{{ $staff->staff_code }}" readonly
                                class="mt-1 block w-full rounded-lg border-gray-300 bg-gray-50 shadow-sm sm:text-sm p-2 cursor-not-allowed">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Full Name</label>
                            <input type="text" value="{{ $staff->full_name }}" readonly
                                class="mt-1 block w-full rounded-lg border-gray-300 bg-gray-50 shadow-sm sm:text-sm p-2 cursor-not-allowed">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Email</label>
                            <input type="email" value="{{ $staff->email }}" readonly
                                class="mt-1 block w-full rounded-lg border-gray-300 bg-gray-50 shadow-sm sm:text-sm p-2 cursor-not-allowed">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Role</label>
                            <input type="text" value="{{ $staff->role->role ?? 'N/A' }}" readonly
                                class="mt-1 block w-full rounded-lg border-gray-300 bg-gray-50 shadow-sm sm:text-sm p-2 cursor-not-allowed">
                        </div>

                        <!-- Editable fields -->
                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700">Phone Number</label>
                            <input type="text" id="phone" name="phone"
                                value="{{ old('phone', $staff->phone) }}"
                                placeholder="Enter phone number"
                                class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 sm:text-sm p-2">
                        </div>

                        <div>
                            <label for="emergency_contact" class="block text-sm font-medium text-gray-700">Emergency Contact</label>
                            <input type="text" id="emergency_contact" name="emergency_contact"
                                value="{{ old('emergency_contact', $staff->emergency_contact) }}"
                                placeholder="Emergency contact number"
                                class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 sm:text-sm p-2">
                        </div>

                        <div class="md:col-span-2">
                            <label for="address" class="block text-sm font-medium text-gray-700">Address</label>
                            <textarea id="address" name="address" rows="3"
                                placeholder="Enter your address"
                                class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 sm:text-sm p-2">{{ old('address', $staff->address) }}</textarea>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end">
                        <button type="submit" class="bg-indigo-500 text-white px-6 py-2 rounded hover:bg-indigo-600">
                            <i class="ti ti-device-floppy mr-2"></i>Update Profile
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Profile Photo and Quick Info -->
        <div class="space-y-6">
            <!-- Profile Photo -->
            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="text-lg font-medium mb-4">Profile Photo</h3>
                <div class="text-center">
                    <img src="{{ $staff->profile_picture ? asset('storage/' . $staff->profile_picture) : 'https://avatar.iran.liara.run/public' }}"
                         alt="Profile Photo"
                         class="w-24 h-24 rounded-full mx-auto mb-4 object-cover border-4 border-gray-200">

                    <p class="text-sm text-gray-600">
                        To change your profile photo, please contact your administrator.
                    </p>
                </div>
            </div>

            <!-- Employment Information -->
            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="text-lg font-medium mb-4">Employment Information</h3>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Joining Date:</span>
                        <span class="font-medium">{{ $staff->joining_date ? $staff->joining_date->format('M j, Y') : 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Shift:</span>
                        <span class="font-medium">
                            {{ $staff->shift_start_time }} - {{ $staff->shift_end_time }}
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Status:</span>
                        <span class="font-medium">
                            <span class="px-2 py-1 text-xs rounded-full {{ $staff->status ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600' }}">
                                {{ $staff->status ? 'Active' : 'Inactive' }}
                            </span>
                        </span>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="text-lg font-medium mb-4">Quick Actions</h3>
                <div class="space-y-3">
                    <a href="{{ route('dashboard.staff.attendance') }}"
                       class="block w-full bg-blue-500 text-white text-center py-2 px-4 rounded hover:bg-blue-600">
                        <i class="ti ti-clock mr-2"></i>Mark Attendance
                    </a>
                    <a href="{{ route('dashboard.staff.salary') }}"
                       class="block w-full bg-green-500 text-white text-center py-2 px-4 rounded hover:bg-green-600">
                        <i class="ti ti-cash mr-2"></i>View Salary
                    </a>
                    <a href="{{ route('dashboard.staff.dashboard') }}"
                       class="block w-full bg-gray-500 text-white text-center py-2 px-4 rounded hover:bg-gray-600">
                        <i class="ti ti-dashboard mr-2"></i>Back to Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection