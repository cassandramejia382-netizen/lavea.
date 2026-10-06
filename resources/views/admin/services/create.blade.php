@extends('admin.layout', ['title' => 'Add Service'])

@push('styles')

<style>
.lavea-admin-content * { margin: 0; padding: 0; }

.lavea-admin-content .page-header {
            margin-bottom: 25px;
        }
.lavea-admin-content .page-header h2 {
            font-size: 26px;
            color: #10204a;
        }
.lavea-admin-content .page-header p {
            color: #7a879d;
            font-size: 13px;
            margin-top: 6px;
        }
.lavea-admin-content .card {
            background: white;
            border: 1px solid #e7ebf2;
            border-radius: 10px;
            padding: 25px;
            max-width: 850px;
        }
.lavea-admin-content .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
.lavea-admin-content .form-group {
            display: flex;
            flex-direction: column;
        }
.lavea-admin-content .form-group.full {
            grid-column: 1 / -1;
        }
.lavea-admin-content label {
            font-size: 12px;
            font-weight: 600;
            color: #44526d;
            margin-bottom: 7px;
        }
.lavea-admin-content input,
.lavea-admin-content textarea {
            width: 100%;
            min-height: 42px;
            border: 1px solid #dfe5ef;
            border-radius: 8px;
            padding: 10px 12px;
            outline: none;
            font-size: 13px;
            color: #172554;
            background: white;
        }
.lavea-admin-content input:focus,
.lavea-admin-content textarea:focus {
            border-color: #4169c8;
        }
.lavea-admin-content textarea {
            min-height: 110px;
            resize: vertical;
        }
.lavea-admin-content .error {
            color: #e05263;
            font-size: 11px;
            margin-top: 5px;
        }
.lavea-admin-content .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 25px;
            padding-top: 20px;
            border-top: 1px solid #edf0f5;
        }
.lavea-admin-content .btn {
            height: 40px;
            padding: 0 17px;
            border-radius: 8px;
            border: none;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            font-size: 13px;
            cursor: pointer;
        }
.lavea-admin-content .btn svg {
            width: 16px;
            height: 16px;
        }
.lavea-admin-content .btn-cancel {
            background: #f1f3f7;
            color: #596579;
        }
.lavea-admin-content .btn-cancel:hover {
            background: #e5e8ee;
        }
.lavea-admin-content .btn-primary {
            background: #4169c8;
            color: white;
        }
.lavea-admin-content .btn-primary:hover {
            background: #3459b1;
        }
@media (max-width: 800px) {
.lavea-admin-content .form-grid {
                grid-template-columns: 1fr;
            }
.lavea-admin-content .form-group.full {
                grid-column: auto;
            }
}
</style>
@endpush

@section('content')


    

    
        

        
            <div class="page-header">
                <h2>Add Service</h2>
                <p>Enter the service information below.</p>
            </div>

            <div class="card">
                <form action="{{ route('admin.services.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="form-grid">
                        <div class="form-group full">
                            <label for="service_name">Service Name</label>
                            <input
                                type="text"
                                id="service_name"
                                name="service_name"
                                value="{{ old('service_name') }}"
                                placeholder="Example: Wash & Fold"
                                required
                            >
                            @error('service_name')
                                <div class="error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="price">Price</label>
                            <input
                                type="number"
                                id="price"
                                name="price"
                                value="{{ old('price') }}"
                                step="0.01"
                                min="0"
                                placeholder="Example: 150.00"
                                required
                            >
                            @error('price')
                                <div class="error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group full">
                            <label for="description">Description</label>
                            <textarea
                                id="description"
                                name="description"
                                placeholder="Enter service description..."
                            >{{ old('description') }}</textarea>
                            @error('description')
                                <div class="error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group full">
                            <label for="image">Service Image</label>
                            <input
                                type="file"
                                id="image"
                                name="image"
                                accept=".jpg,.jpeg,.png,.webp"
                                aria-describedby="image-help"
                            >
                            <small id="image-help">JPG, JPEG, PNG or WEBP. Maximum 2 MB.</small>
                            @include('partials.service-image-preview')
                            @error('image')
                                <div class="error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-actions">
                        <a href="{{ route('admin.services.index') }}" class="btn btn-cancel">Cancel</a>
                        <button type="submit" class="btn btn-primary">
                            <i data-lucide="plus"></i>
                            Save Service
                        </button>
                    </div>
                </form>
            </div>
        
    
@endsection
