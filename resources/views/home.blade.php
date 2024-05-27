{{-- resources/views/home.blade.php --}}

@extends('adminlte::page')

@section('title', 'GeoSnap')

@section('content_header')
    <h1>Dashboard</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-md-12">
            <form action="{{ route('photos.upload') }}" class="dropzone" id="photo-dropzone">
                @csrf
                <div class="dz-message">
                    Drag and drop a photo here or click to upload
                </div>
            </form>
        </div>
    </div>
@stop


@section('js')
    @vite(['resources/js/app.js'])

    <script>

    </script>
@stop
