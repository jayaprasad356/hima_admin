@extends('layouts.admin')

@section('title', 'Create users')
@section('content-header', 'Create users')
@section('content-actions')
    <a href="{{route('users.index')}}" class="btn btn-success"><i class="fas fa-back"></i>Back To Users</a>
@endsection
@section('content')

    <div class="card">
        <div class="card-body">

            <form action="{{ route('users.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="form-group">
                    <label for="name">Name</label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                           id="name"
                           placeholder="name" value="{{ old('name') }}">
                    @error('name')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>
            
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="text" name="email" class="form-control @error('email') is-invalid @enderror" id="email"
                           placeholder="Email" value="{{ old('email') }}">
                    @error('email')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label for="age">Age</label>
                    <input type="text" name="age" class="form-control @error('age') is-invalid @enderror" id="age"
                           placeholder="age" value="{{ old('age') }}">
                    @error('age')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                 </div>

                <div class="form-group">
                    <label for="gender">Gender</label>
                    <select name="gender" class="form-control @error('gender') is-invalid @enderror" id="gender">
                        <option value=''>--select--</option>
                        <option value='male' {{ old('gender') == 'male' ? 'selected' : '' }}>male</option>
                        <option value='female' {{ old('gender') == 'female' ? 'selected' : '' }}>female</option>
                        <option value='others' {{ old('gender') == 'others' ? 'selected' : '' }}>others</option>
                    </select>
                    @error('gender')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="state">State</label>
                    <input type="text" name="state" class="form-control @error('state') is-invalid @enderror" id="state"
                           placeholder="state" value="{{ old('state') }}">
                    @error('state')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="city">City</label>
                    <input type="text" name="city" class="form-control @error('city') is-invalid @enderror" id="city"
                           placeholder="city" value="{{ old('city') }}">
                    @error('city')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <div class="form-group">
    <label for="profession_id">Profession</label>
    <select name="profession_id" class="form-control @error('profession_id') is-invalid @enderror" id="profession_id">
        <option value=''>--select--</option>
        @foreach($professions as $id => $profession)
            <option value="{{ $id }}" {{ old('profession_id') == $id ? 'selected' : '' }}>
                {{ $profession }}
            </option>
        @endforeach
    </select>
    @error('profession_id')
    <span class="invalid-feedback" role="alert">
        <strong>{{ $message }}</strong>
    </span>
    @enderror
</div>



                <div class="form-group">
    <label for="referred_by">Referred By</label>
    <input type="text" name="referred_by" class="form-control @error('referred_by') is-invalid @enderror" id="referred_by"
           placeholder="referred_by" value="{{ old('referred_by') }}">
    @error('referred_by')
    <span class="invalid-feedback" role="alert">
        <strong>{{ $message }}</strong>
    </span>
    @enderror
</div>

               <div class="form-group">
                    <label for="language">Language</label>
                    <select name="language" class="form-control @error('language') is-invalid @enderror" id="language">
                        <option value=''>--select--</option>
                        <option value='Tamil' {{ old('language') == 'Tamil' ? 'selected' : '' }}>Tamil</option>
                        <option value='Telugu' {{ old('language') == 'Telugu' ? 'selected' : '' }}>Telugu</option>
                        <option value='Kanada' {{ old('language') == 'Kanada' ? 'selected' : '' }}>Kanada</option>
                        <option value='Malayalam' {{ old('language') == 'Malayalam' ? 'selected' : '' }}>Malayalam</option>
                         <option value='Hindi' {{ old('language') == 'Hindi' ? 'selected' : '' }}>Hindi</option>
                    </select>
                    @error('language')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>


                 <div class="form-group">
                    <label for="profile">Profile</label>
                    <div class="custom-file">
                        <input type="file" class="custom-file-input" name="profile" id="profile" onchange="updateProfileLabel(this)">
                        <label class="custom-file-label" for="profile" id="profile-label">Choose File</label>
                    </div>
                    @error('profile')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="introduction">Introduction</label>
                    <input type="text" name="introduction" class="form-control @error('introduction') is-invalid @enderror" id="introduction"
                           placeholder="introduction" value="{{ old('introduction') }}">
                    @error('introduction')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="dummy">Dummy</label>
                    <div class="custom-control custom-switch">
                        <input type="hidden" name="dummy" value="0"> <!-- Hidden input to ensure a value is always submitted -->
                        <input type="checkbox" name="dummy" class="custom-control-input @error('dummy') is-invalid @enderror" id="dummy" value="1" {{ old('dummy') ? 'checked' : '' }}>
                        <label class="custom-control-label" for="dummy"></label>
                    </div>
                    @error('dummy')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="message_notify">Message Notify</label>
                    <div class="custom-control custom-switch">
                        <input type="hidden" name="message_notify" value="0"> <!-- Hidden input to ensure a value is always submitted -->
                        <input type="checkbox" name="message_notify" class="custom-control-input @error('message_notify') is-invalid @enderror" id="message_notify" value="1" {{ old('message_notify') ? 'checked' : '' }}>
                        <label class="custom-control-label" for="message_notify"></label>
                    </div>
                    @error('message_notify')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="add_friend_notify">Add Friend Notify</label>
                    <div class="custom-control custom-switch">
                        <input type="hidden" name="add_friend_notify" value="0"> <!-- Hidden input to ensure a value is always submitted -->
                        <input type="checkbox" name="add_friend_notify" class="custom-control-input @error('add_friend_notify') is-invalid @enderror" id="add_friend_notify" value="1" {{ old('add_friend_notify') ? 'checked' : '' }}>
                        <label class="custom-control-label" for="add_friend_notify"></label>
                    </div>
                    @error('add_friend_notify')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="view_notify">View Notify</label>
                    <div class="custom-control custom-switch">
                        <input type="hidden" name="view_notify" value="0"> <!-- Hidden input to ensure a value is always submitted -->
                        <input type="checkbox" name="view_notify" class="custom-control-input @error('view_notify') is-invalid @enderror" id="view_notify" value="1" {{ old('view_notify') ? 'checked' : '' }}>
                        <label class="custom-control-label" for="view_notify"></label>
                    </div>
                    @error('view_notify')
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
