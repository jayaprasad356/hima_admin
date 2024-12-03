@extends('layouts.admin')

@section('title', 'Speech Text Management')
@section('content-header', 'Speech Text Management')
@section('content-actions')
    <a href="{{ route('speech_texts.create') }}" class="btn btn-success"><i class="fas fa-plus"></i> Add New Speech Text</a>
@endsection
@section('css')
    <link rel="stylesheet" href="{{ asset('plugins/sweetalert2/sweetalert2.min.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/css/lightbox.min.css">
@endsection
@section('content')
<div class="card">
    <div class="card-body">
        <div class="row mb-4">

            <div class="col-md-4 text-right">
                <form id="search-form" action="{{ route('speech_texts.index') }}" method="GET">
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
                        <th>Text<i class="fas fa-sort"></i></th>
                        <th>Language<i class="fas fa-sort"></i></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($speech_texts as $speech_text)
                    <tr>
                        <td>
                        <a href="{{ route('speech_texts.edit', $speech_text) }}" class="btn btn-primary"><i class="fas fa-edit"></i></a>
                            <button class="btn btn-danger btn-delete" data-url="{{route('speech_texts.destroy', $speech_text)}}"><i class="fas fa-trash"></i></button>
                        </td>
                        <td>{{$speech_text->id}}</td>  
                        <td>{{$speech_text->text}}</td>
                        <td>{{$speech_text->language}}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        {{ $speech_texts->appends(request()->query())->links() }}
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
