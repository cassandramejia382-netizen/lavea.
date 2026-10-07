@extends('customer.layout', ['title' => 'Profile'])

@push('styles')
    <style>
        .customer-profile-page { max-width: 980px; margin: 0 auto; }
        .customer-profile-card { overflow: hidden; padding: 0; border: 1px solid #e5eaf2; border-radius: 16px; background: #fff; box-shadow: 0 12px 34px #1725540a; }
        .customer-profile-form { padding: 28px; }
        .customer-profile-photo-row { display: flex; align-items: center; gap: 22px; margin-bottom: 28px; }
        .customer-profile-photo { position: relative; display: grid; width: 112px; height: 112px; flex: 0 0 112px; place-items: center; overflow: hidden; border: 4px solid #fff; border-radius: 50%; background: linear-gradient(135deg,#eaf0ff,#dce7ff); color: #4169c8; box-shadow: 0 0 0 1px #dce4f1,0 8px 22px #17255414; isolation: isolate; }
        .customer-profile-photo img { position: absolute; inset: 0; display: block; width: 100%; height: 100%; max-width: none; border-radius: 50%; object-fit: cover; object-position: center; }
        .customer-profile-photo img[hidden] { display: none; }
        .customer-profile-photo svg { width: 42px; height: 42px; }
        .customer-profile-photo-copy strong { display: block; margin-bottom: 6px; color: #172554; font-size: 14px; }
        .customer-profile-photo-copy p { margin: 0 0 12px; color: #8792a6; font-size: 12px; }
        .customer-profile-photo-input { position: absolute; width: 1px; height: 1px; overflow: hidden; clip: rect(0,0,0,0); white-space: nowrap; clip-path: inset(50%); }
        .customer-profile-photo-input:focus-visible + .customer-profile-photo-edit { outline: 3px solid #9cb8ff; outline-offset: 3px; }
        .customer-profile-photo-edit { display: inline-flex; align-items: center; gap: 8px; padding: 10px 14px; border: 1px solid #dce4f1; border-radius: 9px; background: #fff; color: #26395e; cursor: pointer; font-size: 13px; font-weight: 700; }
        .customer-profile-photo-edit:hover { border-color: #4169c8; color: #3158b6; background: #f7f9ff; }
        .customer-profile-photo-edit svg { width: 16px; height: 16px; }
        .customer-profile-error { color: #b42335; font-size: 12px; }
        .customer-profile-toast { position: fixed; top: 92px; right: 28px; z-index: 1400; display: flex; align-items: center; gap: 10px; padding: 13px 18px; border: 1px solid #b8ecd2; border-radius: 11px; background: #effcf5; color: #16794b; box-shadow: 0 12px 30px #1725541a; font-size: 13px; font-weight: 700; opacity: 1; transition: opacity .25s ease,transform .25s ease; }
        .customer-profile-toast svg { width: 18px; height: 18px; }
        .customer-profile-toast.is-hidden { opacity: 0; transform: translateY(-8px); pointer-events: none; }
        .customer-profile-footer { display: flex; justify-content: flex-end; margin-top: 24px; padding-top: 20px; border-top: 1px solid #edf0f5; }
        @media(max-width:600px) { .customer-profile-form { padding: 20px; } .customer-profile-photo-row { align-items: flex-start; gap: 16px; } .customer-profile-photo { width: 88px; height: 88px; flex-basis: 88px; } .customer-profile-toast { top: 76px; right: 16px; left: 16px; justify-content: center; } }
    </style>
@endpush

@section('content')
<div class="customer-profile-page">
<div class="page-head"><div><h1>My Profile</h1><div class="muted">Keep your contact and delivery details up to date.</div></div></div>
@if (session('profile_saved'))
    <div class="customer-profile-toast" id="customer-profile-toast" role="status"><i data-lucide="circle-check"></i><span>{{ session('profile_saved') }}</span></div>
@endif
<div class="customer-profile-card">
    @if($customer)
        <form class="customer-profile-form" method="POST" action="{{ route('customer.profile.update') }}" enctype="multipart/form-data">
            @csrf
            @method('PATCH')
            <div class="customer-profile-photo-row">
                <div class="customer-profile-photo" aria-label="Profile photo preview">
                    @if (auth()->user()->profile_photo_path)
                        <img id="customer-profile-photo-preview" src="{{ asset('storage/'.auth()->user()->profile_photo_path) }}" alt="Profile photo">
                    @else
                        <i id="customer-profile-photo-placeholder" data-lucide="user-round"></i>
                        <img id="customer-profile-photo-preview" alt="Profile photo preview" hidden>
                    @endif
                </div>
                <div class="customer-profile-photo-copy">
                    <strong>Profile photo</strong>
                    <p>Choose a clear photo. JPG, PNG, or WebP up to 5 MB.</p>
                    <input class="customer-profile-photo-input" id="customer_profile_photo" name="profile_photo" type="file" accept="image/jpeg,image/png,image/webp">
                    <label class="customer-profile-photo-edit" for="customer_profile_photo"><i data-lucide="camera"></i>Upload or edit photo</label>
                    @error('profile_photo')<span class="customer-profile-error">{{ $message }}</span>@enderror
                </div>
            </div>
            <div class="form-grid">
                <div class="field"><label for="name">Full name</label><input id="name" name="name" value="{{ old('name', auth()->user()->name) }}" maxlength="255" required autocomplete="name">@error('name')<span class="customer-profile-error">{{ $message }}</span>@enderror</div>
                <div class="field"><label for="email">Email</label><input id="email" type="email" value="{{ auth()->user()->email }}" readonly><small class="muted">Change your email in <a href="{{ route('customer.settings') }}#profile-information">Account Settings</a>.</small></div>
                <div class="field"><label for="phone">Phone</label><input id="phone" name="phone" type="tel" value="{{ old('phone', $customer->phone) }}" maxlength="20" required autocomplete="tel">@error('phone')<span class="customer-profile-error">{{ $message }}</span>@enderror</div>
                <div class="field full"><label for="address">Address</label><textarea id="address" name="address" maxlength="255" required autocomplete="street-address">{{ old('address', $customer->address) }}</textarea>@error('address')<span class="customer-profile-error">{{ $message }}</span>@enderror</div>
            </div>
            <div class="customer-profile-footer"><button class="btn" type="submit">Save Changes</button></div>
        </form>
    @else
        <p class="muted">Your customer profile is not linked yet. Please contact the shop to update your details.</p>
    @endif
</div>
</div>
<script>
    const customerProfilePhotoInput = document.getElementById('customer_profile_photo');
    const customerProfilePhotoPreview = document.getElementById('customer-profile-photo-preview');
    const customerProfilePhotoPlaceholder = document.getElementById('customer-profile-photo-placeholder');

    customerProfilePhotoInput?.addEventListener('change', (event) => {
        const [photo] = event.target.files;

        if (!photo) {
            return;
        }

        customerProfilePhotoPreview.src = URL.createObjectURL(photo);
        customerProfilePhotoPreview.hidden = false;
        customerProfilePhotoPlaceholder?.remove();
    });

    const customerProfileToast = document.getElementById('customer-profile-toast');

    if (customerProfileToast) {
        window.setTimeout(() => customerProfileToast.classList.add('is-hidden'), 3000);
    }
</script>
@endsection
