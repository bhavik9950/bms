@extends('layouts.app')
@section('title', 'My Salary')
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
            <h1 class="text-2xl font-semibold">My Salary</h1>
            <h5 class="text-xs text-gray-500">View your salary information and payment history</h5>
        </div>
    </div>

    <!-- Current Salary Card -->
    @if($currentSalary)
    <div class="bg-white p-6 rounded-lg shadow mb-6">
        <h3 class="text-lg font-medium mb-4">Current Salary</h3>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="text-center">
                <div class="text-2xl font-bold text-green-600">
                    ${{ number_format($currentSalary->base_salary, 2) }}
                </div>
                <p class="text-sm text-gray-600">Base Salary</p>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-blue-600">
                    ${{ number_format($currentSalary->amount_paid, 2) }}
                </div>
                <p class="text-sm text-gray-600">Amount Paid</p>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-red-600">
                    ${{ number_format($currentSalary->pending_amount, 2) }}
                </div>
                <p class="text-sm text-gray-600">Pending Amount</p>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-purple-600">
                    {{ $currentSalary->status === 'active' ? 'Active' : 'Inactive' }}
                </div>
                <p class="text-sm text-gray-600">Status</p>
            </div>
        </div>
        <div class="mt-4 text-sm text-gray-600">
            <p>Last updated: {{ $currentSalary->updated_at->format('M j, Y \a\t H:i') }}</p>
        </div>
    </div>
    @else
    <div class="bg-yellow-50 border border-yellow-200 p-6 rounded-lg shadow mb-6">
        <div class="flex items-center">
            <i class="ti ti-alert-triangle text-yellow-500 text-xl mr-3"></i>
            <div>
                <h3 class="text-lg font-medium text-yellow-800">No Salary Information</h3>
                <p class="text-sm text-yellow-700">Your salary information is not yet configured. Please contact your administrator.</p>
            </div>
        </div>
    </div>
    @endif

    <!-- Salary History -->
    <div class="bg-white p-6 rounded-lg shadow">
        <h3 class="text-lg font-medium mb-4">Salary History</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full table-auto">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Period</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Base Salary</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Amount Paid</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Pending</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($salaries as $salary)
                        <tr>
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">
                                {{ $salary->created_at->format('M Y') }}
                            </td>
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">
                                ${{ number_format($salary->base_salary, 2) }}
                            </td>
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-green-600">
                                ${{ number_format($salary->amount_paid, 2) }}
                            </td>
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-red-600">
                                ${{ number_format($salary->pending_amount, 2) }}
                            </td>
                            <td class="px-4 py-2 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs rounded-full
                                    {{ $salary->status === 'active' ? 'bg-green-100 text-green-600' : 'bg-gray-100 text-gray-600' }}">
                                    {{ ucfirst($salary->status) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                                No salary records found
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Salary Information Note -->
    <div class="bg-blue-50 border border-blue-200 p-4 rounded-lg shadow mt-6">
        <div class="flex items-start">
            <i class="ti ti-info-circle text-blue-500 text-lg mr-3 mt-0.5"></i>
            <div>
                <h4 class="text-sm font-medium text-blue-800">Salary Information</h4>
                <p class="text-sm text-blue-700 mt-1">
                    Your salary is processed monthly. If you have any questions about your salary or payment status,
                    please contact the HR department or your supervisor.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection