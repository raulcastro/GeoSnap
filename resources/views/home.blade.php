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

    <div class="row mt-4">
        <div class="col-md-12">
            <div id="photoCarousel" class="carousel slide" data-ride="carousel">
                <div class="carousel-inner" role="listbox">
                    @foreach($photos->chunk(12) as $chunk)
                        <div class="carousel-item {{ $loop->first ? 'active' : '' }}">
                            <div class="row">
                                @foreach($chunk as $photo)
                                    <div class="col-md-1">
                                        <div class="card">
                                            <img src="{{ asset('storage/photos/thumbnails/' . $photo->file_path) }}" class="card-img-top carousel-img">
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
                <a class="carousel-control-prev" href="#photoCarousel" role="button" data-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="sr-only">Previous</span>
                </a>
                <a class="carousel-control-next" href="#photoCarousel" role="button" data-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="sr-only">Next</span>
                </a>
            </div>
        </div>
    </div>
@stop

@section('css')
    <style>
        .carousel-img {
            height: 150px;
            object-fit: cover;
        }
    </style>
@stop

@section('js')
    @vite(['resources/js/app.js'])

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            loadCarousel();

            function loadCarousel() {
                fetch('/photo-list') // Adjust the URL as necessary to fetch your photos
                    .then(response => response.json())
                    .then(data => {
                        const carouselInner = document.getElementById('carousel-inner');
                        carouselInner.innerHTML = '';
                        data.photos.forEach((photo, index) => {
                            const itemDiv = document.createElement('div');
                            itemDiv.classList.add('carousel-item');
                            if (index === 0) {
                                itemDiv.classList.add('active');
                            }

                            const img = document.createElement('img');
                            img.classList.add('d-block', 'w-100');
                            img.src = `/storage/photos/thumbnails/${photo.file_path}`; // Adjust the path as necessary
                            img.alt = `Photo ${index + 1}`;

                            itemDiv.appendChild(img);
                            carouselInner.appendChild(itemDiv);
                        });
                    })
                    .catch(error => console.error('Error loading carousel:', error));
            }
        });
    </script>
@stop
