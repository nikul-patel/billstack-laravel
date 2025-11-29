@extends('layouts.app')

@section('title', 'Create User')
@section('page_title', 'Create User')

@section('content')
    <div class="bg-white shadow rounded p-6 max-w-3xl mx-auto">
        <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-4">
            @csrf
            @include('admin.users.partials.form', ['user' => null])
            <div class="flex justify-between">
                <a href="{{ route('admin.users.index') }}" class="text-blue-600">Back</a>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Create</button>
            </div>
        </form>
    </div>
@endsection
