<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Edit Employee') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('success'))
                <div class="rounded-lg bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 px-4 py-3 text-sm text-green-700 dark:text-green-300">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Profile --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
                <form method="POST" action="{{ route('admin.employees.update', $employee) }}" class="space-y-5">
                    @csrf @method('PATCH')

                    <div>
                        <x-input-label for="name" :value="__('Full name')" />
                        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $employee->name)" required />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="email" :value="__('Work email')" />
                        <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $employee->email)" required />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <div class="flex items-center justify-between pt-2">
                        <span class="text-sm">
                            Status:
                            @if ($employee->is_active)
                                <span class="text-green-600 dark:text-green-400 font-medium">Active</span>
                            @else
                                <span class="text-gray-500 font-medium">Inactive</span>
                            @endif
                        </span>
                        <x-primary-button>{{ __('Save Changes') }}</x-primary-button>
                    </div>
                </form>
            </div>

            {{-- Account actions --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 space-y-4">
                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Account actions</h3>

                <div class="flex flex-wrap items-end gap-3">
                    <form method="POST" action="{{ route('admin.employees.reset-password', $employee) }}" class="flex items-end gap-2">
                        @csrf @method('PATCH')
                        <div>
                            <label for="reset_password" class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Reset password (blank = random)</label>
                            <input id="reset_password" name="password" type="text" autocomplete="off"
                                   class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-200 text-sm">
                        </div>
                        <button class="px-4 py-2 bg-gray-600 text-white text-sm font-medium rounded-lg hover:opacity-90 transition">Reset</button>
                    </form>

                    <form method="POST" action="{{ route('admin.employees.deactivate', $employee) }}">
                        @csrf @method('PATCH')
                        <button class="px-4 py-2 bg-alert-amber text-white text-sm font-medium rounded-lg hover:opacity-90 transition">
                            {{ $employee->is_active ? 'Deactivate' : 'Activate' }}
                        </button>
                    </form>

                    <form method="POST" action="{{ route('admin.employees.destroy', $employee) }}"
                          onsubmit="return confirm('Delete this employee permanently?')">
                        @csrf @method('DELETE')
                        <button class="px-4 py-2 bg-error text-white text-sm font-medium rounded-lg hover:opacity-90 transition">Delete</button>
                    </form>
                </div>
                @error('password') <p class="text-sm text-error">{{ $message }}</p> @enderror
            </div>

            <a href="{{ route('admin.employees.index') }}" class="inline-block text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400">&larr; Back to employees</a>
        </div>
    </div>
</x-app-layout>
