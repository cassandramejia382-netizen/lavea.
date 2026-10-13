@extends('customer.layout', ['title' => 'My Profile'])
@section('content')
@include('partials.profile-form', ['profileRole' => 'customer', 'profileRoleLabel' => 'Customer Account', 'profilePhone' => $customer?->phone, 'profileAddress' => $customer?->address, 'profileLinked' => $customer !== null])
<style>
.delete-account-panel {
    max-width: 900px;
    margin: 22px auto 0;
    padding: 22px;
    border: 1px solid #f0d5da;
    border-radius: 14px;
    background: #fff;
}
.delete-account-panel h2 {
    margin: 0 0 8px;
    font-size: 16px;
    color: #a73545;
}
.delete-account-panel p {
    margin: 0 0 16px;
    color: #78869d;
    font-size: 13px;
    line-height: 1.6;
}
.delete-account-panel summary {
    color: #a73545;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
}
.delete-account-panel form {
    display: grid;
    gap: 12px;
    max-width: 380px;
    margin-top: 18px;
}
.delete-account-panel input[type="password"] {
    width: 100%;
    padding: 11px;
    border: 1px solid #dfe5ef;
    border-radius: 8px;
    font: inherit;
}
.delete-account-panel label {
    color: #526987;
    font-size: 13px;
}
.delete-account-panel .delete-account-confirm {
    display: flex;
    align-items: flex-start;
    gap: 8px;
}
.delete-account-panel .delete-account-confirm input {
    width: auto;
    flex: none;
}
.delete-account-panel button {
    justify-self: start;
}
html[data-lavea-theme="dark"] .delete-account-panel {
    background: #1f2937;
    border-color: #64748b;
}
</style>
<section id="delete-account-panel" class="delete-account-panel" aria-labelledby="delete-account-title">
    <h2 id="delete-account-title">Delete account</h2>
    <p>Permanently delete your login and profile details. Shop transaction records are retained without your contact details. This cannot be undone.</p>
    <details @if($errors->deleteAccount->any()) open @endif>
        <summary>Delete my account</summary>
        <form method="POST" action="{{ route('customer.profile.destroy') }}" onsubmit="return confirm('Permanently delete your account? This cannot be undone.')">
            @csrf
            @method('DELETE')
            <label for="delete-account-password">Confirm your current password</label>
            <input id="delete-account-password" name="password" type="password" autocomplete="current-password" required>
            @error('password', 'deleteAccount')<span role="alert" class="profile-error">{{ $message }}</span>@enderror
            <label class="delete-account-confirm"><input name="confirm_deletion" type="checkbox" value="1" required>I understand that my account will be permanently deleted.</label>
            @error('confirm_deletion', 'deleteAccount')<span role="alert" class="profile-error">{{ $message }}</span>@enderror
            <button class="btn danger" type="submit">Delete Account</button>
        </form>
    </details>
</section>
@endsection
