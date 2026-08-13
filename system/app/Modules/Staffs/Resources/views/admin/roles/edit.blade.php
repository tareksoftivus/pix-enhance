<x-layouts.admin :title="__('Edit Role')">
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <h1 class="heading-4 text-neutral-950">{{ __('Edit Role') }}</h1>
            <x-ui.button variant="outline" href="{{ route('admin.roles.index') }}">
                <i class="ph ph-arrow-left"></i> {{ __('Back') }}
            </x-ui.button>
        </div>

        <div class="section-card">
            <form method="POST" action="{{ route('admin.roles.update', $role) }}" class="space-y-6 max-w-2xl">
                @csrf
                @method('PUT')
                <x-forms.input :label="__('Role Name')" name="name" :value="$role->name" required :placeholder="__('Enter role name')" />

                <div class="space-y-4">
                    <label class="block text-sm font-medium text-neutral-700">{{ __('Permissions') }}</label>

                    @foreach($permissions as $module => $modulePermissions)
                        <div class="border border-neutral-200 rounded-lg p-4">
                            <h3 class="text-sm font-semibold text-neutral-900 mb-3 capitalize">{{ str_replace('-', ' ', $module) }}</h3>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                @foreach($modulePermissions as $permission)
                                    <x-forms.checkbox
                                        :label="ucfirst(str_replace($module . '.', '', $permission->name))"
                                        name="permissions[]"
                                        :value="$permission->name"
                                        :checked="$role->hasPermissionTo($permission->name)"
                                    />
                                @endforeach
                            </div>
                        </div>
                    @endforeach

                    @error('permissions')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center gap-3 pt-4 border-t border-neutral-100">
                    <x-forms.submit :label="__('Update Role')" />
                    <x-ui.button variant="ghost" href="{{ route('admin.roles.index') }}">{{ __('Cancel') }}</x-ui.button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.admin>