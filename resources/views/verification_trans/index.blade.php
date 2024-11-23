@extends('layouts.admin')

@section('title', 'Verification Trans Management')
@section('content-header', 'Verification Trans Management')
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
        <div class="col-md-8"></div> <!-- Create a placeholder column to push the search box to the right -->
        <div class="col-md-4">
            <!-- Search Form -->
            <form action="{{ route('verification_trans.index') }}" method="GET">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" placeholder="Search by....">
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
                    <th>User Name <i class="fas fa-sort"></i></th>
                    <th>Email <i class="fas fa-sort"></i></th>
                    <th>Txn ID<i class="fas fa-sort"></i></th>
                    <th>Order ID<i class="fas fa-sort"></i></th>
                    <th>Amount<i class="fas fa-sort"></i></th>
                    <th>Status<i class="fas fa-sort"></i></th>
                    <th>Txn Date<i class="fas fa-sort"></i></th>
                    <th>DateTime<i class="fas fa-sort"></i></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($verification_trans as $verification_tran)
                <tr>
                    <td>
                        <button class="btn btn-danger btn-delete" data-url="{{route('verification_trans.destroy', $verification_tran)}}"><i class="fas fa-trash"></i></button>
                    </td>
                    <td>{{$verification_tran->id}}</td>
                    <td>{{ optional($verification_tran->user)->name }}</td> <!-- Display user name safely -->
                    <td>{{ optional($verification_tran->user)->email }}</td> <!-- Display user name safely -->
                    <td>{{$verification_tran->txn_id}}</td>
                    <td>{{$verification_tran->order_id}}</td>
                    <td>{{$verification_tran->amount}}</td>
                    <td>
                        <span class="{{ $verification_tran->status == 1 ? 'text-enable' : 'text-disables' }}">
                                {{ $verification_tran->status == 1 ? 'Verified' : 'Pending' }}
                            </span>
                        </td>
                    <td>{{$verification_tran->txn_date}}</td>
                    <td>{{$verification_tran->datetime}}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    {{ $verification_trans->render() }}
    </div>
</div>
@endsection

@section('js')
    <script src="{{ asset('plugins/sweetalert2/sweetalert2.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/js/lightbox.min.js"></script>
    <script>
        $(document).ready(function () {
            // Submit the form when user selection changes
            $('#user-filter').change(function () {
                if ($(this).val() !== '') {
                    $('#user-filter-form').submit();
                } else {
                    window.location.href = "{{ route('verification_trans.index') }}";
                }
            });
        });

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
        });
    </script>
@endsection
