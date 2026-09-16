<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>

                    @canany(['view_sites', 'view_buildings', 'view_floors', 'view_location_areas', 'view_asset_categories', 'view_assets'])
                        @php
                            $masterDataActive = request()->routeIs(['sites.*', 'buildings.*', 'floors.*', 'location_areas.*', 'asset_categories.*', 'assets.*']);
                        @endphp
                        <x-dropdown align="left" width="48">
                            <x-slot name="trigger">
                                <button class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium focus:outline-none transition duration-150 ease-in-out {{ $masterDataActive ? 'border-indigo-400 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                                    {{ __('Master Data') }}
                                    <svg class="ms-1 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </x-slot>
                            <x-slot name="content">
                                @can('view_sites')
                                    <x-dropdown-link :href="route('sites.index')" class="{{ request()->routeIs('sites.*') ? 'bg-gray-100 font-semibold' : '' }}">{{ __('Sites') }}</x-dropdown-link>
                                @endcan
                                @can('view_buildings')
                                    <x-dropdown-link :href="route('buildings.index')" class="{{ request()->routeIs('buildings.*') ? 'bg-gray-100 font-semibold' : '' }}">{{ __('Buildings') }}</x-dropdown-link>
                                @endcan
                                @can('view_floors')
                                    <x-dropdown-link :href="route('floors.index')" class="{{ request()->routeIs('floors.*') ? 'bg-gray-100 font-semibold' : '' }}">{{ __('Floors') }}</x-dropdown-link>
                                @endcan
                                @can('view_location_areas')
                                    <x-dropdown-link :href="route('location_areas.index')" class="{{ request()->routeIs('location_areas.*') ? 'bg-gray-100 font-semibold' : '' }}">{{ __('Location Areas') }}</x-dropdown-link>
                                @endcan
                                @can('view_asset_categories')
                                    <x-dropdown-link :href="route('asset_categories.index')" class="{{ request()->routeIs('asset_categories.*') ? 'bg-gray-100 font-semibold' : '' }}">{{ __('Asset Categories') }}</x-dropdown-link>
                                @endcan
                                @can('view_assets')
                                    <x-dropdown-link :href="route('assets.index')" class="{{ request()->routeIs('assets.*') ? 'bg-gray-100 font-semibold' : '' }}">{{ __('Assets') }}</x-dropdown-link>
                                @endcan
                            </x-slot>
                        </x-dropdown>
                    @endcanany

                    @can('view_work_orders')
                        <x-nav-link :href="route('work_orders.index')" :active="request()->routeIs('work_orders.*')">
                            {{ __('Work Orders') }}
                        </x-nav-link>
                    @endcan

                    @can('view_spare_parts')
                        <x-nav-link :href="route('spare_parts.index')" :active="request()->routeIs('spare_parts.*')">
                            {{ __('Spare Parts') }}
                        </x-nav-link>
                    @endcan

                    @canany(['view_users', 'view_roles'])
                        @php
                            $userMgmtActive = request()->routeIs(['users.*', 'roles.*']);
                        @endphp
                        <x-dropdown align="left" width="48">
                            <x-slot name="trigger">
                                <button class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium focus:outline-none transition duration-150 ease-in-out {{ $userMgmtActive ? 'border-indigo-400 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                                    {{ __('Pengguna') }}
                                    <svg class="ms-1 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </x-slot>
                            <x-slot name="content">
                                @can('view_users')
                                    <x-dropdown-link :href="route('users.index')" class="{{ request()->routeIs('users.*') ? 'bg-gray-100 font-semibold' : '' }}">{{ __('Users') }}</x-dropdown-link>
                                @endcan
                                @can('view_roles')
                                    <x-dropdown-link :href="route('roles.index')" class="{{ request()->routeIs('roles.*') ? 'bg-gray-100 font-semibold' : '' }}">{{ __('Roles') }}</x-dropdown-link>
                                @endcan
                            </x-slot>
                        </x-dropdown>
                    @endcanany
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ml-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>
                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault(); this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>

            @can('view_sites')
                <x-responsive-nav-link :href="route('sites.index')" :active="request()->routeIs('sites.*')">
                    {{ __('Sites') }}
                </x-responsive-nav-link>
            @endcan
            @can('view_buildings')
                <x-responsive-nav-link :href="route('buildings.index')" :active="request()->routeIs('buildings.*')">
                    {{ __('Buildings') }}
                </x-responsive-nav-link>
            @endcan
            @can('view_floors')
                <x-responsive-nav-link :href="route('floors.index')" :active="request()->routeIs('floors.*')">
                    {{ __('Floors') }}
                </x-responsive-nav-link>
            @endcan
            @can('view_location_areas')
                <x-responsive-nav-link :href="route('location_areas.index')" :active="request()->routeIs('location_areas.*')">
                    {{ __('Location Areas') }}
                </x-responsive-nav-link>
            @endcan
            @can('view_asset_categories')
                <x-responsive-nav-link :href="route('asset_categories.index')" :active="request()->routeIs('asset_categories.*')">
                    {{ __('Asset Categories') }}
                </x-responsive-nav-link>
            @endcan
            @can('view_assets')
                <x-responsive-nav-link :href="route('assets.index')" :active="request()->routeIs('assets.*')">
                    {{ __('Assets') }}
                </x-responsive-nav-link>
            @endcan
            @can('view_work_orders')
                <x-responsive-nav-link :href="route('work_orders.index')" :active="request()->routeIs('work_orders.*')">
                    {{ __('Work Orders') }}
                </x-responsive-nav-link>
            @endcan
            @can('view_spare_parts')
                <x-responsive-nav-link :href="route('spare_parts.index')" :active="request()->routeIs('spare_parts.*')">
                    {{ __('Spare Parts') }}
                </x-responsive-nav-link>
            @endcan
            @can('view_users')
                <x-responsive-nav-link :href="route('users.index')" :active="request()->routeIs('users.*')">
                    {{ __('Users') }}
                </x-responsive-nav-link>
            @endcan
            @can('view_roles')
                <x-responsive-nav-link :href="route('roles.index')" :active="request()->routeIs('roles.*')">
                    {{ __('Roles') }}
                </x-responsive-nav-link>
            @endcan
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault(); this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
