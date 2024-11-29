@extends('layouts.admin')

@section('title', 'Update users')
@section('content-header', 'Update users')
@section('content-actions')
    <a href="{{ route('users.index') }}" class="btn btn-success"><i class="fas fa-back"></i> Back To Users</a>
@endsection

@section('content')

    <div class="card">
        <div class="card-body">

            <form action="{{ route('users.update', $users) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <a href="{{ route('users.add_coins', $users->id) }}" class="btn btn-primary"><i class="fas fa-plus"></i> Add Coins</a>
                <div class="form-group">
                    <br>
                    <label for="name">Name</label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                           id="name"
                           placeholder="Name" value="{{ old('name', $users->name) }}">
                    @error('name')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="mobile">Mobile</label>
                    <input type="number" name="mobile" class="form-control @error('mobile') is-invalid @enderror" id="mobile"
                           placeholder="mobile" value="{{ old('mobile', $users->mobile) }}">
                    @error('mobile')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="language">Language</label>
                    <input type="test" name="language" class="form-control @error('language') is-invalid @enderror" id="language"
                    placeholder="Language" value="{{ old('language', $users->language) }}">
                    @error('language')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>



                <div class="form-group">
                        <label for="avatar_id">Avatar</label>
                        <select name="avatar_id" class="form-control @error('avatar_id') is-invalid @enderror" id="avatar_id">
                            <option value="">--select--</option>
                            @foreach($avatars as $id => $avatar)
                                <option value="{{ $id }}" {{ (old('avatar_id', $users->avatar_id) == $id) ? 'selected' : '' }}>
                                    {{ $id }} - {{ $avatar }} <!-- Display ID and Gender -->
                                </option>
                            @endforeach
                        </select>
                        @error('avatar_id')
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
    const fileInput = document.getElementById('profile');
    const fileInputLabel = fileInput.nextElementSibling;

    fileInput.addEventListener('change', function () {
        const fileName = this.files[0].name;
        fileInputLabel.textContent = fileName;
    });
});
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const fileInput = document.getElementById('cover_img');
    const fileInputLabel = fileInput.nextElementSibling;

    fileInput.addEventListener('change', function () {
        const fileName = this.files[0].name;
        fileInputLabel.textContent = fileName;
    });
});
</script>
@endsection
