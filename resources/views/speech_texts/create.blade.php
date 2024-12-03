@extends('layouts.admin')

@section('title', 'Create Speech Text')
@section('content-header', 'Create Speech Text')
@section('content-actions')
    <a href="{{route('speech_texts.index')}}" class="btn btn-success"><i class="fas fa-back"></i>Back To Speech Text</a>
@endsection
@section('content')

    <div class="card">
        <div class="card-body">

            <form action="{{ route('speech_texts.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="form-group">
                    <label for="text">Text</label>
                    <input type="text" name="text" class="form-control @error('text') is-invalid @enderror"
                           id="text"
                           placeholder="text" value="{{ old('text') }}">
                    @error('text')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>
                <div class="form-group">
                <br>
                    <label for="language">Language</label>
                    <select name="language" class="form-control @error('language') is-invalid @enderror" id="language">
                        <option value=''>--select--</option>
                        <option value='Hindi' {{ old('language') == 'Hindi' ? 'selected' : '' }}>Hindi</option>
                        <option value='Telugu' {{ old('language') == 'Telugu' ? 'selected' : '' }}>Telugu</option>
                        <option value='Malayalam' {{ old('language') == 'Malayalam' ? 'selected' : '' }}>Malayalam</option>
                        <option value='Kannada' {{ old('language') == 'Kannada' ? 'selected' : '' }}>Kannada</option>
                        <option value='Punjabi' {{ old('language') == 'Punjabi' ? 'selected' : '' }}>Punjabi</option>
                        <option value='Tamil' {{ old('language') == 'Tamil' ? 'selected' : '' }}>Tamil</option>
                    </select>
                    @error('language')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
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
