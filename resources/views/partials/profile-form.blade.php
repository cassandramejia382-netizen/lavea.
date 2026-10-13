@push('styles')
    <style>
.profile-page {
    max-width: 900px;
    margin: 0 auto;
}
.profile-intro {
    margin-bottom: 25px;
}
.profile-intro h1 {
    margin: 0 0 8px;
    color: #10204a;
    font-size: 28px;
}
.profile-intro p {
    margin: 0;
    color: #7a879d;
}
.profile-card {
    overflow: hidden;
    padding: 0;
    border: 1px solid #e5eaf2;
    border-radius: 16px;
    background: #fff;
    box-shadow: 0 12px 34px #1725540a;
}
.profile-form {
    padding: 0;
}
.profile-section-title {
    display: flex;
    align-items: center;
    gap: 9px;
    margin: 24px 26px 18px;
    color: #172554;
    font-size: 14px;
    font-weight: 700;
}
.profile-section-title svg {
    width: 17px;
    height: 17px;
    color: #4169c8;
}
.profile-photo-copy .profile-display-name {
    margin-bottom: 5px;
    font-size: 19px;
    letter-spacing: -.4px;
    overflow-wrap: anywhere;
}
.profile-account-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    margin-bottom: 14px;
    padding: 4px 9px;
    border: 1px solid #dce6fa;
    border-radius: 6px;
    background: #edf3ff;
    color: #4169c8;
    font-size: 10px;
    font-weight: 600;
}
.profile-account-badge svg {
    width: 12px;
    height: 12px;
}
.profile-toast {
    position: fixed;
    top: 92px;
    right: 28px;
    z-index: 1400;
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 13px 18px;
    border: 1px solid #b8ecd2;
    border-radius: 11px;
    background: #effcf5;
    color: #16794b;
    box-shadow: 0 12px 30px #1725541a;
    font-size: 13px;
    font-weight: 700;
    opacity: 1;
    transform: translateY(0);
    transition: opacity .25s ease,transform .25s ease;
}
.profile-toast svg {
    width: 18px;
    height: 18px;
}
.profile-toast.is-hidden {
    opacity: 0;
    transform: translateY(-8px);
    pointer-events: none;
}
.profile-photo-row {
    display: flex;
    align-items: center;
    gap: 22px;
    margin-bottom: 0;
    padding: 24px 26px;
    border-bottom: 1px solid #e8edf5;
    background: linear-gradient(110deg,#f5f8ff,#fbfcff);
}
.profile-photo {
    position: relative;
    display: grid;
    width: 112px;
    height: 112px;
    flex: 0 0 112px;
    place-items: center;
    overflow: hidden;
    border: 4px solid #fff;
    border-radius: 50%;
    background: linear-gradient(135deg,#eaf0ff,#dce7ff);
    color: #4169c8;
    box-shadow: 0 0 0 1px #dce4f1,0 8px 22px #17255414;
    isolation: isolate;
}
.profile-photo img {
    position: absolute;
    inset: 0;
    display: block;
    width: 100%;
    height: 100%;
    max-width: none;
    border-radius: 50%;
    object-fit: cover;
    object-position: center;
}
.profile-photo img[hidden] {
    display: none;
}
.profile-photo svg {
    width: 42px;
    height: 42px;
}
.profile-photo-edit {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 14px;
    border: 1px solid #dce4f1;
    border-radius: 9px;
    background: #fff;
    color: #26395e;
    cursor: pointer;
    font-size: 13px;
    font-weight: 700;
    transition: .2s ease;
}
.profile-photo-edit:hover {
    border-color: #4169c8;
    color: #3158b6;
    background: #f7f9ff;
}
.profile-photo-edit svg {
    width: 16px;
    height: 16px;
}
.profile-photo-copy strong {
    display: block;
    margin-bottom: 6px;
    color: #172554;
    font-size: 14px;
}
.profile-photo-copy p {
    margin: 0 0 12px;
    color: #8792a6;
    font-size: 12px;
}
.profile-photo-input {
    position: absolute;
    width: 1px;
    height: 1px;
    overflow: hidden;
    clip: rect(0,0,0,0);
    white-space: nowrap;
    clip-path: inset(50%);
}
.profile-photo-input:focus-visible + .profile-photo-edit {
    outline: 3px solid #9cb8ff;
    outline-offset: 3px;
}
.profile-fields {
    display: grid;
    grid-template-columns: repeat(2,minmax(0,1fr));
    gap: 18px;
    margin: 0 26px;
}
.profile-field {
    display: flex;
    flex-direction: column;
    gap: 8px;
}
.profile-field label {
    color: #34415a;
    font-size: 13px;
    font-weight: 700;
}
.profile-field input {
    width: 100%;
    height: 46px;
    padding: 0 13px;
    border: 1px solid #dfe5ef;
    border-radius: 9px;
    background: #fff;
    color: #263550;
    font: inherit;
    transition: border-color .2s,box-shadow .2s;
}
.profile-field textarea {
    width: 100%;
    min-height: 72px;
    padding: 12px 13px;
    border: 1px solid #dfe5ef;
    border-radius: 9px;
    background: #fff;
    color: #263550;
    font: inherit;
    resize: vertical;
    transition: border-color .2s,box-shadow .2s;
}
.profile-field input[readonly] {
    background: #f7f9fc;
    color: #718099;
}
.profile-field input:focus,.profile-field textarea:focus {
    outline: none;
    border-color: #5b7fda;
    box-shadow: 0 0 0 3px #4169c81a;
}
.profile-field small {
    color: #7a879d;
    font-size: 12px;
    line-height: 1.5;
}
.profile-field small a {
    color: #3158b6;
}
.profile-field.full {
    grid-column: 1 / -1;
}
.profile-error {
    color: #b42335;
    font-size: 12px;
}
.profile-form-footer {
    display: flex;
    align-items: center;
    gap: 12px;
    flex-wrap: wrap;
    justify-content: space-between;
    margin-top: 24px;
    padding: 18px 26px;
    border-top: 1px solid #edf0f5;
    background: #fafbfd;
}
.profile-form-footer small {
    color: #8995aa;
    font-size: 11px;
}
.profile-save {
    display: inline-flex;
    align-items: center;
    gap: 9px;
    padding: 12px 18px;
    border: 0;
    border-radius: 9px;
    background: linear-gradient(135deg,#4169c8,#3158b6);
    color: #fff;
    cursor: pointer;
    font-weight: 700;
    box-shadow: 0 6px 14px #4169c833;
    transition: transform .2s,box-shadow .2s;
}
.profile-save:hover {
    transform: translateY(-1px);
    box-shadow: 0 9px 18px #4169c83d;
}
.profile-save svg {
    width: 17px;
    height: 17px;
}
html[data-lavea-theme="dark"] .profile-photo-row,
        html[data-lavea-theme="dark"] .profile-form-footer {
    background: #223454;
    border-color: #34415a;
}
html[data-lavea-theme="dark"] .profile-account-badge {
    background: #1e3a5f;
    color: #bfdbfe;
    border-color: #34415a;
}
html[data-lavea-theme="dark"] .profile-account-badge svg {
    color: inherit !important;
}
@media(max-width:600px) {
    .profile-intro h1 {
        font-size: 24px;
    }
    .profile-toast {
        top: 76px;
        right: 16px;
        left: 16px;
        justify-content: center;
    }
    .profile-photo-row {
        align-items: flex-start;
        gap: 16px;
        padding: 20px;
    }
    .profile-photo {
        width: 80px;
        height: 80px;
        flex-basis: 80px;
    }
    .profile-fields {
        grid-template-columns: 1fr;
        gap: 17px;
        margin: 0 20px;
    }
    .profile-section-title {
        margin: 20px;
    }
    .profile-form-footer {
        padding: 16px 20px;
    }
}
</style>
@endpush

    <div class="profile-page">
        <div class="profile-intro">
            <h1>My Profile</h1>
            <p>Manage your personal information and profile photo.</p>
        </div>

        @if (session('profile_saved'))
            <div class="profile-toast" id="profile-toast" role="status"><i data-lucide="circle-check"></i><span>{{ session('profile_saved') }}</span></div>
        @endif

        @if($profileRole === 'customer')
            <div style="display:flex;justify-content:flex-end;margin-bottom:12px">
                <a class="btn danger" href="#delete-account-panel" onclick="document.querySelector('#delete-account-panel details').open = true" style="font-size:12px;padding:9px 13px">Delete Account</a>
            </div>
        @endif
        @if($profileLinked ?? true)
<section class="profile-card" aria-label="Profile details">
            <form class="profile-form" method="POST" action="{{ route($profileRole.'.profile.update') }}" enctype="multipart/form-data">
                @csrf
                <div class="profile-photo-row">
                    <div class="profile-photo" aria-label="Profile photo preview">
                        @if (auth()->user()->profile_photo_path)
                            <img id="profile-photo-preview" src="{{ asset('storage/'.auth()->user()->profile_photo_path) }}" alt="Profile photo">
                        @else
                            <i id="profile-photo-placeholder" data-lucide="user-round"></i>
                            <img id="profile-photo-preview" alt="Profile photo preview" hidden>
                        @endif
                    </div>
                    <div class="profile-photo-copy">
                        <strong class="profile-display-name">{{ auth()->user()->name }}</strong>
                        <span class="profile-account-badge"><i data-lucide="shield-check" aria-hidden="true"></i>{{ $profileRoleLabel }}</span>
                        <p>Choose a clear photo. JPG, PNG, or WebP up to 5 MB.</p>
                        <input class="profile-photo-input" id="profile_photo" name="profile_photo" type="file" accept="image/jpeg,image/png,image/webp">
                        <label class="profile-photo-edit" for="profile_photo"><i data-lucide="camera"></i>Change photo</label>
                        @error('profile_photo')<span class="profile-error">{{ $message }}</span>@enderror
                    </div>
                </div>

                <div class="profile-section-title"><i data-lucide="user-round" aria-hidden="true"></i>Personal information</div>
                <div class="profile-fields">
                    <div class="profile-field">
                        <label for="profile_name">Full name</label>
                        <input id="profile_name" name="name" type="text" value="{{ old('name', auth()->user()->name) }}" maxlength="255" autocomplete="name" required>
                        @error('name')<span class="profile-error">{{ $message }}</span>@enderror
                    </div>
                    <div class="profile-field">
                        <label for="profile_email">Email</label>
                        <input id="profile_email" type="email" value="{{ auth()->user()->email }}" autocomplete="email" readonly>
                        <small>Change your email in <a href="{{ route($profileRole.'.settings') }}{{ $profileRole === 'admin' ? '#admin-profile' : '#profile-information' }}">Account Settings</a>.</small>
                    </div>
                    <div class="profile-field full">
                        <label for="profile_address">Address</label>
                        <textarea id="profile_address" name="address" maxlength="255" autocomplete="street-address" @if($profileRole === 'customer') required @endif>{{ old('address', $profileAddress) }}</textarea>
                        @error('address')<span class="profile-error">{{ $message }}</span>@enderror
                    </div>
                    <div class="profile-field">
                        <label for="profile_phone">Phone</label>
                        <input id="profile_phone" name="phone" type="tel" value="{{ old('phone', $profilePhone) }}" maxlength="20" autocomplete="tel" @if($profileRole === 'customer') required @endif>
                        @error('phone')<span class="profile-error">{{ $message }}</span>@enderror
                    </div>
                </div>

                <div class="profile-form-footer">
                    <small>Keep your contact details up to date.</small>
                    <button class="profile-save" type="submit"><i data-lucide="save"></i>Save profile</button>
                </div>
            </form>
        </section>
@else
<div class="profile-card" style="padding:24px"><p class="muted">Your customer profile is not linked yet. Please contact the shop to update your details.</p></div>
@endif
    </div>

    <script>
        const profilePhotoInput = document.getElementById('profile_photo');
        const profilePhotoPreview = document.getElementById('profile-photo-preview');
        const profilePhotoPlaceholder = document.getElementById('profile-photo-placeholder');

        profilePhotoInput?.addEventListener('change', (event) => {
            const [photo] = event.target.files;

            if (!photo) {
                return;
            }

            profilePhotoPreview.src = URL.createObjectURL(photo);
            profilePhotoPreview.hidden = false;
            profilePhotoPlaceholder?.remove();
        });

        const profileToast = document.getElementById('profile-toast');

        if (profileToast) {
            window.setTimeout(() => profileToast.classList.add('is-hidden'), 3000);
        }
    </script>
