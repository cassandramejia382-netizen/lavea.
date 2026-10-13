@extends('admin.layout', ['title' => $manager->exists ? 'Edit Manager' : 'Add Manager'])
@push('styles')
<link rel="stylesheet" href="{{ asset('css/lavea-settings.css') }}?v={{ filemtime(public_path('css/lavea-settings.css')) }}">
<style>.manager-form-heading{display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;margin-bottom:24px}.manager-form-heading h1{font-size:26px;margin:0}.manager-form-actions{display:flex;align-items:center;gap:10px;flex-wrap:wrap;margin-top:20px}.manager-account-form{max-width:720px}</style>
@endpush
@section('content')
<div class="manager-form-heading"><h1>{{ $manager->exists ? 'Edit Manager Account' : 'Add Manager Account' }}</h1><a class="lavea-back-link" href="{{ route('admin.managers.index') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m12 19-7-7 7-7M5 12h14"/></svg>Back to Managers</a></div>
<section class="settings-card manager-account-form manager-accounts">
<p>This account can sign in to the Manager Dashboard. Ordinary laundry staff are managed in the Staff module.</p>
<form method="POST" action="{{ $manager->exists ? route('admin.managers.update', $manager) : route('admin.managers.store') }}">@csrf @if($manager->exists)@method('PUT')@endif
<div class="form-group"><label for="name">Name</label><input id="name" name="name" value="{{ old('name', $manager->name) }}" maxlength="255" required>@error('name')<div class="field-error">{{ $message }}</div>@enderror</div>
<div class="form-group"><label for="email">Email</label><input id="email" name="email" type="email" value="{{ old('email', $manager->email) }}" maxlength="255" autocomplete="email" required>@error('email')<div class="field-error">{{ $message }}</div>@enderror</div>
<div class="form-group"><label for="phone">Phone Number</label><input id="phone" name="phone" type="tel" value="{{ old('phone', $manager->phone) }}" maxlength="20">@error('phone')<div class="field-error">{{ $message }}</div>@enderror</div>
<div class="form-group"><label for="password">{{ $manager->exists ? 'New Password (optional)' : 'Password' }}</label><input id="password" name="password" type="password" minlength="8" autocomplete="new-password" @required(!$manager->exists)>@if($manager->exists)<small class="settings-note">Leave blank to keep the existing password.</small>@endif @error('password')<div class="field-error">{{ $message }}</div>@enderror</div>
<div class="form-group"><label for="password_confirmation">Confirm Password</label><input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" @required(!$manager->exists)></div>
<div class="manager-form-actions"><button class="btn btn-primary" type="submit">{{ $manager->exists ? 'Save Changes' : 'Create Manager' }}</button><a class="lavea-back-link" href="{{ route('admin.managers.index') }}">Cancel</a></div>
</form>
</section>
@endsection
