@extends('layouts.admin')

@section('title', 'User Management')
@section('content-header', 'User Management')

@section('css')
    <link rel="stylesheet" href="{{ asset('plugins/sweetalert2/sweetalert2.min.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/css/lightbox.min.css">
@endsection

@section('content')
<div class="card">
    <div class="card-body">
        <div class="row mb-4">
            <div class="col-md-8">
                <!-- User Filter Dropdowns -->
            </div>
            <div class="col-md-4 text-right">
                <!-- Search Form -->
                <form id="search-form" action="{{ route('users.index') }}" method="GET">
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
                    <th>Actions</th>
                    <th>ID <i class="fas fa-sort"></i></th>
                    <th>Name <i class="fas fa-sort"></i></th>
                    <th>Mobile <i class="fas fa-sort"></i></th>
                    <th>Language <i class="fas fa-sort"></i></th>
                    <th>Gender<i class="fas fa-sort"></i></th>
                    <th>profile<i class="fas fa-sort"></i></th>
                    <th>Balance <i class="fas fa-sort"></i></th>
                    <th>Coins <i class="fas fa-sort"></i></th>
                    <th>Total Coins <i class="fas fa-sort"></i></th>
                    <th>Status <i class="fas fa-sort"></i></th>
                    <th>DateTime <i class="fas fa-sort"></i></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $user)
                    <tr>
                    <td>
                            <a href="{{ route('users.edit', $user) }}" class="btn btn-primary"><i class="fas fa-edit"></i></a>
                            <button class="btn btn-danger btn-delete" data-url="{{route('users.destroy', $user)}}"><i class="fas fa-trash"></i></button>
                        </td>
                        <td>{{$user->id}}</td>
                   
                        <td>{{$user->name}}</td>
                        <td>{{$user->mobile}}</td>
                        <td>{{$user->language}}</td>
                        <td>{{$user->gender}}</td>
                        <td>
                            @if ($user->avatars && $user->avatars->image) <!-- Check if avatar and image exist -->
                                <a href="{{ asset('storage/app/public/avatars/' . $user->avatars->image) }}" data-lightbox="image-{{ $user->id }}">
                                    <img class="customer-img img-thumbnail img-fluid" src="{{ asset('storage/app/public/avatars/' . $user->avatars->image) }}" alt="{{ $user->name }}" style="max-width: 100px; max-height: 100px;">
                                </a>
                            @else
                                <span>No Avatar</span> <!-- Fallback if no image is found -->
                            @endif
                        </td>
                        <td>{{$user->balance}}</td>
                        <td>{{$user->coins}}</td>
                        <td>{{$user->total_coins}}</td>
                        <td>
                        <span class=" @if($user->status == 1) text-pending @elseif($user->status == 2) text-success @elseif($user->status == 3) text-danger @else text-secondary @endif">
                                @if($user->status == 1) Pending @elseif($user->status == 2) Verified @elseif($user->status == 3) Rejected @else Unknown @endif
                            </span>
                            </td>
                        <td>{{$user->datetime}}</td>
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
        }, 300); // Adjust delay as needed
    });


    let debounceTimer;

function filterUsers() {
    clearTimeout(debounceTimer);

    debounceTimer = setTimeout(function() {
        let search = $('#search-input').val();

        window.location.search = `search=${encodeURIComponent(search)}`;
    }, 500); // Adjust the delay (in milliseconds) as needed
}

$('#search-input').on('input', filterUsers);
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
@endsection