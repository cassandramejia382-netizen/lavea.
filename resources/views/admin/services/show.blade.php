﻿<!DOCTYPE html>
<html lang="en">

<head>
    @include('partials.theme-assets')
    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>LAVEA | Service Details</title>

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
            max-width: 800px;
            margin: 50px auto;
            padding: 0 20px;
        }

        .back {
            color: #4169c8;
            text-decoration: none;
            font-size: 13px;
        }

        .card {
            margin-top: 25px;
            background: white;
            border: 1px solid #e5e9f1;
            border-radius: 12px;
            padding: 30px;
        }

        .image {
            width: 180px;
            height: 180px;
            object-fit: cover;
            border-radius: 12px;
            margin-bottom: 20px;
        }

        .no-image {
            width: 180px;
            height: 180px;
            background: #eef2f8;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #8995aa;
            margin-bottom: 20px;
        }

        h1 {
            margin-bottom: 10px;
        }

        .price {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .description {
            color: #66738b;
            line-height: 1.6;
            margin-bottom: 25px;
        }

        .edit {
            display: inline-block;
            background: #4169c8;
            color: white;
            text-decoration: none;
            padding: 11px 18px;
            border-radius: 8px;
            font-size: 13px;
        }

    </style>

    <script src="https://unpkg.com/lucide@latest"></script>
    <link rel="stylesheet" href="{{ asset('css/admin-topbar.css') }}">
</head>

<body>

@include('admin.partials.topbar')

<div class="container">

    <a
        href="{{ route('admin.services.index') }}"
        class="back"
    >
        â† Back to Services
    </a>


    <div class="card">

        @if($service->image)

            <img
                src="{{ asset('uploads/services/' . $service->image) }}"
                class="image"
                alt="{{ $service->service_name }}"
            >

        @else

            <div class="no-image">
                No Image
            </div>

        @endif


        <h1>
            {{ $service->service_name }}
        </h1>


        <div class="price">
            ₱{{ number_format($service->price, 2) }}
        </div>


        <div class="description">
            {{ $service->description ?: 'No description available.' }}
        </div>


        <a
            href="{{ route('admin.services.edit', $service->id) }}"
            class="edit"
        >
            Edit Service
        </a>

    </div>

</div>

</body>

</html>

