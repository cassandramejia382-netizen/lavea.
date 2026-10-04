@extends('customer.layout', ['title' => 'Profile'])
@section('content')
<div class="page-head"><div><h1>My Profile</h1><div class="muted">Keep your contact and delivery details up to date.</div></div></div>
<div class="card">
    @if($customer)
        <form method="POST" action="{{ route('customer.profile.update') }}">
            @csrf
            @method('PATCH')
            <div class="form-grid">
                <div class="field"><label for="name">Name</label><input id="name" name="name" value="{{ old('name', auth()->user()->name) }}" maxlength="255" required autocomplete="name"></div>
                <div class="field"><label for="email">Email</label><input id="email" type="email" value="{{ auth()->user()->email }}" readonly><small class="muted">Contact the shop if you need to change your email.</small></div>
                <div class="field"><label for="phone">Phone</label><input id="phone" name="phone" type="tel" value="{{ old('phone', $customer->phone) }}" maxlength="20" required autocomplete="tel"></div>
                <div class="field full"><label for="address">Address</label><textarea id="address" name="address" maxlength="255" required autocomplete="street-address">{{ old('address', $customer->address) }}</textarea></div>
            </div>
            <button class="btn" type="submit" style="margin-top:20px">Save Changes</button>
        </form>
    @else
        <p class="muted">Your customer profile is not linked yet. Please contact the shop to update your details.</p>
    @endif
</div>
@endsection
