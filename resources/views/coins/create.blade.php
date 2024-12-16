@extends('layouts.admin')

@section('title', 'Create Coins')
@section('content-header', 'Create Coins')
@section('content-actions')
    <a href="{{route('coins.index')}}" class="btn btn-success"><i class="fas fa-back"></i>Back To Coins</a>
@endsection
@section('content')

    <div class="card">
        <div class="card-body">

            <form action="{{ route('coins.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="form-group">
                    <label for="price">Price</label>
                    <input type="number" name="price" class="form-control @error('price') is-invalid @enderror"
                           id="price"
                           placeholder="price" value="{{ old('price') }}">
                    @error('price')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="coins">Coins</label>
                    <input type="number" name="coins" class="form-control @error('coins') is-invalid @enderror"
                           id="coins"
                           placeholder="coins" value="{{ old('coins') }}">
                    @error('coins')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="save">Save</label>
                    <input type="number" name="save" class="form-control @error('save') is-invalid @enderror"
                           id="save"
                           placeholder="save" value="{{ old('save') }}">
                    @error('save')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="popular">Popular</label>
                    <div class="custom-control custom-switch">
                        <input type="hidden" name="popular" value="0"> <!-- Default value -->
                        <input type="checkbox" name="popular" class="custom-control-input" id="popular" value="1" 
                               {{ old('popular', 0) == 1 ? 'checked' : '' }}>
                        <label class="custom-control-label" for="popular">Enable Popular</label>
                    </div>
                </div>
             
                <button class="btn btn-success btn-block btn-lg" type="submit">Submit</button>
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

        function updateProfileLabel(input) {
            var fileName = input.files[0].name;
            var label = $(input).siblings('.custom-file-label');
            label.text(fileName);
        }
    </script>
@endsection
