{{-- Pending toggle system for hide sidebar --}}

<!-- Sidebar -->
<div id="hs-sidebar-header"  class="fixed top-0 start-0 bottom-0 z-60 w-64 bg-white border-e border-gray-200
           transform -translate-x-full md:translate-x-0 transition-transform duration-300"
    role="dialog" tabindex="-1" aria-label="Sidebar">
  <div class="relative flex flex-col h-full max-h-full">
    <!-- Header -->
    <div class="mt-auto p-1 border-y border-gray-200">
      <!-- Account Dropdown -->
      <div class="hs-dropdown [--strategy:absolute] [--auto-close:inside] relative w-full inline-flex">
        <button id="hs-sidebar-header-example-with-dropdown" type="button" class="w-full inline-flex shrink-0 items-center gap-x-2  text-start text-sm text-gray-800 rounded-md hover:bg-gray-100 focus:outline-hidden focus:bg-gray-100 mt-2" aria-haspopup="menu" aria-expanded="false" aria-label="Dropdown">
          <img class="shrink-0  rounded-md ml-8" width="95px" height="40px" src="{{ asset('storage/images/TMS.png') }}" alt="Avatar">
        <!-- <span class="ml-3 font-bold text-lg">  Tailor</span> -->
        </button>
      </div>
      <!-- End Account Dropdown -->
    </div>
    <!-- End Header -->
<!-- Close Button (Mobile Only) -->
<button 
    id="mobile-close-button"
    class="md:hidden absolute top-4 right-4 p-2 text-gray-600 hover:bg-gray-200 rounded-lg focus:outline-none">
    <!-- X Icon -->
    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
         viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M6 18L18 6M6 6l12 12" />
    </svg>
</button>

    <!-- Body -->
    <nav x-data="{ openDropdown: null }" class="h-full overflow-y-auto [&::-webkit-scrollbar]:w-2 [&::-webkit-scrollbar-thumb]:rounded-full [&::-webkit-scrollbar-track]:bg-gray-100 [&::-webkit-scrollbar-thumb]:bg-gray-300 space-y-2 p-4">
      <a href="{{ route('dashboard') }}" class="flex items-center px-4 py-2 rounded hover:bg-gray-100 {{ request()->routeIs('dashboard') ? 'text-green-600' : 'text-black' }}">
        <i class="ti ti-layout-dashboard mr-2 text-xl"></i>
        <span>Dashboard</span>
      </a>
      <a href="{{route('dashboard.orders')}}" class="flex items-center px-4 py-2 rounded hover:bg-gray-100 {{ request()->routeIs('dashboard.orders*') ? 'text-green-600' : 'text-black' }}">
        <i class="ti ti-shopping-cart mr-2 text-xl"></i>
        <span>Orders</span>
      </a>
      <a href="#" class="flex items-center px-4 py-2 rounded hover:bg-gray-100 {{ request()->routeIs('tasks') ? 'text-green-600' : 'text-black' }}">
        <i class="ti ti-checkbox mr-2 text-xl"></i>
        <span>Tasks</span>
      </a>
      <!-- Inventory Dropdown -->
      <div class="space-y-1">
        <button @click="openDropdown = openDropdown==='inventory' ? null : 'inventory'" class="flex items-center w-full px-4 py-2 rounded hover:bg-gray-100 focus:outline-none {{ request()->is('inventory*') ? 'text-green-600' : 'text-black' }}">
          <i class="ti ti-package mr-2 text-xl"></i>
          <span>Inventory</span>
          <svg :class="{'rotate-90': openDropdown==='inventory'}" class="ml-auto h-4 w-4 transition-transform" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7"/></svg>
        </button>
        <div x-show="openDropdown==='inventory'" class="pl-8 space-y-1" x-cloak>
          <a href="#" class="block py-1 hover:text-green-700"><i class="ti ti-plus mr-2"></i>Add Purchase Entry</a>
          <a href="#" class="block py-1 hover:text-green-700"><i class="ti ti-list mr-2"></i>Item List</a>
          <a href="#" class="block py-1 hover:text-green-700"><i class="ti ti-truck mr-2"></i>Supplier</a>
          <a href="#" class="block py-1 hover:text-green-700"><i class="ti ti-trending-up mr-2"></i>Inventory Reports</a>
          <a href="#" class="block py-1 hover:text-green-700"><i class="ti ti-alert-hexagon mr-2"></i>Low Stock Alerts</a>
        </div>
      </div>
      <!-- Staff Dropdown -->
      <div class="space-y-1">
        <button @click="openDropdown = openDropdown==='staff' ? null : 'staff'" class="flex items-center w-full px-4 py-2 rounded hover:bg-gray-100 focus:outline-none {{ request()->is('staff*') ? 'text-green-600' : 'text-black' }}">
          <i class="ti ti-users mr-2 text-xl"></i>
          <span>Staff</span>
          <svg :class="{'rotate-90': openDropdown==='staff'}" class="ml-auto h-4 w-4 transition-transform" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7"/></svg>
        </button>
        <div x-show="openDropdown==='staff'" class="pl-8 space-y-1" x-cloak>
          <a href="{{route('dashboard.staff')}}" class="block py-1 hover:text-green-700
           {{ request()->routeIs('dashboard.staff') ? 'text-green-600 font-semibold' : 'text-black' }}"><i class="ti ti-users mr-2"></i>Staff List</a>
          <a href="{{route('dashboard.staff.create')}}" class="block py-1 hover:text-green-700
          {{request()->routeIs('dashboard.staff.create')?'text-green-600 font-semibold':'text-black'}}"><i class="ti ti-user-plus mr-2"></i>Add new Staff</a>
          <a href="{{route('dashboard.staff.salary')}}" class="block py-1 hover:text-green-700
          {{request()->routeIs('dashboard.staff.salary')?'text-green-600 font-semibold':'text-black'}}"><i class="ti ti-currency-rupee mr-2"></i>Salary Management</a>
        </div>
      </div>
      <!-- Attendance Dropdown -->
      <div class="space-y-1">
        <button @click="openDropdown = openDropdown==='attendance' ? null : 'attendance'" class="flex items-center w-full px-4 py-2 rounded hover:bg-gray-100 focus:outline-none {{ request()->is('attendance*') ? 'text-green-600' : 'text-black' }}">
          <i class="ti ti-calendar mr-2 text-xl"></i>
          <span>Attendance</span>
          <svg :class="{'rotate-90': openDropdown==='attendance'}" class="ml-auto h-4 w-4 transition-transform" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7"/></svg>
        </button>
        <div x-show="openDropdown==='attendance'" class="pl-8 space-y-1" x-cloak>
          <a href="{{ route('dashboard.attendance.index') }}" class="block py-1 hover:text-green-700
          {{request()->routeIs('dashboard.attendance.index')?'text-green-600 font-semibold':'text-black'}}"><i class="ti ti-users mr-2"></i>Staff Attendance</a>
          <a href="{{ route('dashboard.attendance.mark') }}" class="block py-1 hover:text-green-700
          {{request()->routeIs('dashboard.attendance.mark')?'text-green-600 font-semibold':'text-black'}}"><i class="ti ti-checkbox mr-2"></i>Mark Attendance</a>
          <a href="{{ route('dashboard.attendance.date') }}" class="block py-1 hover:text-green-700
          {{request()->routeIs('dashboard.attendance.date')?'text-green-600 font-semibold':'text-black'}}"><i class="ti ti-calendar mr-2"></i>View by Date</a>
          <a href="{{ route('dashboard.attendance.monthly') }}" class="block py-1 hover:text-green-700
          {{request()->routeIs('dashboard.attendance.monthly')?'text-green-600 font-semibold':'text-black'}}"><i class="ti ti-calendar-cog mr-2"></i>Monthly Summary</a>
        </div>
      </div>
      {{-- Roles --}}
      <a href="{{route('dashboard.roles')}}" class="flex items-center px-4 py-2 rounded hover:bg-gray-100 {{ request()->routeIs('dashboard.roles*') ? 'text-green-600' : 'text-black' }}">
        <i class="ti ti-settings mr-2 text-xl"></i>
        <span>Role</span>
      </a>
  {{-- Masters dropdown --}}
<div x-data="{ openDropdown: '{{ request()->is('masters*') ? 'masters' : '' }}' }" class="space-y-1">

    {{-- Main button --}}
    <button 
        @click="openDropdown = openDropdown==='masters' ? null : 'masters'" 
        class="flex items-center w-full px-4 py-2 rounded focus:outline-none 
        {{ request()->is('masters*') ? 'text-green-600 bg-gray-100' : 'text-black hover:bg-gray-100' }}">
        
        <i class="ti ti-database mr-2 text-xl"></i>
        <span>Masters</span>
        <svg :class="{'rotate-90': openDropdown==='masters'}" 
             class="ml-auto h-4 w-4 transition-transform"
             fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
             <path d="M9 5l7 7-7 7"/>
        </svg>
    </button>

    {{-- Submenu --}}
    <div x-show="openDropdown==='masters'" x-cloak class="pl-8 space-y-1">
        
        <a href="{{ route('dashboard.masters') }}"
           class="flex items-center py-2 rounded hover:bg-gray-100
           {{ request()->routeIs('dashboard.masters') ? 'text-green-600 font-semibold' : 'text-black' }}">
           <i class="ti ti-shirt mr-2"></i>
           <span>Garment</span>
        </a>

        <a href="{{ route('dashboard.masters.measurements') }}"
           class="flex items-center py-2 rounded hover:bg-gray-100
           {{ request()->routeIs('dashboard.masters.measurements') ? 'text-green-600 font-semibold' : 'text-black' }}">
           <i class="ti ti-ruler-measure mr-2"></i>
           <span>Measurements</span>
        </a>

        <a href="{{route('dashboard.masters.relations')}}" class="flex items-center py-2 rounded hover:bg-gray-100  {{ request()->routeIs('dashboard.masters.relations') ? 'text-green-600 font-semibold' : 'text-black' }}">
            <i class="ti ti-hierarchy-2 mr-2"></i>
            <span>Relation</span>
        </a>

        <a href="{{route('dashboard.masters.fabrics')}}" class="flex items-center py-2 rounded hover:bg-gray-100  {{ request()->routeIs('dashboard.masters.fabrics') ? 'text-green-600 font-semibold' : 'text-black' }}">
            <i class="ti ti-brand-databricks mr-2"></i>
            <span>Fabric</span>
        </a>
    </div>
</div>


      <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="flex items-center px-4 py-2 rounded hover:bg-gray-100 w-full text-left {{ request()->routeIs('logout') ? 'text-green-600' : 'text-black' }}">
          <i class="ti ti-logout mr-2 text-xl"></i>
          <span>Logout</span>
        </button>
      </form>
     
    </nav>
    <!-- End Body -->
  </div>
</div>
<!-- End Sidebar -->