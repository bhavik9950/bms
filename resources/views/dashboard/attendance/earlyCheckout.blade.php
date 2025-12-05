@extends('layouts.app')
@section('title', 'Early Checkout Reason')
@section('content')

<div class="container mx-auto py-2 px-2">
    <div class="flex items-center justify-between mb-6">
        {{-- Header Title --}}
        <div>
            <h1 class="text-2xl font-semibold">Early Checkout Reason</h1>
            <h5 class="text-xs text-gray-500">Provide reason for early departure</h5>
        </div>

        <a href="{{ route('dashboard.attendance.index') }}"
            class="bg-red-400 hover:bg-red-600 text-white px-4 py-2 rounded-md">
            <i class="ti ti-arrow-left mr-2"></i>
            Back to Attendance
        </a>
    </div>

    {{-- Current Check-in Status --}}
    <div class="bg-white p-6 rounded-lg shadow mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-lg font-semibold text-gray-800">Current Session</h3>
                <p class="text-sm text-gray-600 mt-1" id="current-time">{{ now()->format('l, F j, Y \a\t g:i A') }}</p>
            </div>
            <div class="text-right">
                <div class="text-2xl font-bold mb-2" id="session-status">
                    <span class="px-3 py-1 text-sm rounded-full bg-green-100 text-green-600">Checked In</span>
                </div>
                <p class="text-sm text-gray-600" id="check-in-time">Checked in at 09:15 AM</p>
            </div>
        </div>
    </div>

    {{-- Early Checkout Warning --}}
    <div class="bg-yellow-50 border border-yellow-200 p-6 rounded-lg shadow mb-6">
        <div class="flex items-start">
            <i class="ti ti-alert-triangle text-2xl text-yellow-600 mr-4 mt-1"></i>
            <div>
                <h3 class="text-lg font-semibold text-yellow-800 mb-2">Early Checkout Notice</h3>
                <p class="text-yellow-700 mb-3">
                    You are attempting to check out before the standard working hours end.
                    Please provide a valid reason for your early departure.
                </p>
                <div class="bg-yellow-100 p-3 rounded">
                    <p class="text-sm text-yellow-800">
                        <strong>Standard working hours:</strong> 9:00 AM - 6:00 PM<br>
                        <strong>Your checkout time:</strong> <span id="checkout-time-display">{{ now()->format('g:i A') }}</span>
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- Reason Selection Form --}}
    <div class="bg-white p-6 rounded-lg shadow mb-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Select Reason for Early Checkout</h3>
        <form id="early-checkout-form">
            @csrf

            {{-- Reason Selection --}}
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-3">Reason *</label>
                <div class="space-y-3">
                    <div class="flex items-center">
                        <input type="radio" id="reason-emergency" name="reason" value="emergency" class="mr-3" required>
                        <label for="reason-emergency" class="flex-1 cursor-pointer">
                            <div class="flex items-center justify-between">
                                <span class="font-medium">Medical Emergency</span>
                                <span class="text-sm text-gray-500">Family or personal medical issue</span>
                            </div>
                        </label>
                    </div>
                    <div class="flex items-center">
                        <input type="radio" id="reason-transport" name="reason" value="transport" class="mr-3">
                        <label for="reason-transport" class="flex-1 cursor-pointer">
                            <div class="flex items-center justify-between">
                                <span class="font-medium">Transportation Issues</span>
                                <span class="text-sm text-gray-500">Vehicle breakdown, traffic, etc.</span>
                            </div>
                        </label>
                    </div>
                    <div class="flex items-center">
                        <input type="radio" id="reason-family" name="reason" value="family" class="mr-3">
                        <label for="reason-family" class="flex-1 cursor-pointer">
                            <div class="flex items-center justify-between">
                                <span class="font-medium">Family Emergency</span>
                                <span class="text-sm text-gray-500">Family member needs assistance</span>
                            </div>
                        </label>
                    </div>
                    <div class="flex items-center">
                        <input type="radio" id="reason-appointment" name="reason" value="appointment" class="mr-3">
                        <label for="reason-appointment" class="flex-1 cursor-pointer">
                            <div class="flex items-center justify-between">
                                <span class="font-medium">Medical/Doctor Appointment</span>
                                <span class="text-sm text-gray-500">Pre-scheduled medical visit</span>
                            </div>
                        </label>
                    </div>
                    <div class="flex items-center">
                        <input type="radio" id="reason-personal" name="reason" value="personal" class="mr-3">
                        <label for="reason-personal" class="flex-1 cursor-pointer">
                            <div class="flex items-center justify-between">
                                <span class="font-medium">Personal Reasons</span>
                                <span class="text-sm text-gray-500">Other personal matters</span>
                            </div>
                        </label>
                    </div>
                    <div class="flex items-center">
                        <input type="radio" id="reason-other" name="reason" value="other" class="mr-3">
                        <label for="reason-other" class="flex-1 cursor-pointer">
                            <div class="flex items-center justify-between">
                                <span class="font-medium">Other</span>
                                <span class="text-sm text-gray-500">Please specify below</span>
                            </div>
                        </label>
                    </div>
                </div>
            </div>

            {{-- Additional Details --}}
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Additional Details</label>
                <textarea id="additional-details" name="details" rows="4"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring focus:border-indigo-400"
                    placeholder="Please provide more details about your early checkout reason..."></textarea>
                <p class="text-xs text-gray-500 mt-1">Maximum 500 characters</p>
            </div>

            {{-- Contact Information --}}
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Contact Information (Optional)</label>
                <input type="tel" id="contact-number" name="contact" pattern="[0-9]{10}"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring focus:border-indigo-400"
                    placeholder="Enter your contact number for follow-up">
                <p class="text-xs text-gray-500 mt-1">10-digit mobile number</p>
            </div>

            {{-- Supporting Documents --}}
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Supporting Documents (Optional)</label>
                <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center">
                    <i class="ti ti-upload text-3xl text-gray-400 mb-2"></i>
                    <p class="text-gray-600 mb-2">Upload supporting documents</p>
                    <p class="text-sm text-gray-500">Medical certificates, appointment letters, etc.</p>
                    <input type="file" id="supporting-docs" name="documents" accept=".pdf,.jpg,.jpeg,.png"
                        class="hidden" multiple>
                    <button type="button" id="upload-btn"
                        class="mt-2 bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded">
                        Choose Files
                    </button>
                </div>
                <div id="file-list" class="mt-2 space-y-1"></div>
            </div>

            {{-- Location Verification --}}
            <div class="mb-6 p-4 bg-blue-50 rounded-lg">
                <h4 class="text-sm font-medium text-blue-800 mb-2">Location Verification</h4>
                <p class="text-sm text-blue-700 mb-2">Your current location will be recorded with this early checkout request.</p>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                    <div>
                        <span class="text-blue-600">Latitude:</span>
                        <span id="checkout-latitude" class="font-mono">-</span>
                    </div>
                    <div>
                        <span class="text-blue-600">Longitude:</span>
                        <span id="checkout-longitude" class="font-mono">-</span>
                    </div>
                    <div>
                        <span class="text-blue-600">Accuracy:</span>
                        <span id="checkout-accuracy" class="font-mono">-</span>
                    </div>
                </div>
            </div>

            {{-- Submit Buttons --}}
            <div class="flex justify-end space-x-3">
                <button type="button" id="cancel-btn"
                    class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-lg">
                    Cancel
                </button>
                <button type="submit" id="submit-btn"
                    class="bg-red-500 hover:bg-red-600 text-white px-6 py-3 rounded-lg disabled:opacity-50 disabled:cursor-not-allowed">
                    <i class="ti ti-send mr-2"></i>
                    Submit Early Checkout
                </button>
            </div>
        </form>
    </div>

    {{-- Recent Early Checkouts --}}
    <div class="bg-white p-6 rounded-lg shadow">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Recent Early Checkout Requests</h3>
        <div id="recent-requests" class="space-y-3">
            <div class="text-center text-gray-500 py-8">
                <i class="ti ti-history text-3xl mb-2"></i>
                <p>No recent early checkout requests</p>
            </div>
        </div>
    </div>
</div>

{{-- JavaScript for early checkout --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('early-checkout-form');
    const submitBtn = document.getElementById('submit-btn');
    const cancelBtn = document.getElementById('cancel-btn');
    const uploadBtn = document.getElementById('upload-btn');
    const supportingDocs = document.getElementById('supporting-docs');
    const fileList = document.getElementById('file-list');
    const additionalDetails = document.getElementById('additional-details');
    const recentRequests = document.getElementById('recent-requests');

    let selectedFiles = [];

    // Mock recent requests
    const mockRequests = [
        {
            date: '2025-12-02',
            time: '04:30 PM',
            reason: 'Medical Emergency',
            status: 'approved'
        },
        {
            date: '2025-11-28',
            time: '03:45 PM',
            reason: 'Family Emergency',
            status: 'approved'
        }
    ];

    // Get user's location
    function getLocation() {
        return new Promise((resolve, reject) => {
            if (!navigator.geolocation) {
                reject(new Error('Geolocation is not supported'));
                return;
            }

            navigator.geolocation.getCurrentPosition(
                (position) => {
                    const location = {
                        latitude: position.coords.latitude,
                        longitude: position.coords.longitude,
                        accuracy: position.coords.accuracy
                    };

                    // Update location display
                    document.getElementById('checkout-latitude').textContent = location.latitude.toFixed(6);
                    document.getElementById('checkout-longitude').textContent = location.longitude.toFixed(6);
                    document.getElementById('checkout-accuracy').textContent = location.accuracy.toFixed(1) + 'm';

                    resolve(location);
                },
                (error) => {
                    reject(error);
                },
                {
                    enableHighAccuracy: true,
                    timeout: 10000,
                    maximumAge: 300000 // 5 minutes
                }
            );
        });
    }

    // Handle file upload
    function handleFileUpload(files) {
        selectedFiles = Array.from(files);
        renderFileList();

        // Validate file types and sizes
        const allowedTypes = ['application/pdf', 'image/jpeg', 'image/jpg', 'image/png'];
        const maxSize = 5 * 1024 * 1024; // 5MB

        for (let file of selectedFiles) {
            if (!allowedTypes.includes(file.type)) {
                alert(`File "${file.name}" is not allowed. Only PDF and image files are accepted.`);
                selectedFiles = selectedFiles.filter(f => f !== file);
                renderFileList();
                return;
            }
            if (file.size > maxSize) {
                alert(`File "${file.name}" is too large. Maximum size is 5MB.`);
                selectedFiles = selectedFiles.filter(f => f !== file);
                renderFileList();
                return;
            }
        }
    }

    // Render file list
    function renderFileList() {
        fileList.innerHTML = '';
        selectedFiles.forEach((file, index) => {
            const fileItem = document.createElement('div');
            fileItem.className = 'flex items-center justify-between bg-gray-50 p-2 rounded';
            fileItem.innerHTML = `
                <div class="flex items-center">
                    <i class="ti ti-file text-lg mr-2 text-blue-600"></i>
                    <span class="text-sm">${file.name}</span>
                    <span class="text-xs text-gray-500 ml-2">(${(file.size / 1024).toFixed(1)} KB)</span>
                </div>
                <button type="button" class="text-red-500 hover:text-red-700 remove-file" data-index="${index}">
                    <i class="ti ti-x"></i>
                </button>
            `;
            fileList.appendChild(fileItem);
        });

        // Add event listeners for remove buttons
        document.querySelectorAll('.remove-file').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const index = parseInt(e.currentTarget.dataset.index);
                selectedFiles.splice(index, 1);
                renderFileList();
            });
        });
    }

    // Validate form
    function validateForm() {
        const reason = form.querySelector('input[name="reason"]:checked');
        if (!reason) {
            alert('Please select a reason for early checkout.');
            return false;
        }

        const details = additionalDetails.value.trim();
        if (details.length > 500) {
            alert('Additional details must be less than 500 characters.');
            return false;
        }

        return true;
    }

    // Submit form
    async function submitForm(e) {
        e.preventDefault();

        if (!validateForm()) return;

        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="ti ti-loader animate-spin mr-2"></i> Submitting...';

        try {
            // Get location
            const location = await getLocation();

            const formData = new FormData(form);
            const submitData = {
                reason: formData.get('reason'),
                details: formData.get('details'),
                contact: formData.get('contact'),
                location: location,
                timestamp: new Date().toISOString(),
                files: selectedFiles
            };

            // Simulate API call
            await new Promise(resolve => setTimeout(resolve, 2000));

            // Add to recent requests
            const newRequest = {
                date: new Date().toLocaleDateString(),
                time: new Date().toLocaleTimeString(),
                reason: submitData.reason.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase()),
                status: 'pending'
            };
            mockRequests.unshift(newRequest);
            renderRecentRequests();

            alert('Early checkout request submitted successfully! Your supervisor will review it.');

            // Reset form
            form.reset();
            selectedFiles = [];
            renderFileList();

        } catch (error) {
            console.error('Submit error:', error);
            alert('Failed to submit early checkout request. Please ensure location services are enabled.');
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="ti ti-send mr-2"></i> Submit Early Checkout';
        }
    }

    // Cancel form
    function cancelForm() {
        if (confirm('Are you sure you want to cancel? Any unsaved changes will be lost.')) {
            form.reset();
            selectedFiles = [];
            renderFileList();
        }
    }

    // Render recent requests
    function renderRecentRequests() {
        let html = '';

        if (mockRequests.length === 0) {
            html = `
                <div class="text-center text-gray-500 py-8">
                    <i class="ti ti-history text-3xl mb-2"></i>
                    <p>No recent early checkout requests</p>
                </div>
            `;
        } else {
            mockRequests.forEach(request => {
                const statusClasses = {
                    'approved': 'bg-green-100 text-green-600',
                    'pending': 'bg-yellow-100 text-yellow-600',
                    'rejected': 'bg-red-100 text-red-600'
                };

                html += `
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div class="flex items-center">
                            <i class="ti ti-clock text-lg mr-3 text-blue-600"></i>
                            <div>
                                <p class="font-medium">${request.date} at ${request.time}</p>
                                <p class="text-sm text-gray-600">Reason: ${request.reason}</p>
                            </div>
                        </div>
                        <span class="px-2 py-1 text-xs rounded-full ${statusClasses[request.status] || 'bg-gray-100 text-gray-600'}">${request.status.charAt(0).toUpperCase() + request.status.slice(1)}</span>
                    </div>
                `;
            });
        }

        recentRequests.innerHTML = html;
    }

    // Character counter for additional details
    additionalDetails.addEventListener('input', function() {
        const remaining = 500 - this.value.length;
        const counter = this.nextElementSibling;
        counter.textContent = `${remaining} characters remaining`;

        if (remaining < 0) {
            counter.classList.add('text-red-600');
            counter.classList.remove('text-gray-500');
        } else {
            counter.classList.remove('text-red-600');
            counter.classList.add('text-gray-500');
        }
    });

    // Initialize
    async function initialize() {
        renderRecentRequests();

        try {
            await getLocation();
        } catch (error) {
            console.warn('Initial location fetch failed:', error);
        }
    }

    // Update current time every minute
    setInterval(() => {
        document.getElementById('current-time').textContent = new Date().toLocaleDateString() + ' at ' + new Date().toLocaleTimeString();
    }, 60000);

    // Event listeners
    form.addEventListener('submit', submitForm);
    cancelBtn.addEventListener('click', cancelForm);
    uploadBtn.addEventListener('click', () => supportingDocs.click());
    supportingDocs.addEventListener('change', (e) => handleFileUpload(e.target.files));

    // Initialize
    initialize();
});
</script>

@endsection