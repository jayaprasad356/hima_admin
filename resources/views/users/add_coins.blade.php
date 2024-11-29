@extends('layouts.admin')

@section('title', 'Add Coins')
@section('content-header', 'Add Coins')
@section('content-actions')
    <a href="{{ route('users.edit', $user->id) }}" class="btn btn-success"><i class="fas fa-back"></i> Back To User</a>
@endsection
@section('content')

    <div class="card">
        <div class="card-body">

            <form action="{{ route('users.store_coins', $user->id) }}" method="POST">
                @csrf

                <div class="form-group">
                    <label for="coins">Coins</label>
                    <input type="number" name="coins" class="form-control @error('coins') is-invalid @enderror"
                           id="coins" placeholder="Enter coins" value="{{ old('coins') }}">
                    @error('coins')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <button class="btn btn-success btn-block btn-lg" type="submit">Add Coins</button>
            </form>
        </div>
    </div>
@endsection
