@extends('layouts.admin')

@section('title', 'Update Avatar')
@section('content-header', 'Update Avatar')
@section('content-actions')
    <a href="{{route('avatars.index')}}" class="btn btn-success"><i class="fas fa-back"></i>Back To Avatar</a>
@endsection
@section('content')

    <div class="card">
        <div class="card-body">

        <form action="{{ route('avatars.update', $avatars) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="form-group">
                    <br>
                        <label for="gender">Gender</label>
                        <select name="gender" class="form-control @error('gender') is-invalid @enderror" id="gender">
                            <option value='male' {{ old('gender', $avatars->gender) == 'male' ? 'selected' : '' }}>male</option>
                            <option value='female' {{ old('gender', $avatars->gender) == 'female' ? 'selected' : '' }}>female</option>
                        </select>
                        @error('gender')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>

                <div class="form-group">
                    <span>Current Image:</span>
                    <img src="{{ asset('storage/app/public/avatars/' . $avatars->image) }}" alt="{{ $avatars->name }}" style="max-width: 100px; max-height: 100px;">
                    <br>
                    <label for="image">New Image</label>
                    <div class="custom-file">
                        <input type="file" class="custom-file-input" name="image" id="image">
                        <label class="custom-file-label" for="image">Choose file</label>
                        @if($avatars->image)
                            <input type="hidden" name="existing_image" value="{{ $avatars->image }}">
                        @endif
                    </div>
                    @error('image')
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
      <script>
document.addEventListener('DOMContentLoaded', function () {
    const fileInput = document.getElementById('image');
    const fileInputLabel = fileInput.nextElementSibling;

    fileInput.addEventListener('change', function () {
        const fileName = this.files[0].name;
        fileInputLabel.textContent = fileName;
    });
});
</script>
<script src="//cdn.ckeditor.com/4.21.0/full-all/ckeditor.js"></script>
<script>
    // Replace CKEditor for privacy_policy and terms_conditions textareas
    document.addEventListener('DOMContentLoaded', function () {
        CKEDITOR.replace('description', {
            extraPlugins: 'colorbutton'
        });
    });
</script>
@endsection
