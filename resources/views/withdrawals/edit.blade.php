@extends('layouts.admin')

@section('title', 'Update Withdrawals')
@section('content-header', 'Update Withdrawals')
@section('content-actions')
    <a href="{{route('withdrawals.index')}}" class="btn btn-success"><i class="fas fa-back"></i>Back To Verifications</a>
@endsection
@section('content')

    <div class="card">
        <div class="card-body">

            <form action="{{ route('withdrawals.update', $withdrawal) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')


                    <div class="form-group">
    <label for="status">Status</label>
    <div class="btn-group btn-group-toggle d-block" data-toggle="buttons">
        <label class="btn btn-outline-success {{ old('status', $withdrawal->status) === 1 ? 'active' : '' }}">
            <input type="radio" name="status" id="status" value="1" {{ old('status', $withdrawal->status) === 1 ? 'checked' : '' }}> Paid
        </label>
        <label class="btn btn-outline-primary {{ old('status', $withdrawal->status) === 0 ? 'active' : '' }}">
            <input type="radio" name="status" id="status" value="0" {{ old('status', $withdrawal->status) === 0 ? 'checked' : '' }}> Pending
        </label>
        <label class="btn btn-outline-danger {{ old('status', $withdrawal->status) === 2 ? 'active' : '' }}">
            <input type="radio" name="status" id="status" value="2" {{ old('status', $withdrawal->status) === 2 ? 'checked' : '' }}> Cancelled
        </label>
    </div>
    @error('status')
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
    <script src="{{ asset('plugins/sweetalert2/sweetalert2.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/js/lightbox.min.js"></script>
    <script>
    // Handle delete button click
    $(document).on('click', '.btn-delete', function (e) {
        e.preventDefault(); // Prevent the default behavior

        const $this = $(this);
        Swal.fire({
            title: 'Are you sure?',
            text: "Do you really want to delete this payment image?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'No',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: $this.data('url'),
                    type: 'DELETE',
                    data: {_token: '{{ csrf_token() }}'},
                    success: function (response) {
                        if (response.success) {
                            // Remove the image element and the delete button
                            $this.closest('div').fadeOut(500, function () {
                                $(this).remove();
                            });
                            Swal.fire('Deleted!', response.message, 'success');
                        } else {
                            Swal.fire('Error!', response.message, 'error');
                        }
                    },
                    error: function () {
                        Swal.fire('Error!', 'An error occurred while deleting the image.', 'error');
                    }
                });
            }
        });
    });
</script>

@endsection