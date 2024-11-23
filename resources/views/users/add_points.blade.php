@extends('layouts.admin')

@section('title', 'Add Points')
@section('content-header', 'Add Points')
@section('content-actions')
    <a href="{{ route('users.edit', $user->id) }}" class="btn btn-success"><i class="fas fa-back"></i> Back To User</a>
@endsection
@section('content')

    <div class="card">
        <div class="card-body">

            <form action="{{ route('users.store_points', $user->id) }}" method="POST">
                @csrf

                <div class="form-group">
                    <label for="points">Points</label>
                    <input type="number" name="points" class="form-control @error('points') is-invalid @enderror"
                           id="points" placeholder="Enter points" value="{{ old('points') }}">
                    @error('points')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <button class="btn btn-success btn-block btn-lg" type="submit">Add Points</button>
            </form>
        </div>
    </div>
@endsection
