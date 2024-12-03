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
                <button class="btn btn-primary mr-3" id="verifyButton">Approved</button>
                <button class="btn btn-danger mr-3" id="RejectButton">Rejected</button>
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
        <div class="row mb-4">
                <div class="col-md-8">
                    <!-- User Filter Dropdowns -->
                    <form id="user-filter-form" action="{{ route('user_verifications.index') }}" method="GET" class="form-inline">
                        <div class="form-group mr-3">
                            <label for="status-filter" class="mr-2">Filter by Status:</label>
                            <select name="status" id="status-filter" class="form-control">
                                <option value="1" {{ request()->input('status') === '1' ? 'selected' : '' }}>Pending</option>
                                <option value="2" {{ request()->input('status') === '2' ? 'selected' : '' }}>Rejected</option>
                                <option value="3" {{ request()->input('status') === '3' ? 'selected' : '' }}>Verified</option>
                            </select>
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
                        <th>Voice <i class="fas fa-sort"></i></th>
                        <th>Status <i class="fas fa-sort"></i></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $user)
                    <tr>
                        <td><input type="checkbox" class="checkbox" data-id="{{ $user->id }}"></td>
                        <td>{{ $user->id }}</td>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->mobile }}</td>
                        <td><a href="{{ asset('storage/app/public/voices/' . $user->voice) }}" target="_blank">Play Voice</a></td>
                        <td>
                            <span class=" @if($user->status == 1) text-pending @elseif($user->status == 3) text-success @elseif($user->status == 2) text-danger @else text-secondary @endif">
                                @if($user->status == 1) Pending @elseif($user->status == 3) Verified @elseif($user->status == 2) Rejected @else Unknown @endif
                            </span>
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
<script>
  $(document).ready(function () {
    // Function to get URL parameters
    function getQueryParams() {
        const params = {};
        window.location.search.substring(1).split("&").forEach(function (pair) {
            const [key, value] = pair.split("=");
            params[key] = decodeURIComponent(value);
        });
        return params;
    }

    // Load initial parameters
    const queryParams = getQueryParams();
    $('#search-input').val(queryParams.search || '');
    $('#status-filter').val(queryParams.status || '');

    // Handle search input with debounce
    let debounceTimeout;
    $('#search-input').on('input', function () {
        clearTimeout(debounceTimeout);
        debounceTimeout = setTimeout(function () {
            $('#user-filter-form').submit();  // Automatically submit form on search input
        }, 300); // Adjust delay as needed
    });

    // Handle status filter change
    $('#status-filter').change(function () {
        $('#user-filter-form').submit();  // Automatically submit form when status changes
    });
    let debounceTimer;

function filterUsers() {
    clearTimeout(debounceTimer);

    debounceTimer = setTimeout(function () {
            let search = $('#search-input').val();
            let status = $('#status-filter').val();

            // Update the URL with search, status, and filter_date
            window.location.search = `search=${encodeURIComponent(search)}&status=${encodeURIComponent(status)}`;
        }, 500); // Adjust the delay (in milliseconds) as needed
    }
    $('#search-input').on('input', filterUsers);
            $(document).ready(function () {
                $(document).on('click', '.btn-delete', function () {
                    $this = $(this);
                    const swalWithBootstrapButtons = Swal.mixin({
                        customClass: {
                            confirmButton: 'btn btn-success',
                            cancelButton: 'btn btn-danger'
                        },
                        buttonsStyling: false
                    })

                    swalWithBootstrapButtons.fire({
                        title: 'Are you sure?',
                        text: "Do you really want to delete this customer?",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Yes, delete it!',
                        cancelButtonText: 'No',
                        reverseButtons: true
                    }).then((result) => {
                        if (result.value) {
                            $.post($this.data('url'), {_method: 'DELETE', _token: '{{csrf_token()}}'}, function (res) {
                                $this.closest('tr').fadeOut(500, function () {
                                    $(this).remove();
                                })
                            })
                        }
                    })
                })
            });

            $(document).ready(function() {
                $('.table th').click(function() {
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
                    // Update arrows
                    updateArrows(table, index, this.asc);
                });

                function comparer(index) {
                    return function(a, b) {
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
                        alert('Approved successfully!');
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
    });  });
</script>
@endsection

