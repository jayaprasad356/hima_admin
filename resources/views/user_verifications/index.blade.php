@extends('layouts.admin')

@section('title', 'User Verifications Management')
@section('content-header', 'User Verifications Management')
@section('content-actions')
@endsection

@section('css')
    <link rel="stylesheet" href="{{ asset('plugins/sweetalert2/sweetalert2.min.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/css/lightbox.min.css">
@endsection

@section('content')
<div class="card">
    <div class="card-body">
        <!-- Filters and Actions -->
        <div class="row mb-4">
            <div class="col-md-8 d-flex align-items-center">
                <!-- Checkbox for Select All -->
                <div class="form-check mr-3">
                    <input type="checkbox" class="form-check-input" id="checkAll">
                    <label class="form-check-label" for="checkAll">Select All</label>
                </div>

                <!-- Verify Button -->
                <button class="btn btn-primary mr-3" id="verifyButton">Verify</button>
                <button class="btn btn-danger mr-3" id="RejectButton">Reject</button>
            </div>
            <div class="col-md-4 text-md-right mt-3 mt-md-0">
                <!-- Search Form -->
                <form id="search-form" action="{{ route('user_verifications.index') }}" method="GET">
                    <div class="input-group">
                        <input type="text" id="search-input" name="search" class="form-control" placeholder="Search by..." autocomplete="off" value="{{ request()->input('search') }}">
                        <div class="input-group-append">
                         <button class="btn btn-primary" type="submit" style="display: none;">Search</button>
                 </div>
             </div>
        </form>
            </div>
        </div>

        <!-- Table Content -->
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="thead-dark">
                    <tr>
                        <th>Checkbox</th>
                        <th>ID <i class="fas fa-sort"></i></th>
                        <th>User Name <i class="fas fa-sort"></i></th>
                        <th>User Mobile <i class="fas fa-sort"></i></th>
                        <th>Selfie Image <i class="fas fa-sort"></i></th>
                        <th>Proof Image <i class="fas fa-sort"></i></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $user)
                    <tr>
                        <td><input type="checkbox" class="checkbox" data-id="{{ $user->id }}"></td>
                        <td>{{ $user->id }}</td>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->mobile }}</td>

                        <td>
                            <a href="{{ asset('storage/app/public/users/' . $user->selfi_image) }}" data-lightbox="selfi_image-{{ $user->id }}">
                                <img class="customer-img img-thumbnail img-fluid" src="{{ asset('storage/app/public/users/' . $user->selfi_image) }}" alt="" style="max-width: 100px; max-height: 100px;">
                            </a>
                        </td>
                        <td>
                            <a href="{{ asset('storage/app/public/users/' . $user->proof_image) }}" data-lightbox="front_image-{{ $user->id }}">
                                <img class="customer-img img-thumbnail img-fluid" src="{{ asset('storage/app/public/users/' . $user->proof_image) }}" alt="" style="max-width: 100px; max-height: 100px;">
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        {{ $users->appends(request()->query())->links() }}
    </div>
</div>

@endsection

@section('js')
<script src="{{ asset('plugins/sweetalert2/sweetalert2.min.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/js/lightbox.min.js"></script>
@section('js')
<script src="{{ asset('plugins/sweetalert2/sweetalert2.min.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/js/lightbox.min.js"></script>
<script>
    $(document).ready(function () {
        // Function to filter verifications based on search and filters
        function filterVerifications() {
            let search = $('#search-input').val();

            // Construct the URL with query parameters
            let url = `{{ route('user_verifications.index') }}?search=${encodeURIComponent(search)}`;

            // Redirect to the filtered URL
            window.location.href = url;
        }

        let debounceTimer;

        // Debounce function to limit the frequency of function execution
        function debounceFilterVerifications() {
            clearTimeout(debounceTimer);

            debounceTimer = setTimeout(function() {
                filterVerifications();
            }, 500); // Adjust the delay (in milliseconds) as needed
        }

        // Attach event handlers for input and filter changes
        $('#search-input').on('input', debounceFilterVerifications);

        // Handle delete button click
        $(document).on('click', '.btn-delete', function () {
            const $this = $(this);
            Swal.fire({
                title: 'Are you sure?',
                text: "Do you really want to delete this verification?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'No',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    $.post($this.data('url'), {_method: 'DELETE', _token: '{{ csrf_token() }}'}, function () {
                        $this.closest('tr').fadeOut(500, function () {
                            $(this).remove();
                        });
                    });
                }
            });
        });

        // Handle table sorting
        $('.table th').click(function () {
            var table = $(this).parents('table').eq(0);
            var index = $(this).index();
            var rows = table.find('tr:gt(0)').toArray().sort(comparer(index));
            this.asc = !this.asc;
            if (!this.asc) {
                rows = rows.reverse();
            }
            for (var i = 0; i < rows.length; i++) {
                table.append(rows[i]);
            }
            updateArrows(table, index, this.asc);
        });

        function comparer(index) {
            return function (a, b) {
                var valA = getCellValue(a, index),
                    valB = getCellValue(b, index);
                return $.isNumeric(valA) && $.isNumeric(valB) ? valA - valB : valA.localeCompare(valB);
            };
        }

        function getCellValue(row, index) {
            return $(row).children('td').eq(index).text();
        }

        function updateArrows(table, index, asc) {
            table.find('.arrow').remove();
            var arrow = asc ? '<i class="fas fa-arrow-up arrow"></i>' : '<i class="fas fa-arrow-down arrow"></i>';
            table.find('th').eq(index).append(arrow);
        }

        // Handle "Select All" checkbox
        $('#checkAll').change(function() {
            $('.checkbox').prop('checked', $(this).prop('checked'));
        });
            // Handle Reject Button click
            $('#RejectButton').click(function() {
                var verificationIds = [];
                $('.checkbox:checked').each(function() {
                    verificationIds.push($(this).data('id'));
                });

                if (verificationIds.length > 0) {
                    // AJAX call to backend
                    $.ajax({
                        url: "{{ route('user_verifications.reject') }}", // You need to create this route
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            verification_ids: verificationIds
                        },
                        success: function(response) {
                            // Handle success response
                            alert('Rejected successfully!');
                            location.reload(); // Reload the page or update UI as needed
                        },
                        error: function(xhr, status, error) {
                            // Handle error response
                            console.error(error);
                            alert('Error rejecting verifications. Please try again.');
                        }
                    });
                } else {
                    alert('Please select at least one verification.');
                }
            });

        // Handle Verify Button click
        $('#verifyButton').click(function() {
            var verificationIds = [];
            $('.checkbox:checked').each(function() {
                verificationIds.push($(this).data('id'));
            });

            if (verificationIds.length > 0) {
                // AJAX call to backend
                $.ajax({
                    url: "{{ route('user_verifications.verify') }}",
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        verification_ids: verificationIds
                    },
                    success: function(response) {
                        // Handle success response
                        alert('Verified successfully!');
                        location.reload(); // Reload the page or update UI as needed
                    },
                    error: function(xhr, status, error) {
                        // Handle error response
                        console.error(error);
                        alert('Error updating verifications. Please try again.');
                    }
                });
            } else {
                alert('Please select at least one verification.');
            }
            
        });
    });
</script>
@endsection
