@extends('admin.layout', ['title' => 'Services'])

@push('styles')

<style>
.lavea-admin-content * { margin: 0; padding: 0; }

.lavea-admin-content .avatar {
            width: 38px;
            height: 38px;
            background: #e8eefb;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
.lavea-admin-content .avatar svg {
            width: 18px;
            height: 18px;
        }
.lavea-admin-content .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }
.lavea-admin-content .page-header h1 {
            font-size: 26px;
            color: #10204a;
        }
.lavea-admin-content .page-header p {
            color: #7a879d;
            font-size: 13px;
            margin-top: 6px;
        }
.lavea-admin-content #services-page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
            margin-bottom: 25px;
        }
.lavea-admin-content #services-page-header h1 {
            font-size: 26px;
            color: #10204a;
        }
.lavea-admin-content #services-page-header p {
            color: #7a879d;
            font-size: 13px;
            margin-top: 6px;
        }
.lavea-admin-content .add-btn {
            background: #4169c8;
            color: white;
            padding: 11px 17px;
            border-radius: 8px;
            text-decoration: none;
            font-size: 13px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
.lavea-admin-content .add-btn:hover {
            background: #3459b1;
        }
.lavea-admin-content .success {
            background: #e6f8ef;
            color: #168458;
            padding: 13px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 13px;
        }
.lavea-admin-content .card {
            background: white;
            border: 1px solid #e7ebf2;
            border-radius: 10px;
            padding: 20px;
        }
.lavea-admin-content .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
.lavea-admin-content .card-header h2 {
            font-size: 19px;
            color: #172554;
        }
.lavea-admin-content .btn {
            border: none;
            border-radius: 8px;
            padding: 10px 14px;
            text-decoration: none;
            cursor: pointer;
            font-size: 13px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }
.lavea-admin-content .btn svg {
            width: 16px;
            height: 16px;
        }
.lavea-admin-content .btn-primary {
            background: #4169c8;
            color: white;
        }
.lavea-admin-content .btn-primary:hover {
            background: #3459b1;
        }
.lavea-admin-content .table-container {
            overflow-x: auto;
        }
.lavea-admin-content table {
            width: 100%;
            border-collapse: collapse;
        }
.lavea-admin-content th {
            background: #f8f9fc;
            color: #718099;
            font-size: 11px;
            text-align: left;
            padding: 13px;
        }
.lavea-admin-content td {
            padding: 14px 13px;
            border-bottom: 1px solid #edf0f5;
            font-size: 12px;
            color: #44526d;
        }
.lavea-admin-content .service-image {
            display: block;
            width: 55px;
            height: 55px;
            border-radius: 8px;
            object-fit: contain;
            object-position: center;
            background: #eef2f8;
        }
.lavea-admin-content .no-image {
            width: 55px;
            height: 55px;
            border-radius: 8px;
            background: #eef2f8;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #8995aa;
        }
.lavea-admin-content .price {
            font-weight: bold;
            color: #172554;
        }
.lavea-admin-content .description {
            max-width: 280px;
        }
.lavea-admin-content .actions {
            display: flex;
            gap: 7px;
        }
.lavea-admin-content .action {
            width: 34px;
            height: 34px;
            border-radius: 7px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
        }
.lavea-admin-content .action svg {
            width: 16px;
        }
.lavea-admin-content .view {
            background: #edf3ff;
            color: #3973e6;
        }
.lavea-admin-content .edit {
            background: #f0eaff;
            color: #7545d0;
        }
.lavea-admin-content .delete {
            background: #fff0f1;
            color: #e05263;
            border: none;
            cursor: pointer;
        }
.lavea-admin-content .empty {
            text-align: center;
            padding: 50px 20px;
            color: #8995aa;
        }
</style>
@endpush

@section('content')


    <!-- SIDEBAR -->



    <!-- MAIN -->







            <div class="page-header" id="services-page-header">
                <div>
                    <h1>Services</h1>
                    <p>Manage laundry services and pricing.</p>
                </div>

                @include('admin.partials.current-date')
            </div>


            <!-- SUCCESS MESSAGE -->
            @if(session('success'))

                <div class="success">
                    {{ session('success') }}
                </div>

            @endif


            <!-- SERVICE TABLE -->
            <div class="card">

                <div class="card-header">
                    <h2>Services List</h2>
                    <a href="{{ route('admin.services.create') }}" class="btn btn-primary">
                        <i data-lucide="plus"></i>
                        Add Service
                    </a>
                </div>

                <div class="table-container">

                    <table>

                        <thead>
                            <tr>
                                <th>Image</th>
                                <th>Service</th>
                                <th>Price</th>
                                <th>Description</th>
                                <th>Actions</th>
                            </tr>
                        </thead>

                        <tbody>

                            @forelse($services as $service)

                                <tr>

                                    <td>

                                        @if($imageUrl = $service->imageUrl())

                                            <a href="{{ route('admin.services.show', $service) }}" aria-label="View {{ $service->service_name }} details">
                                                <img
                                                    src="{{ $imageUrl }}"
                                                    class="service-image"
                                                    alt="{{ $service->service_name }}"
                                                >
                                            </a>

                                        @else

                                            <div class="no-image">
                                                <span style="font-size:10px;text-align:center">No Image</span>
                                            </div>

                                        @endif

                                    </td>

                                    <td>
                                        <a href="{{ route('admin.services.show', $service) }}" style="color:inherit;text-decoration:none">
                                            <strong>{{ $service->service_name }}</strong>
                                        </a>
                                    </td>

                                    <td class="price">
                                        ₱{{ number_format($service->price, 2) }}
                                    </td>

                                    <td class="description">
                                        {{ $service->description ?: 'No description' }}
                                    </td>

                                    <td>

                                        <div class="actions">

                                            <!-- VIEW -->
                                            <a
                                                href="{{ route('admin.services.show', $service->id) }}"
                                                class="action view"
                                                title="View"
                                            >
                                                <i data-lucide="eye"></i>
                                            </a>

                                            <!-- EDIT -->
                                            <a
                                                href="{{ route('admin.services.edit', $service->id) }}"
                                                class="action edit"
                                                title="Edit"
                                            >
                                                <i data-lucide="pencil"></i>
                                            </a>

                                            <!-- DELETE -->
                                            <form
                                                action="{{ route('admin.services.destroy', $service->id) }}"
                                                method="POST"
                                                onsubmit="return confirm('Are you sure you want to delete this service?')"
                                            >

                                                @csrf
                                                @method('DELETE')

                                                <button
                                                    type="submit"
                                                    class="action delete"
                                                    title="Delete"
                                                >
                                                    <i data-lucide="trash-2"></i>
                                                </button>

                                            </form>

                                        </div>

                                    </td>

                                </tr>

                            @empty

                                <tr>
                                    <td colspan="5">

                                        <div class="empty">

                                            <i
                                                data-lucide="package-x"
                                                style="width:40px;height:40px;margin-bottom:10px;"
                                            >
                                            </i>

                                            <p>No services found.</p>

                                        </div>

                                    </td>
                                </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>

            </div>

@endsection
