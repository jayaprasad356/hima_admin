
@extends('layouts.admin')

@section('title', 'Trips Management')
@section('content-header', 'Trips Management')
@section('content-actions')
    <a href="{{ route('trips.create') }}" class="btn btn-success"><i class="fas fa-plus"></i> Add New Trip</a>
@endsection

@section('css')
    <link rel="stylesheet" href="{{ asset('plugins/sweetalert2/sweetalert2.min.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/css/lightbox.min.css">
@endsection

@section('content')
<div class="card">
    <div class="card-body">
        <!-- Action Buttons and Search -->
        <div class="row mb-4">
            <!-- Left side (Checkbox and Buttons) -->
            <div class="col-lg-8 col-md-12 d-flex align-items-center flex-wrap">
                <!-- Checkbox for Select All -->
                <div class="form-check mr-3 mb-2 mb-lg-0">
                    <input type="checkbox" class="form-check-input" id="checkAll">
                    <label class="form-check-label" for="checkAll">Select All</label>
                </div>
                
                <!-- Status Buttons -->
                <div class="d-flex flex-wrap align-items-center">
                    <button class="btn btn-primary mr-2 mb-2 mb-lg-0" id="pendingButton">Pending</button>
                    <button class="btn btn-success mr-2 mb-2 mb-lg-0" id="verifyButton">Approved</button>
                    <button class="btn btn-danger mb-2 mb-lg-0" id="cancelButton">Cancelled</button>
                </div>
            </div>
            
            <!-- Right side (Search) -->
            <div class="col-lg-4 col-md-12 mt-3 mt-lg-0">
                <!-- Search Form -->
                <form action="{{ route('trips.index') }}" method="GET">
                    <div class="input-group">
                        <input type="text" id="search-input" name="search" class="form-control" placeholder="Search by..." autocomplete="off" value="{{ request()->input('search') }}">
                        <div class="input-group-append">
                            <button class="btn btn-primary" type="submit" style="display: none;">Search</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Filters -->
        <div class="row mb-4">
            <div class="col-md-12">
                <form id="filter-form" action="{{ route('trips.index') }}" method="GET">
                    <div class="form-row align-items-end">
                        <div class="form-group col-md-4">
                            <label for="trip_status-filter">Filter by Status:</label>
                            <select name="trip_status" id="trip_status-filter" class="form-control">
                                <option value="0" {{ request()->input('trip_status') === '0' ? 'selected' : '' }}>Pending</option>
                                <option value="1" {{ request()->input('trip_status') === '1' ? 'selected' : '' }}>Approved</option>
                                <option value="2" {{ request()->input('trip_status') === '2' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                        </div>

                        <div class="form-group col-md-3">
                            <label for="filter-date">Filter by Date:</label>
                            <input type="date" id="filter-date" name="filter_date" class="form-control" value="{{ request()->input('filter_date') }}">
                        </div>
                    </div>
                </form>
            </div>
        </div>


        <!-- Trips Table -->
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="thead-dark">
                    <tr>
                        <th><input type="checkbox" id="checkAll"></th>
                        <th>Actions</th>
                        <th>ID <i class="fas fa-sort"></i></th>
                        <th>Trip Image</th>
                        <th>User Name <i class="fas fa-sort"></i></th>
                        <th>Trip Type <i class="fas fa-sort"></i></th>
                        <th>Location <i class="fas fa-sort"></i></th>
                        <th>From Date <i class="fas fa-sort"></i></th>
                        <th>To Date <i class="fas fa-sort"></i></th>
                        <th>Trip Title <i class="fas fa-sort"></i></th>
                        <th>Trip Description <i class="fas fa-sort"></i></th>
                        <th>Trip Status</th>
                        <th>Trip DateTime <i class="fas fa-sort"></i></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($trips as $trip)
                        <tr>
                            <td><input type="checkbox" class="checkbox" data-id="{{ $trip->id }}"></td>
                            <td>
                                <a href="{{ route('trips.edit', $trip) }}" class="btn btn-primary"><i class="fas fa-edit"></i></a>
                                <button class="btn btn-danger btn-delete" data-url="{{ route('trips.destroy', $trip) }}"><i class="fas fa-trash"></i></button>
                            </td>
                            <td>{{ $trip->id }}</td>
                            <td>
                                <a href="{{ asset('storage/app/public/trips/' . $trip->trip_image) }}" data-lightbox="trip_image-{{ $trip->id }}">
                                    <img class="customer-img img-thumbnail img-fluid" src="{{ asset('storage/app/public/trips/' . $trip->trip_image) }}" alt="Trip Image" style="max-width: 100px; max-height: 100px;">
                                </a>
                            </td>
                            <td>{{ optional($trip->users)->name }}</td>
                            <td>{{ $trip->trip_type }}</td>
                            <td>{{ $trip->location }}</td>
                            <td>{{ $trip->from_date }}</td>
                            <td>{{ $trip->to_date }}</td>
                            <td>{{ $trip->trip_title }}</td>
                            <td>{{ $trip->trip_description }}</td>
                            <td>
                                @if ($trip->trip_status === 1)
                                    <span class="badge badge-success">Approved</span>
                                @elseif ($trip->trip_status === 0)
                                    <span class="badge badge-primary">Pending</span>
                                @elseif ($trip->trip_status === 2)
                                    <span class="badge badge-danger">Cancelled</span>
                                @endif
                            </td>
                            <td>{{ $trip->trip_datetime }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <!-- Pagination Links -->
        {{ $trips->appends(request()->query())->links() }}
    </div>
</div>
@endsection
@section('js')
<script src="{{ asset('plugins/sweetalert2/sweetalert2.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/js/lightbox.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
        $('#trip_status-filter').val(queryParams.trip_status || '');
        $('#filter-date').val(queryParams.filter_date || '');

        $('#filter-date').change(function () {
            $('#filter-form').submit(); // Automatically submit form when date changes
        })

        // Handle search input
        $('#search-input').on('input', function () {
            filterUsers();
        });

        // Handle status filter change
        $('#trip_status-filter').change(function () {
            filterUsers();
        });
      
        let debounceTimer;

function filterUsers() {
    clearTimeout(debounceTimer);

    debounceTimer = setTimeout(function() {
        let search = $('#search-input').val();
        let verified = $('#trip_status-filter').val();
        let filterDate = $('#filter-date').val();

// Redirect to the same page with the parameters
window.location.search = `search=${encodeURIComponent(search)}&trip_status=${encodeURIComponent(verified)}&filter_date=${encodeURIComponent(filterDate)}`;
    }, 500); // Adjust the delay (in milliseconds) as needed
}
       
           // Handle delete button click
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
                text: "Do you really want to delete this user?",
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
            // Update arrows
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
    });
    </script>

    <script>
        $(document).ready(function () {
            // Handle "Select All" checkbox
            $('#checkAll').change(function () {
                $('.checkbox').prop('checked', $(this).prop('checked'));
            });

            // Handle Pending Button click
            $('#pendingButton').click(function () {
                updateStatus(0); // Status 0 for Pending
            });

            // Handle Approve Button click
            $('#verifyButton').click(function () {
                updateStatus(1); // Status 1 for Approved
            });

            // Handle Cancel Button click
            $('#cancelButton').click(function () {
                updateStatus(2); // Status 2 for Cancelled
            });
        });

        // Function to update status via AJAX
        function updateStatus(status) {
            var tripIds = [];
            $('.checkbox:checked').each(function () {
                tripIds.push($(this).data('id'));
            });

            if (tripIds.length === 0) {
                alert('Please select at least one trip to update status.');
                return;
            }

            $.ajax({
                url: '{{ route("trips.updateStatus") }}',
                type: 'POST',
                data: {
                    trip_ids: tripIds,
                    status: status,
                    _token: '{{ csrf_token() }}'
                },
                success: function (response) {
                    if (response.success) {
                        location.reload();
                    } else {
                        alert('Failed to update status. Please try again.');
                    }
                },
                error: function () {
                    alert('Failed to update status. Please try again.');
                }
            });
        }
    </script>
@endsection
