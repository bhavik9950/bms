@extends('layouts.app')
@section('title', 'Break Management')
@section('content')

<div class="container mx-auto py-2 px-2">
    <div class="flex items-center justify-between mb-6">
        {{-- Header Title --}}
        <div>
            <h1 class="text-2xl font-semibold">Break Management</h1>
            <h5 class="text-xs text-gray-500">Start and stop your breaks</h5>
        </div>

        <a href="{{ route('dashboard.attendance.index') }}"
            class="bg-red-400 hover:bg-red-600 text-white px-4 py-2 rounded-md">
            <i class="ti ti-arrow-left mr-2"></i>
            Back to Attendance
        </a>
    </div>

    {{-- Current Break Status --}}
    <div class="bg-white p-6 rounded-lg shadow mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-lg font-semibold text-gray-800">Current Break Status</h3>
                <p class="text-sm text-gray-600 mt-1" id="current-time">{{ now()->format('l, F j, Y \a\t g:i A') }}</p>
            </div>
            <div class="text-right">
                <div class="text-2xl font-bold mb-2" id="break-status">
                    <span class="px-3 py-1 text-sm rounded-full bg-green-100 text-green-600">Not on Break</span>
                </div>
                <p class="text-sm text-gray-600" id="break-timer">--:--:--</p>
            </div>
        </div>
    </div>

    {{-- Break Control --}}
    <div class="bg-white p-6 rounded-lg shadow mb-6">
        <div class="text-center">
            <button id="break-toggle-btn" class="bg-orange-500 hover:bg-orange-600 text-white px-8 py-4 rounded-lg text-lg font-semibold disabled:opacity-50 disabled:cursor-not-allowed">
                <i class="ti ti-coffee mr-2"></i>
                <span id="break-btn-text">Start Break</span>
            </button>
            <p class="text-sm text-gray-600 mt-3" id="break-info">Click to start your break when needed</p>
        </div>

        {{-- Break Location Info --}}
        <div class="mt-6 p-4 bg-orange-50 rounded-lg">
            <h4 class="text-sm font-medium text-orange-800 mb-2">Break Location Tracking</h4>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                <div>
                    <span class="text-orange-600">Latitude:</span>
                    <span id="break-latitude" class="font-mono">-</span>
                </div>
                <div>
                    <span class="text-orange-600">Longitude:</span>
                    <span id="break-longitude" class="font-mono">-</span>
                </div>
                <div>
                    <span class="text-orange-600">Accuracy:</span>
                    <span id="break-accuracy" class="font-mono">-</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Today's Breaks --}}
    <div class="bg-white p-6 rounded-lg shadow mb-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Today's Breaks</h3>
        <div id="breaks-list" class="space-y-3">
            <div class="text-center text-gray-500 py-8">
                <i class="ti ti-coffee text-3xl mb-2"></i>
                <p>No breaks recorded today</p>
            </div>
        </div>

        {{-- Break Summary --}}
        <div class="mt-6 p-4 bg-gray-50 rounded-lg">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-center">
                <div>
                    <p class="text-2xl font-bold text-orange-600" id="total-breaks">0</p>
                    <p class="text-sm text-gray-600">Total Breaks</p>
                </div>
                <div>
                    <p class="text-2xl font-bold text-blue-600" id="total-break-time">0:00</p>
                    <p class="text-sm text-gray-600">Total Break Time</p>
                </div>
                <div>
                    <p class="text-2xl font-bold text-green-600" id="remaining-breaks">2</p>
                    <p class="text-sm text-gray-600">Remaining Today</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Break Rules --}}
    <div class="bg-blue-50 p-6 rounded-lg shadow">
        <h3 class="text-lg font-semibold text-blue-800 mb-4">Break Policy</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h4 class="font-medium text-blue-700 mb-2">Break Duration</h4>
                <ul class="text-sm text-blue-600 space-y-1">
                    <li>• Maximum 2 breaks per day</li>
                    <li>• Each break: 15-60 minutes</li>
                    <li>• Total break time: Maximum 2 hours</li>
                </ul>
            </div>
            <div>
                <h4 class="font-medium text-blue-700 mb-2">Important Notes</h4>
                <ul class="text-sm text-blue-600 space-y-1">
                    <li>• Location is tracked during breaks</li>
                    <li>• Breaks must be within office premises</li>
                    <li>• Unauthorized extended breaks may affect attendance</li>
                </ul>
            </div>
        </div>
    </div>
</div>

{{-- JavaScript for break management --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    const breakToggleBtn = document.getElementById('break-toggle-btn');
    const breakBtnText = document.getElementById('break-btn-text');
    const breakInfo = document.getElementById('break-info');
    const breakStatus = document.getElementById('break-status');
    const breakTimer = document.getElementById('break-timer');
    const breaksList = document.getElementById('breaks-list');
    const totalBreaks = document.getElementById('total-breaks');
    const totalBreakTime = document.getElementById('total-break-time');
    const remainingBreaks = document.getElementById('remaining-breaks');

    let isOnBreak = false;
    let breakStartTime = null;
    let breakTimerInterval = null;
    let todaysBreaks = [];
    const maxBreaksPerDay = 2;
    const maxBreakDuration = 60 * 60 * 1000; // 1 hour in milliseconds

    // Mock data - replace with actual API calls
    const mockTodaysBreaks = [
        {
            startTime: '2025-12-04 12:30:00',
            endTime: '2025-12-04 13:00:00',
            duration: 30,
            location: 'Main Office'
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
                    document.getElementById('break-latitude').textContent = location.latitude.toFixed(6);
                    document.getElementById('break-longitude').textContent = location.longitude.toFixed(6);
                    document.getElementById('break-accuracy').textContent = location.accuracy.toFixed(1) + 'm';

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

    // Update break status display
    function updateBreakStatus() {
        if (isOnBreak) {
            breakStatus.innerHTML = '<span class="px-3 py-1 text-sm rounded-full bg-orange-100 text-orange-600">On Break</span>';
            breakBtnText.textContent = 'End Break';
            breakToggleBtn.classList.remove('bg-orange-500', 'hover:bg-orange-600');
            breakToggleBtn.classList.add('bg-red-500', 'hover:bg-red-600');
            breakInfo.textContent = 'Click to end your current break';
        } else {
            breakStatus.innerHTML = '<span class="px-3 py-1 text-sm rounded-full bg-green-100 text-green-600">Not on Break</span>';
            breakBtnText.textContent = 'Start Break';
            breakToggleBtn.classList.remove('bg-red-500', 'hover:bg-red-600');
            breakToggleBtn.classList.add('bg-orange-500', 'hover:bg-orange-600');
            breakInfo.textContent = 'Click to start your break when needed';
        }
    }

    // Start break timer
    function startBreakTimer() {
        breakTimerInterval = setInterval(() => {
            if (breakStartTime) {
                const elapsed = Date.now() - breakStartTime;
                const hours = Math.floor(elapsed / (1000 * 60 * 60));
                const minutes = Math.floor((elapsed % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((elapsed % (1000 * 60)) / 1000);

                breakTimer.textContent = `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;

                // Check if break exceeds maximum duration
                if (elapsed > maxBreakDuration) {
                    alert('Your break has exceeded the maximum allowed duration. Please end your break.');
                }
            }
        }, 1000);
    }

    // Stop break timer
    function stopBreakTimer() {
        if (breakTimerInterval) {
            clearInterval(breakTimerInterval);
            breakTimerInterval = null;
        }
        breakTimer.textContent = '--:--:--';
    }

    // Add break to list
    function addBreakToList(breakData) {
        const breakItem = document.createElement('div');
        breakItem.className = 'flex items-center justify-between p-3 bg-orange-50 rounded-lg';

        const startTime = new Date(breakData.startTime).toLocaleTimeString();
        const endTime = breakData.endTime ? new Date(breakData.endTime).toLocaleTimeString() : 'Ongoing';
        const duration = breakData.duration ? `${breakData.duration} min` : '-';

        breakItem.innerHTML = `
            <div class="flex items-center">
                <i class="ti ti-coffee text-lg mr-3 text-orange-600"></i>
                <div>
                    <p class="font-medium">Break ${todaysBreaks.length}</p>
                    <p class="text-sm text-gray-600">${startTime} - ${endTime}</p>
                </div>
            </div>
            <div class="text-right text-sm">
                <p class="font-medium">${duration}</p>
                <p class="text-gray-600">${breakData.location}</p>
            </div>
        `;

        // Remove "no breaks" message if it exists
        const noBreaks = breaksList.querySelector('.text-center');
        if (noBreaks) {
            breaksList.innerHTML = '';
        }

        breaksList.appendChild(breakItem);
    }

    // Update break summary
    function updateBreakSummary() {
        const total = todaysBreaks.length;
        const totalMinutes = todaysBreaks.reduce((sum, breakItem) => sum + (breakItem.duration || 0), 0);
        const hours = Math.floor(totalMinutes / 60);
        const minutes = totalMinutes % 60;
        const remaining = maxBreaksPerDay - total;

        totalBreaks.textContent = total;
        totalBreakTime.textContent = `${hours}:${minutes.toString().padStart(2, '0')}`;
        remainingBreaks.textContent = Math.max(0, remaining);
    }

    // Toggle break
    async function toggleBreak() {
        breakToggleBtn.disabled = true;
        breakToggleBtn.innerHTML = '<i class="ti ti-loader animate-spin mr-2"></i> Processing...';

        try {
            // Get location
            const location = await getLocation();

            if (!isOnBreak) {
                // Start break
                if (todaysBreaks.length >= maxBreaksPerDay) {
                    alert('You have reached the maximum number of breaks allowed per day.');
                    return;
                }

                breakStartTime = Date.now();
                isOnBreak = true;
                startBreakTimer();

                // Add break to list
                const newBreak = {
                    startTime: new Date().toISOString(),
                    endTime: null,
                    duration: null,
                    location: 'Main Office' // In real app, use actual location
                };
                todaysBreaks.push(newBreak);
                addBreakToList(newBreak);

                // Simulate API call
                await new Promise(resolve => setTimeout(resolve, 1000));

                alert('Break started successfully!');

            } else {
                // End break
                const breakDuration = Math.round((Date.now() - breakStartTime) / (1000 * 60)); // minutes
                isOnBreak = false;
                stopBreakTimer();

                // Update last break
                const lastBreak = todaysBreaks[todaysBreaks.length - 1];
                lastBreak.endTime = new Date().toISOString();
                lastBreak.duration = breakDuration;

                // Update break in list
                const breakItems = breaksList.querySelectorAll('.flex.items-center.justify-between');
                const lastBreakItem = breakItems[breakItems.length - 1];
                if (lastBreakItem) {
                    const timeElement = lastBreakItem.querySelector('.text-right p.font-medium');
                    if (timeElement) {
                        timeElement.textContent = `${breakDuration} min`;
                    }
                }

                // Simulate API call
                await new Promise(resolve => setTimeout(resolve, 1000));

                alert(`Break ended successfully! Duration: ${breakDuration} minutes`);
            }

            updateBreakStatus();
            updateBreakSummary();

        } catch (error) {
            console.error('Break toggle failed:', error);
            alert('Failed to ' + (isOnBreak ? 'end' : 'start') + ' break. Please ensure location services are enabled.');
        } finally {
            breakToggleBtn.disabled = false;
            breakToggleBtn.innerHTML = `<i class="ti ti-${isOnBreak ? 'player-stop' : 'coffee'} mr-2"></i> <span id="break-btn-text">${isOnBreak ? 'End Break' : 'Start Break'}</span>`;
        }
    }

    // Initialize with existing breaks
    function initialize() {
        todaysBreaks = [...mockTodaysBreaks];
        todaysBreaks.forEach(breakItem => addBreakToList(breakItem));
        updateBreakSummary();
        updateBreakStatus();

        // Get initial location
        getLocation().catch(error => {
            console.warn('Initial location fetch failed:', error);
        });
    }

    // Update current time every minute
    setInterval(() => {
        document.getElementById('current-time').textContent = new Date().toLocaleDateString() + ' at ' + new Date().toLocaleTimeString();
    }, 60000);

    // Event listeners
    breakToggleBtn.addEventListener('click', toggleBreak);

    // Initialize
    initialize();
});
</script>

@endsection