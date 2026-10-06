@extends('admin.layout', ['title' => 'Service Details'])

@push('styles')

<style>
.lavea-admin-content .container {
            max-width: 800px;
            margin: 50px auto;
            padding: 0 20px;
        }
.lavea-admin-content .back {
            color: #4169c8;
            text-decoration: none;
            font-size: 13px;
        }
.lavea-admin-content .card {
            margin-top: 25px;
            background: white;
            border: 1px solid #e5e9f1;
            border-radius: 12px;
            padding: 30px;
        }
.lavea-admin-content .image {
            width: 180px;
            height: 180px;
            object-fit: cover;
            border-radius: 12px;
            margin-bottom: 20px;
        }
.lavea-admin-content .no-image {
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
.lavea-admin-content h1 {
            margin-bottom: 10px;
        }
.lavea-admin-content .price {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 20px;
        }
.lavea-admin-content .description {
            color: #66738b;
            line-height: 1.6;
            margin-bottom: 25px;
        }
.lavea-admin-content .edit {
            display: inline-block;
            background: #4169c8;
            color: white;
            text-decoration: none;
            padding: 11px 18px;
            border-radius: 8px;
            font-size: 13px;
        }
</style>
@endpush

@section('content')
<div class="container">

    <a
        href="{{ route('admin.services.index') }}"
        class="back"
    >
        â† Back to Services
    </a>


    <div class="card">

        @if($imageUrl = $service->imageUrl())

            <img
                src="{{ $imageUrl }}"
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
@endsection
