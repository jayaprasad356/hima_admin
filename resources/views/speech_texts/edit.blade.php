@extends('layouts.admin')

@section('title', 'Update Speech Text')
@section('content-header', 'Update Speech Text')
@section('content-actions')
    <a href="{{route('speech_texts.index')}}" class="btn btn-success"><i class="fas fa-back"></i>Back To Speech Text</a>
@endsection
@section('content')

    <div class="card">
        <div class="card-body">

            <form action="{{ route('speech_texts.update', $speech_texts) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label for="text">Text</label>
                    <input type="text" name="text" class="form-control @error('text') is-invalid @enderror"
                           id="text"
                           placeholder="text" value="{{ old('text', $speech_texts->text) }}">
                    @error('profession')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror 
                </div>

                <div class="form-group">
                    <br>
                        <label for="language">Trip Type</label>
                        <select name="language" class="form-control @error('language') is-invalid @enderror" id="profession">
                            <option value=''>--select--</option>
                            <option value='Hindi' {{ old('language', $speech_texts->Hindi) == 'Hindi' ? 'selected' : '' }}>Hindi</option>
                            <option value='Telugu' {{ old('language', $speech_texts->language) == 'Telugu' ? 'selected' : '' }}>Telugu</option>
                            <option value='Malayalam' {{ old('language', $speech_texts->language) == 'Malayalam' ? 'selected' : '' }}>Malayalam</option>
                            <option value='Kannada' {{ old('language', $speech_texts->language) == 'Kannada' ? 'selected' : '' }}>Kannada</option>
                            <option value='Punjabi' {{ old('language', $speech_texts->language) == 'Punjabi' ? 'selected' : '' }}>Punjabi</option>
                            <option value='Tamil' {{ old('language', $speech_texts->language) == 'Tamil' ? 'selected' : '' }}>Tamil</option>
                        </select>
                        @error('language')
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
