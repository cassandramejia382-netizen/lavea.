<!DOCTYPE html>
<html lang="en">

<head>
    @include('partials.theme-assets')

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>LAVEA | Customer Details</title>

    <script src="https://unpkg.com/lucide@latest"></script>

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

        .customer-header {
            display: flex;
            align-items: center;
            gap: 15px;
            padding-bottom: 25px;
            border-bottom: 1px solid #edf0f5;
        }

        .avatar {
            width: 65px;
            height: 65px;
            background: #e8eefb;
            color: #4169c8;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .avatar svg {
            width: 30px;
        }

        h1 {
            margin: 0 0 5px;
        }

        .email {
            color: #8995aa;
            font-size: 12px;
        }

        .details {
            margin-top: 25px;
        }

        .detail {
            padding: 15px 0;
            border-bottom: 1px solid #edf0f5;
        }

        .label {
            color: #8995aa;
            font-size: 11px;
            margin-bottom: 5px;
        }

        .value {
            color: #263552;
            font-size: 14px;
        }

        .customer-header .lavea-page-date {
            margin-left: auto;
        }

    </style>

    <link rel="stylesheet" href="{{ asset('css/admin-topbar.css') }}">
</head>

<body>

@include('admin.partials.topbar')

<div class="container">

    <a
        href="{{ route('admin.customers.index') }}"
        class="back"
    >
        â† Back to Customers
    </a>


    <div class="card">

        <div class="customer-header">

            <div class="avatar">

                <i data-lucide="user"></i>

            </div>

            <div>

                <h1>
                    {{ $customer->name }}
                </h1>

                <div class="email">
                    {{ $customer->email ?: 'No email' }}
                </div>

            </div>

            @include('admin.partials.current-date')

        </div>


        <div class="details">

            <div class="detail">

                <div class="label">
                    CUSTOMER ID
                </div>

                <div class="value">
                    {{ $customer->id }}
                </div>

            </div>

            <div class="detail">

                <div class="label">
                    PHONE NUMBER
                </div>

                <div class="value">
                    {{ $customer->phone ?: 'No phone provided' }}
                </div>

            </div>


            <div class="detail">

                <div class="label">
                    EMAIL
                </div>

                <div class="value">
                    {{ $customer->email ?: 'No email provided' }}
                </div>

            </div>


            <div class="detail">

                <div class="label">
                    ADDRESS
                </div>

                <div class="value">
                    {{ $customer->address ?: 'No address provided' }}
                </div>

            </div>


            <div class="detail">

                <div class="label">
                    CUSTOMER SINCE
                </div>

                <div class="value">
                    {{ $customer->created_at?->format('F d, Y') }}
                </div>

            </div>


            <div class="detail">

                <div class="label">
                    LAST UPDATED
                </div>

                <div class="value">
                    {{ $customer->updated_at?->format('F d, Y') ?: 'Not available' }}
                </div>

            </div>

        </div>

    </div>

</div>

<script>
    lucide.createIcons();
</script>

</body>

</html>

