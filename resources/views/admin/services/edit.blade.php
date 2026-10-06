@extends('admin.layout', ['title' => 'Edit Service'])

@push('styles')

<style>
.lavea-admin-content .container {
            max-width: 850px;
            margin: 50px auto;
            padding: 0 20px;
        }
.lavea-admin-content .back {
            color: #4169c8;
            text-decoration: none;
            font-size: 13px;
        }
.lavea-admin-content h1 {
            margin-top: 20px;
            margin-bottom: 7px;
        }
.lavea-admin-content .subtitle {
            color: #7a879d;
            font-size: 13px;
            margin-bottom: 25px;
        }
.lavea-admin-content .card {
            background: white;
            border: 1px solid #e5e9f1;
            border-radius: 12px;
            padding: 30px;
        }
.lavea-admin-content .form-group {
            margin-bottom: 20px;
        }
.lavea-admin-content label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 8px;
        }
.lavea-admin-content input,
.lavea-admin-content textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #dce2ec;
            border-radius: 8px;
            outline: none;
            font-size: 13px;
        }
.lavea-admin-content textarea {
            min-height: 120px;
            resize: vertical;
        }
.lavea-admin-content input:focus,
.lavea-admin-content textarea:focus {
            border-color: #4169c8;
        }
.lavea-admin-content .current-image {
            margin-bottom: 12px;
        }
.lavea-admin-content .current-image img {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 10px;
            border: 1px solid #e1e5ed;
        }
.lavea-admin-content .error {
            color: #dc3545;
            font-size: 11px;
            margin-top: 5px;
        }
.lavea-admin-content .buttons {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 25px;
        }
.lavea-admin-content .cancel {
            background: #eef1f6;
            color: #44526d;
            text-decoration: none;
            padding: 12px 18px;
            border-radius: 8px;
            font-size: 13px;
        }
.lavea-admin-content .update {
            border: none;
            background: #4169c8;
            color: white;
            padding: 12px 20px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 13px;
        }
</style>
@endpush

@section('content')
<div class="container">

    <a href="{{ route('admin.services.index') }}" class="back">
        â† Back to Services
    </a>

    <h1>Edit Service</h1>

    <p class="subtitle">
        Update the information of this laundry service.
    </p>


    <div class="card">

        <form
            action="{{ route('admin.services.update', $service->id) }}"
            method="POST"
            enctype="multipart/form-data"
        >

            @csrf
            @method('PUT')


            <div class="form-group">

                <label>Service Name</label>

                <input
                    type="text"
                    name="service_name"
                    value="{{ old('service_name', $service->service_name) }}"
                >

                @error('service_name')
                    <div class="error">{{ $message }}</div>
                @enderror

            </div>


            <div class="form-group">

                <label>Price</label>

                <input
                    type="number"
                    name="price"
                    value="{{ old('price', $service->price) }}"
                    step="0.01"
                    min="0"
                >

                @error('price')
                    <div class="error">{{ $message }}</div>
                @enderror

            </div>


            <div class="form-group">

                <label>Description</label>

                <textarea name="description">{{ old('description', $service->description) }}</textarea>

                @error('description')
                    <div class="error">{{ $message }}</div>
                @enderror

            </div>


            <div class="form-group">

                <label>Current Image</label>

                @if($imageUrl = $service->imageUrl())

                    <div class="current-image">

                        <img
                            src="{{ $imageUrl }}"
                            alt="{{ $service->service_name }}"
                        >

                    </div>

                @else

                    <p style="font-size:12px;color:#8995aa;margin-bottom:10px;">
                        No image uploaded.
                    </p>

                @endif

            </div>


            <div class="form-group">

                <label for="image">Change Image</label>

                <input
                    type="file"
                    id="image"
                    name="image"
                    accept=".jpg,.jpeg,.png,.webp"
                    aria-describedby="image-help"
                >
                <small id="image-help">JPG, JPEG, PNG or WEBP. Maximum 2 MB. Leave empty to keep the current image.</small>
                @include('partials.service-image-preview')

                @error('image')
                    <div class="error">{{ $message }}</div>
                @enderror

            </div>


            <div class="buttons">

                <a
                    href="{{ route('admin.services.index') }}"
                    class="cancel"
                >
                    Cancel
                </a>

                <button type="submit" class="update">
                    Update Service
                </button>

            </div>

        </form>

    </div>

</div>
@endsection
