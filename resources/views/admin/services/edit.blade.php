<!DOCTYPE html>
<html lang="en">

<head>
    @include('partials.theme-assets')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>LAVEA | Edit Service</title>

    <style>
        * {
            box-sizing: border-box;
            font-family: Arial, Helvetica, sans-serif;
        }

        body {
            margin: 0;
            background: #f5f7fb;
            color: #172554;
        }

        .container {
            max-width: 850px;
            margin: 50px auto;
            padding: 0 20px;
        }

        .back {
            color: #4169c8;
            text-decoration: none;
            font-size: 13px;
        }

        h1 {
            margin-top: 20px;
            margin-bottom: 7px;
        }

        .subtitle {
            color: #7a879d;
            font-size: 13px;
            margin-bottom: 25px;
        }

        .card {
            background: white;
            border: 1px solid #e5e9f1;
            border-radius: 12px;
            padding: 30px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 8px;
        }

        input,
        textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #dce2ec;
            border-radius: 8px;
            outline: none;
            font-size: 13px;
        }

        textarea {
            min-height: 120px;
            resize: vertical;
        }

        input:focus,
        textarea:focus {
            border-color: #4169c8;
        }

        .current-image {
            margin-bottom: 12px;
        }

        .current-image img {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 10px;
            border: 1px solid #e1e5ed;
        }

        .error {
            color: #dc3545;
            font-size: 11px;
            margin-top: 5px;
        }

        .buttons {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 25px;
        }

        .cancel {
            background: #eef1f6;
            color: #44526d;
            text-decoration: none;
            padding: 12px 18px;
            border-radius: 8px;
            font-size: 13px;
        }

        .update {
            border: none;
            background: #4169c8;
            color: white;
            padding: 12px 20px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 13px;
        }
    </style>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link rel="stylesheet" href="{{ asset('css/admin-topbar.css') }}">
</head>

<body>

@include('admin.partials.topbar')

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

                @if($service->image)

                    <div class="current-image">

                        <img
                            src="{{ asset('uploads/services/' . $service->image) }}"
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

                <label>Change Image</label>

                <input
                    type="file"
                    name="image"
                    accept=".jpg,.jpeg,.png,.webp"
                >

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

</body>
</html>

