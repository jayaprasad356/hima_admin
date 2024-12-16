@extends('layouts.admin')

@section('title', 'Update Coins')
@section('content-header', 'Update Coins')
@section('content-actions')
    <a href="{{route('coins.index')}}" class="btn btn-success"><i class="fas fa-back"></i>Back To Coins</a>
@endsection
@section('content')

    <div class="card">
        <div class="card-body">

            <form action="{{ route('coins.update', $coins) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label for="price">Price</label>
                    <input type="number" name="price" class="form-control @error('price') is-invalid @enderror"
                           id="price"
                           placeholder="price" value="{{ old('price', $coins->price) }}">
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
                           placeholder="coins" value="{{ old('coins', $coins->coins) }}">
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
                           placeholder="save" value="{{ old('save', $coins->save) }}">
                    @error('save')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror 
                </div>


                <div class="form-group">
                        <label for="popular">Popular</label>
                        <div class="custom-control custom-switch">
                            <input type="hidden" name="popular" value="0"> <!-- Hidden input to ensure a value is always submitted -->
                            <input type="checkbox" name="popular" class="custom-control-input @error('popular') is-invalid @enderror" id="popular" value="1" {{ old('popular', $users->popular) == '1' ? 'checked' : '' }}>
                            <label class="custom-control-label" for="popular"></label>
                        </div>
                        @error('popular')
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
