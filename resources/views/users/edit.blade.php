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
                <a href="{{ route('users.add_points', $users->id) }}" class="btn btn-primary"><i class="fas fa-plus"></i> Add Points</a>
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
                    <label for="unique_name">Unique Name</label>
                    <input type="text" name="unique_name" class="form-control @error('unique_name') is-invalid @enderror"
                           id="unique_name"
                           placeholder="Unique Name" value="{{ old('unique_name', $users->unique_name) }}">
                    @error('unique_name')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="text" name="email" class="form-control @error('email') is-invalid @enderror" id="email"
                           placeholder="Email" value="{{ old('email', $users->email) }}">
                    @error('email')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="age">Age</label>
                    <input type="number" name="age" class="form-control @error('age') is-invalid @enderror"
                           id="age"
                           placeholder="age" value="{{ old('age', $users->age) }}">
                    @error('age')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="gender">Gender</label>
                    <select name="gender" class="form-control @error('gender') is-invalid @enderror" id="gender">
                            <option value='male' {{ old('gender', $users->gender) == 'male' ? 'selected' : '' }}>male</option>
                            <option value='female' {{ old('gender', $users->gender) == 'female' ? 'selected' : '' }}>female</option>
                            <option value='others' {{ old('gender', $users->gender) == 'others' ? 'selected' : '' }}>others</option>
                        </select>
                    @error('gender')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="state">State</label>
                    <input type="text" name="state" class="form-control @error('state') is-invalid @enderror"
                           id="state"
                           placeholder="state" value="{{ old('state', $users->state) }}">
                    @error('state')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>


                <div class="form-group">
                    <label for="city">City</label>
                    <input type="text" name="city" class="form-control @error('city') is-invalid @enderror"
                           id="city"
                           placeholder="city" value="{{ old('city', $users->city) }}">
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
            <option value="{{ $id }}" {{ old('profession_id', $users->profession_id) == $id ? 'selected' : '' }}>
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
                    <label for="language">Language</label>
                    <select name="language" class="form-control @error('language') is-invalid @enderror" id="language">
                            <option value='Tamil' {{ old('language', $users->language) == 'Tamil' ? 'selected' : '' }}>Tamil</option>
                            <option value='Telugu' {{ old('language', $users->language) == 'Telugu' ? 'selected' : '' }}>Telugu</option>
                            <option value='Kanada' {{ old('language', $users->language) == 'Kanada' ? 'selected' : '' }}>Kanada</option>
                            <option value='Malayalam' {{ old('language', $users->language) == 'Malayalam' ? 'selected' : '' }}>Malayalam</option>
                            <option value='Hindi' {{ old('language', $users->language) == 'Hindi' ? 'selected' : '' }}>Hindi</option>
                        </select>
                    @error('language')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="referred_by">Referred By</label>
                    <input type="text" name="referred_by" class="form-control @error('referred_by') is-invalid @enderror"
                           id="referred_by"
                           placeholder="referred_by" value="{{ old('referred_by', $users->referred_by) }}">
                    @error('referred_by')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="refer_code">Refer Code</label>
                    <input type="text" name="refer_code" class="form-control @error('refer_code') is-invalid @enderror"
                           id="refer_code"
                           placeholder="refer_code" value="{{ old('refer_code', $users->refer_code) }}">
                    @error('refer_code')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="points">Points</label>
                    <input type="number" name="points" class="form-control @error('points') is-invalid @enderror"
                           id="points"
                           placeholder="points" value="{{ old('points', $users->points) }}">
                    @error('points')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="total_points">Total Points</label>
                    <input type="number" name="total_points" class="form-control @error('total_points') is-invalid @enderror"
                           id="total_points"
                           placeholder="Total Points" value="{{ old('total_points', $users->total_points) }}">
                    @error('total_points')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <div class="form-group">
                    <span>Current Profile:</span>
                    <img src="{{ asset('storage/app/public/users/' . $users->profile) }}" alt="{{ $users->name }}" style="max-width: 100px; max-height: 100px;">
                    <br>
                    <label for="profile">New Profile</label>
                    <div class="custom-file">
                        <input type="file" class="custom-file-input" name="profile" id="profile">
                        <label class="custom-file-label" for="profile">Choose file</label>
                        @if($users->profile)
                            <input type="hidden" name="existing_profile" value="{{ $users->profile }}">
                        @endif
                    </div>
                    @error('profile')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                    <div class="form-group">
                        <span>Current Cover Image:</span>
                        <img src="{{ asset('storage/app/public/users/' . $users->cover_img) }}" alt="{{ $users->name }}" style="max-width: 100px; max-height: 100px;">
                        <br>
                        <label for="profile">New Cover Image</label>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" name="cover_img" id="cover_img">
                            <label class="custom-file-label" for="cover_img">Choose file</label>
                            @if($users->cover_img)
                                <input type="hidden" name="existing_cover_img" value="{{ $users->cover_img }}">
                            @endif
                        </div>
                        @error('cover_img')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>


                 <div class="form-group">
                    <label for="introduction">Introduction</label>
                    <input type="text" name="introduction" class="form-control @error('introduction') is-invalid @enderror"
                           id="introduction"
                           placeholder="introduction" value="{{ old('introduction', $users->introduction) }}">
                    @error('introduction')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="verification_end_date">Verification End Date</label>
                    <input type="date" name="verification_end_date" class="form-control @error('verification_end_date') is-invalid @enderror"
                           id="verification_end_date"
                           placeholder="Verification End Date" value="{{ old('verification_end_date', $users->verification_end_date) }}">
                    @error('verification_end_date')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                    <div class="form-group">
                        <label for="verified">Verified</label>
                        <div class="custom-control custom-switch">
                            <input type="hidden" name="verified" value="0"> <!-- Hidden input to ensure a value is always submitted -->
                            <input type="checkbox" name="verified" class="custom-control-input @error('verified') is-invalid @enderror" id="verified" value="1" {{ old('verified', $users->verified) == '1' ? 'checked' : '' }}>
                            <label class="custom-control-label" for="verified"></label>
                        </div>
                        @error('verified')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="online_status">Online Status</label>
                        <div class="custom-control custom-switch">
                            <input type="hidden" name="online_status" value="0"> <!-- Hidden input to ensure a value is always submitted -->
                            <input type="checkbox" name="online_status" class="custom-control-input @error('online_status') is-invalid @enderror" id="online_status" value="1" {{ old('online_status', $users->online_status) == '1' ? 'checked' : '' }}>
                            <label class="custom-control-label" for="online_status"></label>
                        </div>
                        @error('online_status')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="dummy">Dummy</label>
                        <div class="custom-control custom-switch">
                            <input type="hidden" name="dummy" value="0"> <!-- Hidden input to ensure a value is always submitted -->
                            <input type="checkbox" name="dummy" class="custom-control-input @error('dummy') is-invalid @enderror" id="dummy" value="1" {{ old('dummy', $users->dummy) == '1' ? 'checked' : '' }}>
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
                            <input type="checkbox" name="message_notify" class="custom-control-input @error('message_notify') is-invalid @enderror" id="message_notify" value="1" {{ old('message_notify', $users->message_notify) == '1' ? 'checked' : '' }}>
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
                            <input type="checkbox" name="add_friend_notify" class="custom-control-input @error('add_friend_notify') is-invalid @enderror" id="add_friend_notify" value="1" {{ old('add_friend_notify', $users->add_friend_notify) == '1' ? 'checked' : '' }}>
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
                            <input type="checkbox" name="view_notify" class="custom-control-input @error('view_notify') is-invalid @enderror" id="view_notify" value="1" {{ old('view_notify', $users->view_notify) == '1' ? 'checked' : '' }}>
                            <label class="custom-control-label" for="view_notify"></label>
                        </div>
                        @error('view_notify')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="profile_verified">Profile Verified</label>
                        <div class="custom-control custom-switch">
                            <input type="hidden" name="profile_verified" value="0"> <!-- Hidden input to ensure a value is always submitted -->
                            <input type="checkbox" name="profile_verified" class="custom-control-input @error('profile_verified') is-invalid @enderror" id="profile_verified" value="1" {{ old('profile_verified', $users->profile_verified) == '1' ? 'checked' : '' }}>
                            <label class="custom-control-label" for="profile_verified"></label>
                        </div>
                        @error('profile_verified')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>


                    <div class="form-group">
                        <label for="cover_img_verified">Cover Image Verified</label>
                        <div class="custom-control custom-switch">
                            <input type="hidden" name="cover_img_verified" value="0"> <!-- Hidden input to ensure a value is always submitted -->
                            <input type="checkbox" name="cover_img_verified" class="custom-control-input @error('cover_img_verified') is-invalid @enderror" id="cover_img_verified" value="1" {{ old('cover_img_verified', $users->cover_img_verified) == '1' ? 'checked' : '' }}>
                            <label class="custom-control-label" for="cover_img_verified"></label>
                        </div>
                        @error('cover_img_verified')
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
