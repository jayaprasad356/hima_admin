    @extends('layouts.admin')

    @section('title', 'Withdrawals Management')
    @section('content-header', 'Withdrawals Management')
    @section('content-actions')
    @endsection
    @section('css')
        <link rel="stylesheet" href="{{ asset('plugins/sweetalert2/sweetalert2.min.css') }}">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/css/lightbox.min.css">
    @endsection
    @section('content')
    <div class="card">
    <div class="card-body">
    <div class="row mb-4">
    <div class="col-md-8 d-flex align-items-center">
        <!-- Checkbox for Select All -->
        <div class="form-check mr-3">
            <input type="checkbox" class="form-check-input" id="checkAll">
            <label class="form-check-label" for="checkAll">Select All</label>
        </div>
                <!-- Verify Button -->
                <button class="btn btn-primary mr-3" id="verifyButton">Paid</button>
                <button class="btn btn-danger mr-3" id="cancelButton">Cancel</button>
            </div>
            <div class="col-md-4 d-flex flex-column flex-md-row justify-content-md-end align-items-start">
            <!-- Export Withdrawals Button -->
            <form action="{{ route('withdrawals.export') }}" method="GET" class="mt-2 mt-md-0">
                <button type="submit" class="btn btn-success">Export Withdrawals</button>
            </form>

            <!-- Export Users Button -->
            <form action="{{ route('withdrawals.exportUsers') }}" method="GET" class="mt-2 mt-md-0 ml-md-2">
                <button type="submit" class="btn btn-success">Export Users</button>
            </form>
        </div>
       </div>

            <div class="row mb-4">
                <div class="col-md-8">
                    <!-- User Filter Dropdowns -->
                    <form id="user-filter-form" action="{{ route('withdrawals.index') }}" method="GET" class="form-inline">
                        <div class="form-group mr-3">
                            <label for="status-filter" class="mr-2">Filter by Status:</label>
                            <select name="status" id="status-filter" class="form-control">
                                <option value="">All</option>
                                <option value="1" {{ request()->input('status') === '1' ? 'selected' : '' }}>Paid</option>
                                <option value="0" {{ request()->input('status') === '0' ? 'selected' : '' }}>Pending</option>
                                <option value="2" {{ request()->input('status') === '2' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                        </div>
                        <div class="form-group mr-3">
                    <label for="filter-date" class="mr-2">Filter by Date:</label>
                    <input type="date" id="filter-date" name="filter_date" class="form-control" value="{{ request()->input('filter_date') }}">
                </div>
                    </form>
                </div>
                <div class="col-md-4 text-right">
                    <!-- Search Form -->
                    <form id="search-form" action="{{ route('withdrawals.index') }}" method="GET">
                        <div class="input-group">
                            <input type="text" id="search-input" name="search" class="form-control" placeholder="Search by..." autocomplete="off" value="{{ request()->input('search') }}">
                            <div class="input-group-append">
                                <button class="btn btn-primary" type="submit" style="display: none;">Search</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="thead-dark">
                    <tr>
                    <th>Checkbox</th>
                        <th>Actions</th>
                        <th>ID <i class="fas fa-sort"></i></th>
                        <th>Unique Name<i class="fas fa-sort"></i></th>
                        <th>Amount <i class="fas fa-sort"></i></th>
                        <th>Status <i class="fas fa-sort"></i></th>
                        <th>Datetime <i class="fas fa-sort"></i></th>
                        <th>Bank Name<i class="fas fa-sort"></i></th>
                        <th>Branch Name<i class="fas fa-sort"></i></th>
                        <th>Account Number <i class="fas fa-sort"></i></th>
                        <th>Account Holder Name <i class="fas fa-sort"></i></th>
                        <th>IFSC Code <i class="fas fa-sort"></i></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($withdrawals as $withdrawal)
                    <tr>
                    <td><input type="checkbox" class="checkbox" data-id="{{ $withdrawal->id }}"></td>
                        <td>
                        <a href="{{ route('withdrawals.edit', $withdrawal) }}" class="btn btn-primary"><i class="fas fa-edit"></i></a>
                            <button class="btn btn-danger btn-delete" data-url="{{route('withdrawals.destroy', $withdrawal)}}"><i class="fas fa-trash"></i></button>
                        </td>
                        <td>{{$withdrawal->id}}</td>
                        <td>{{ optional($withdrawal->user)->unique_name }}</td> <!-- Display user name safely -->
                        <td>{{$withdrawal->amount}}</td>
                        <td>
                                <span class="
                                    {{ $withdrawal->status == 0 ? 'text-pending' : '' }}
                                    {{ $withdrawal->status == 1 ? 'text-paid' : '' }}
                                    {{ $withdrawal->status == 2 ? 'text-cancelled' : '' }}
                                ">
                                    {{ $withdrawal->status == 0 ? 'Pending' : '' }}
                                    {{ $withdrawal->status == 1 ? 'Paid' : '' }}
                                    {{ $withdrawal->status == 2 ? 'Cancelled' : '' }}
                                </span>
                            </td>
                        <td>{{$withdrawal->datetime}}</td>
                        <td>{{ $withdrawal->user->bankDetails->bank_name ?? 'N/A' }}</td>
                        <td>{{ $withdrawal->user->bankDetails->branch_name ?? 'N/A' }}</td>
                        <td>{{ $withdrawal->user->bankDetails->account_number ?? 'N/A' }}</td>
                        <td>{{ $withdrawal->user->bankDetails->account_holder_name ?? 'N/A' }}</td>
                        <td>{{ $withdrawal->user->bankDetails->ifsc_code ?? 'N/A' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        {{ $withdrawals->appends(request()->query())->links() }}
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
    $('#filter-date').val(queryParams.filter_date || '');

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

    // Handle date filter change
    $('#filter-date').change(function () {
        $('#user-filter-form').submit();  // Automatically submit form when date changes
    });
        let debounceTimer;

    function filterUsers() {
        clearTimeout(debounceTimer);

        debounceTimer = setTimeout(function () {
                let search = $('#search-input').val();
                let status = $('#status-filter').val();
                let filterDate = $('#filter-date').val();

                // Update the URL with search, status, and filter_date
                window.location.search = `search=${encodeURIComponent(search)}&status=${encodeURIComponent(status)}&filter_date=${encodeURIComponent(filterDate)}`;
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

        // Handle Verify Button click
        $('#verifyButton').click(function() {
            var withdrawalIds = [];
            $('.checkbox:checked').each(function() {
                withdrawalIds.push($(this).data('id'));
            });

            if (withdrawalIds.length > 0) {
                // AJAX call to backend
                $.ajax({
                    url: "{{ route('withdrawals.verify') }}",
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        withdrawal_ids: withdrawalIds
                    },
                    success: function(response) {
                        // Handle success response
                        alert('Paid successfully!');
                        location.reload(); // Reload the page or update UI as needed
                    },
                    error: function(xhr, status, error) {
                        // Handle error response
                        console.error(error);
                        alert('Error updating withdrawals. Please try again.');
                    }
                });
            } else {
                alert('Please select at least one withdrawals.');
            }
        });
            // Handle Verify Button click
            $('#cancelButton').click(function() {
            var withdrawalIds = [];
            $('.checkbox:checked').each(function() {
                withdrawalIds.push($(this).data('id'));
            });

            if (withdrawalIds.length > 0) {
                // AJAX call to backend
                $.ajax({
                    url: "{{ route('withdrawals.cancel') }}",
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        withdrawal_ids: withdrawalIds
                    },
                    success: function(response) {
                        // Handle success response
                        alert('cancel successfully!');
                        location.reload(); // Reload the page or update UI as needed
                    },
                    error: function(xhr, status, error) {
                        // Handle error response
                        console.error(error);
                        alert('Error updating withdrawals. Please try again.');
                    }
                });
            } else {
                alert('Please select at least one withdrawals.');
            }
        });
    });
});
        </script>
    @endsection
