@extends('layouts.admin')

@section('content-header', 'Dashboard')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <!-- small box -->
            <div class="col-lg-4 col-6">
              <div class="small-box bg-green">
                    <div class="inner">
                        <h3>{{ $today_recharge_amount }}</h3>
                        <p>Today Recharge Amount</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-user-clock"></i> <!-- Example of a different icon -->
                    </div>
                    <a href="{{ route('transactions.index') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>

             <div class="col-lg-4 col-6">
                <!-- small box -->
                <div class="small-box bg-blue">
                    <div class="inner">
                        <h3>{{ $pending_trips_count }}</h3>
                        <p>Pending Trips</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-suitcase"></i>
                    </div>
                    <a href="{{ route('trips.index', ['trip_status' => '0']) }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>

            <div class="col-lg-4 col-6">
              <div class="small-box bg-red">
                    <div class="inner">
                        <h3>{{ $pending_withdrawals_count }}</h3>
                        <p>Pending Withdrawals Count</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-user-clock"></i> <!-- Example of a different icon -->
                    </div>
                    <a href="{{ route('withdrawals.index') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>

            <div class="col-lg-4 col-6">
                <!-- small box -->
                <div class="small-box bg-purple">
                    <div class="inner">
                        <h3>{{ $today_registration_count }}</h3>
                        <p>Today Registration Count</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-user-clock"></i> <!-- Example of a different icon -->
                    </div>
                    <a href="{{ route('users.index') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>

            <div class="col-lg-4 col-6">
                <!-- small box -->
                <div class="small-box bg-orange">
                    <div class="inner">
                        <h3>{{ $today_active_users }}</h3>
                        <p>Today Active Users Count</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-user-clock"></i> <!-- Example of a different icon -->
                    </div>
                    <a href="{{ route('users.index') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
           

            <!-- ./col -->
        </div>
        <!-- /.row -->
    </div>
@endsection
