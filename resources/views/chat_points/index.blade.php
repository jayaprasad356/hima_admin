@extends('layouts.admin')

@section('title', 'User Reports Management')
@section('content-header', 'User Reports Management')
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
            <div class="col-md-8">
                <div class="col-md-4">
                    <!-- Input for User ID -->
                    <form id="chat_user-id-form" action="{{ route('chat_points.index') }}" method="GET">
                        <div class="input-group">
                            <input type="text" id="chat_user-id-input" name="chat_user_id" class="form-control" placeholder="Enter User ID" autocomplete="off" value="{{ request()->input('chat_user_id') }}">
                        </div>
                    </form>
                </div>
            </div>

            <div class="col-md-4 text-right">
                <form id="search-form" action="{{ route('chat_points.index') }}" method="GET">
                    <div class="input-group">
                        <input type="text" id="search-input" name="search" class="form-control" placeholder="Search by..." autocomplete="off" value="{{ request()->input('search') }}">
                    </div>
                </form>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="thead-dark">
                    <tr>
                        <th>Actions</th>
                        <th>ID <i class="fas fa-sort"></i></th>
                        <th>User ID <i class="fas fa-sort"></i></th>
                        <th>User Name <i class="fas fa-sort"></i></th>
                        <th>User Gender <i class="fas fa-sort"></i></th>
                        <th>Chat User Name <i class="fas fa-sort"></i></th>
                        <th>Points <i class="fas fa-sort"></i></th>
                        <th>DateTime<i class="fas fa-sort"></i></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($chat_points as $chat_point)
                    <tr>
                        <td>
                            <button class="btn btn-danger btn-delete" data-url="{{route('chat_points.destroy', $chat_point)}}"><i class="fas fa-trash"></i></button>
                        </td>
                        <td>{{$chat_point->id}}</td> 
                        <td>{{ optional($chat_point->user)->id }}</td> 
                        <td>{{ optional($chat_point->user)->name }}</td> 
                        <td>{{ optional($chat_point->user)->gender }}</td> 
                        <td>{{ optional($chat_point->chatUser)->name }}</td> 
                        <td>{{$chat_point->points}}</td>
                        <td>{{$chat_point->datetime}}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        {{ $chat_points->appends(request()->query())->links() }}
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

            // Handle search input with debounce
            let debounceTimeout;
            $('#search-input').on('input', function () {
                clearTimeout(debounceTimeout);
                debounceTimeout = setTimeout(function () {
                    filterUsers();
                }, 500); // Adjust delay as needed
            });

            // Automatically submit user ID filter on input change
             // Debounce function to delay execution
        function debounce(func, delay) {
            let timeout;
            return function (...args) {
                clearTimeout(timeout);
                timeout = setTimeout(() => func.apply(this, args), delay);
            };
        }

        // Automatically submit user ID filter on input change with debounce
        $('#chat_user-id-input').on('input', debounce(function () {
            const chatuserId = $(this).val();
            window.location.search = chatuserId ? `chat_user_id=${encodeURIComponent(chatuserId)}` : ''; // Clear if empty
        }, 500)); // 5000 milliseconds = 5 seconds

            function filterUsers() {
                let search = $('#search-input').val();
                window.location.search = search ? `search=${encodeURIComponent(search)}` : ''; // Clear the query if empty
            }

            // Handle delete button click
            $(document).on('click', '.btn-delete', function () {
                const $this = $(this);
                const swalWithBootstrapButtons = Swal.mixin({
                    customClass: {
                        confirmButton: 'btn btn-success',
                        cancelButton: 'btn btn-danger'
                    },
                    buttonsStyling: false
                });

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
                            });
                        });
                    }
                });
            });

            // Handle table sorting
            $('.table th').click(function () {
                const table = $(this).parents('table').eq(0);
                const index = $(this).index();
                const rows = table.find('tr:gt(0)').toArray().sort(comparer(index));
                this.asc = !this.asc;
                if (!this.asc) {
                    rows.reverse();
                }
                for (const row of rows) {
                    table.append(row);
                }
                // Update arrows
                updateArrows(table, index, this.asc);
            });

            function comparer(index) {
                return function (a, b) {
                    const valA = getCellValue(a, index),
                          valB = getCellValue(b, index);
                    return $.isNumeric(valA) && $.isNumeric(valB) ? valA - valB : valA.localeCompare(valB);
                };
            }

            function getCellValue(row, index) {
                return $(row).children('td').eq(index).text();
            }

            function updateArrows(table, index, asc) {
                table.find('.arrow').remove();
                const arrow = asc ? '<i class="fas fa-arrow-up arrow"></i>' : '<i class="fas fa-arrow-down arrow"></i>';
                table.find('th').eq(index).append(arrow);
            }
        });
    </script>
@endsection
