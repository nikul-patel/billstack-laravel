<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Billstack')</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50 text-gray-800 min-h-screen">
    <header class="bg-white shadow">
        <div class="container mx-auto px-4 py-4 flex items-center justify-between">
            <div class="text-xl font-semibold text-blue-700">Billstack</div>
            @auth
                <nav class="space-x-4 text-sm font-medium">
                    <a href="{{ route('dashboard') }}" class="text-gray-700 hover:text-blue-600">Dashboard</a>
                    <a href="{{ route('customers.index') }}" class="text-gray-700 hover:text-blue-600">Customers</a>
                    <a href="{{ route('items.index') }}" class="text-gray-700 hover:text-blue-600">Items</a>
                    <a href="{{ route('invoices.index') }}" class="text-gray-700 hover:text-blue-600">Invoices</a>
                    <a href="{{ route('recurring-profiles.index') }}" class="text-gray-700 hover:text-blue-600">Recurring</a>
                </nav>
                <form method="POST" action="{{ route('logout') }}" class="ml-6">
                    @csrf
                    <button class="text-sm text-red-600 hover:text-red-700">Logout</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="text-sm text-blue-600">Login</a>
            @endauth
        </div>
    </header>
    <main class="container mx-auto px-4 py-8">
        <h1 class="text-2xl font-bold mb-4">@yield('page_title', 'Dashboard')</h1>
        @if(session('success'))
            <div class="bg-green-100 text-green-700 p-3 mb-4 rounded">
                {{ session('success') }}
            </div>
        @endif
        @if($errors->any())
            <div class="bg-red-50 text-red-700 p-3 mb-4 rounded">
                <ul class="list-disc ml-5 text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        @yield('content')
    </main>
</body>
</html>
