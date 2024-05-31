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
                                    <div class="card photo-card">
                                        <img src="{{ asset('storage/photos/thumbnails/' . $photo->file_path) }}" class="card-img-top carousel-img">
                                        <div class="photo-overlay">
                                            <a href="#" class="delete-photo"><i class="fas fa-trash-alt"></i></a>
                                            <a href="#" class="view-photo"><i class="fas fa-search"></i></a>
                                        </div>
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

    <div class="row mt-4">
        <div class="col-md-12">
            <div id="map" style="height: 500px;"></div>
        </div>
    </div>

    <div class="modal fade" id="photoModal" tabindex="-1" role="dialog" aria-labelledby="photoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="photoModalLabel">Photo Details</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Photo details will be loaded here -->
                </div>
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

        #map {
            width: 100%;
            height: 500px;
        }
        .photo-card {
            position: relative;
        }

        .photo-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            justify-content: center;
            align-items: center;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .photo-card:hover .photo-overlay {
            opacity: 1;
        }

        .photo-overlay .delete-photo {
            position: absolute;
            top: 10px;
            right: 10px;
            color: red;
            font-size: 1.2rem;
        }

        .photo-overlay .view-photo {
            color: white;
            font-size: 2rem;
        }
    </style>
@stop

@section('js')
    @vite(['resources/js/app.js'])
    <script
    src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&callback=initMap&libraries=places&v=weekly"
    async></script>
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

        function initMap() {
            fetch('/last-photo')
                .then(response => response.json())
                .then(data => {
                    const lastPhoto = data.lastPhoto;
                    const mapCenter = lastPhoto ? { lat: parseFloat(lastPhoto.latitude), lng: parseFloat(lastPhoto.longitude) } : { lat: -34.397, lng: 150.644 };

                    const map = new google.maps.Map(document.getElementById('map'), {
                        zoom: 12,
                        center: mapCenter,
                    });

                    fetch('/photo-list')
                        .then(response => response.json())
                        .then(data => {
                            data.photos.forEach(photo => {
                                if (photo.latitude && photo.longitude) {
                                    const marker = new google.maps.Marker({
                                        position: { lat: parseFloat(photo.latitude), lng: parseFloat(photo.longitude) },
                                        map: map,
                                        title: photo.file_path,
                                    });

                                    const infoWindow = new google.maps.InfoWindow({
                                        content: `<img src="/storage/photos/${photo.file_path}" style="width: 100px; height: auto;"><br>${photo.file_path}`
                                    });

                                    marker.addListener('click', () => {
                                        infoWindow.open(map, marker);
                                    });
                                }
                            });
                        })
                        .catch(error => console.error('Error loading photos:', error));
                })
                .catch(error => console.error('Error loading last photo:', error));
        }
    </script>
@stop
