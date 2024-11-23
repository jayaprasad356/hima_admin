@extends('layouts.admin')

@section('title', 'Update points')
@section('content-header', 'Update points')
@section('content-actions')
    <a href="{{route('points.index')}}" class="btn btn-success"><i class="fas fa-back"></i>Back To points</a>
@endsection
@section('content')

    <div class="card">
        <div class="card-body">

            <form action="{{ route('points.update', $points) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label for="points">Points</label>
                    <input type="number" name="points" class="form-control @error('points') is-invalid @enderror"
                           id="points"
                           placeholder="points" value="{{ old('points', $points->points) }}">
                    @error('points')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror 
                </div>


                <div class="form-group">
                    <label for="offer_percentage">Offer Percentage</label>
                    <input type="number" name="offer_percentage" class="form-control @error('offer_percentage') is-invalid @enderror"
                           id="offer_percentage"
                           placeholder="Offer Percentage" value="{{ old('offer_percentage', $points->offer_percentage) }}">
                    @error('offer_percentage')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror 
                </div>

                <div class="form-group">
                    <label for="price">Price</label>
                    <input type="number" name="price" class="form-control @error('price') is-invalid @enderror"
                           id="price"
                           placeholder="price" value="{{ old('price', $points->price) }}">
                    @error('price')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror 
                </div>

            
                <button class="btn btn-success btn-block btn-lg" type="submit">Save Changes</button>
            </form>
        </div>
    </div>
@endsection

@section('js')
    <script src="{{ asset('plugins/bs-custom-file-input/bs-custom-file-input.min.js') }}"></script>
    <script>
        $(document).ready(function () {
            bsCustomFileInput.init();
        });
    </script>
@endsection
