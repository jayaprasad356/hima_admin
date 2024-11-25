@extends('layouts.admin')

@section('title', 'Create Avatar')
@section('content-header', 'Create Avatar')
@section('content-actions')
    <a href="{{route('avatars.index')}}" class="btn btn-success"><i class="fas fa-back"></i>Back To Avatar</a>
@endsection
@section('content')

    <div class="card">
        <div class="card-body">

            <form action="{{ route('avatars.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="form-group">
                    <label for="gender">Gender</label>
                    <select name="gender" class="form-control @error('gender') is-invalid @enderror" id="gender">
                        <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>male</option>
                        <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>female</option>
                    </select>
                    @error('gender')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>


                <div class="form-group">
                    <label for="image">Image</label>
                    <div class="custom-file">
                        <input type="file" class="custom-file-input" name="image" id="image" onchange="updateProfileLabel(this)">
                        <label class="custom-file-label" for="image" id="image-label">Choose File</label>
                    </div>
                    @error('image')
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
<script src="//cdn.ckeditor.com/4.21.0/full-all/ckeditor.js"></script>
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
    <script>
    // Replace CKEditor for privacy_policy and terms_conditions textareas
    document.addEventListener('DOMContentLoaded', function () {
        CKEDITOR.replace('description', {
            extraPlugins: 'colorbutton'
        });
     
    });
</script>
@endsection
