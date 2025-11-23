@extends('layouts.app')

@section('title', 'Login')
@section('page_title', 'Sign In')

@section('content')
    <div class="bg-white shadow rounded p-6 max-w-md mx-auto">
        <form method="POST" action="{{ route('login.store') }}" class="space-y-4">
            @csrf
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                <input
                    id="email"
                    name="email"
                    type="email"
                    value="{{ old('email') }}"
                    required
                    autofocus
                    class="mt-1 block w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring focus:border-blue-400"
                >
                @error('email')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                <input
                    id="password"
                    name="password"
                    type="password"
                    required
                    class="mt-1 block w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring focus:border-blue-400"
                >
                @error('password')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center">
                <input id="remember" name="remember" type="checkbox" class="h-4 w-4 text-blue-600 border-gray-300 rounded">
                <label for="remember" class="ml-2 block text-sm text-gray-700">Remember me</label>
            </div>

            <button
                type="submit"
                class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700 transition"
            >
                Login
            </button>
        </form>
    </div>
@endsection
