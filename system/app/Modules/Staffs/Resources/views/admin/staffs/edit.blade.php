<x-layouts.admin :title="__('Edit Staff')">
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <h1 class="heading-4 text-neutral-950">{{ __('Edit Staff') }}</h1>
            <x-ui.button variant="outline" href="{{ route('admin.staffs.index') }}">
                <i class="ph ph-arrow-left"></i> {{ __('Back') }}
            </x-ui.button>
        </div>

        <div class="section-card">
            <form method="POST" action="{{ route('admin.staffs.update', $staff) }}" class="space-y-5 max-w-2xl">
                @csrf
                @method('PUT')
                <x-forms.input :label="__('Name')" name="name" :value="$staff->name" required :placeholder="__('Enter full name')" />
                <x-forms.input :label="__('Email')" name="email" type="email" :value="$staff->email" required :placeholder="__('Enter email address')" />
                <x-forms.input :label="__('Phone')" name="phone" type="tel" :value="$staff->phone" :placeholder="__('Enter phone number')" />
                <x-forms.input :label="__('Password')" name="password" type="password" :placeholder="__('Leave blank to keep current password')" />
                <x-forms.input :label="__('Confirm Password')" name="password_confirmation" type="password" :placeholder="__('Confirm password')" />

                <div class="space-y-2">
                    <label class="block text-sm font-medium text-neutral-700">{{ __('Roles') }}</label>
                    <div class="space-y-2">
                        @foreach($roles as $role)
                            <x-forms.checkbox :label="$role->name" name="roles[]" :value="$role->name" :checked="$staff->hasRole($role->name)" />
                        @endforeach
                    </div>
                    @error('roles')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                <x-forms.toggle :label="__('Active')" name="is_active" :checked="$staff->is_active" />

                <div class="flex items-center gap-3 pt-4 border-t border-neutral-100">
                    <x-forms.submit :label="__('Update Staff')" />
                    <x-ui.button variant="ghost" href="{{ route('admin.staffs.index') }}">{{ __('Cancel') }}</x-ui.button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.admin>
