@extends('layouts.admin')

@section('title', 'Transactions Management')
@section('content-header', 'Transactions Management')
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
                
                <!-- Filter by Type -->
                <div class="form-group mb-0 d-flex align-items-center">
                    <label for="type-filter" class="mr-2 mb-0">Filter by type:</label>
                    <select name="type" id="type-filter" class="form-control">
                        <option value="" {{ request()->input('type') === null ? 'selected' : '' }}>All</option>
                        @foreach ($types as $type)
                            <option value="{{ $type->type }}" {{ request()->input('type') === $type->type ? 'selected' : '' }}>{{ $type->type }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            
            <div class="col-md-4 text-md-right mt-3 mt-md-0">
                <!-- Search Form -->
                <form id="search-form" action="{{ route('transactions.index') }}" method="GET">
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
                        <th>ID <i class="fas fa-sort"></i></th>
                        <th>User Name <i class="fas fa-sort"></i></th>
                        <th>Type <i class="fas fa-sort"></i></th>
                        <th>Points <i class="fas fa-sort"></i></th>
                        <th>Amount <i class="fas fa-sort"></i></th>
                        <th>DateTime <i class="fas fa-sort"></i></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($transactions as $transaction)
                    <tr>
                        <td>{{ $transaction->id }}</td>
                        <td>{{ optional($transaction->user)->name }}</td>
                        <td>{{ $transaction->type }}</td>
                        <td>{{ $transaction->points }}</td>
                        <td>{{ $transaction->amount }}</td>
                        <td>{{ $transaction->datetime }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        {{ $transactions->appends(request()->query())->links() }}
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
    $('#type-filter').val(queryParams.type || '');

    // Handle search input with debounce
    let debounceTimeout;
    $('#search-input').on('input', function () {
        clearTimeout(debounceTimeout);
        debounceTimeout = setTimeout(function () {
            updateUrlParams();
        }, 300); // Adjust delay as needed
    });

    // Handle type filter change
    $('#type-filter').change(function () {
        updateUrlParams();
    });

    function updateUrlParams() {
        const search = $('#search-input').val();
        const type = $('#type-filter').val();
        const queryParams = { search };
        
        // Add type parameter only if it is not 'All'
        if (type && type !== '') {
            queryParams.type = type;
        }

        const queryString = new URLSearchParams(queryParams).toString();
        window.location.search = queryString;
    }

    // Sorting functionality
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
