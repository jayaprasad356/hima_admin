<?php

namespace App\Http\Controllers;

use App\Http\Controllers\CartController;
use App\Http\Controllers\PointsController;
use App\Http\Controllers\PlansController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\TripsController;
use App\Http\Controllers\BankDetailsController;
use App\Http\Controllers\ChatsController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\HomeController;    
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\AppsettingsController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\FriendsController;
use App\Http\Controllers\NotificationsController;
use App\Http\Controllers\UserNotificationsController;
use App\Http\Controllers\UserVerificationController;
use App\Http\Controllers\VerificationsController;
use App\Http\Controllers\FakechatsController;
use App\Http\Controllers\Chat_pointsController;
use App\Http\Controllers\FeedbacksController;
use App\Http\Controllers\Recharge_transController;
use App\Http\Controllers\UserReportsController;
use App\Http\Controllers\Verification_transController;
use App\Http\Controllers\ProfessionsController;
use App\Http\Controllers\TransactionsController;
use App\Http\Controllers\BulkUserController;
use App\Http\Controllers\WalletsController;
use App\Http\Controllers\WithdrawalsController;
use App\Models\BankDetails;
use App\Models\UserNotifications;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;



Route::get('/', function () {
    return redirect('/admin');
});

Auth::routes();



Route::namespace('Auth')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    Route::get('/register', 'RegisterController@showRegistrationForm')->name('register');
    Route::post('/register', 'RegisterController@register');

    Route::get('/password/reset', 'ForgotPasswordController@showLinkRequestForm')->name('password.request');
    Route::post('/password/email', 'ForgotPasswordController@sendResetLinkEmail')->name('password.email');
    Route::get('/password/reset/{token}', 'ResetPasswordController@showResetForm')->name('password.reset');
    Route::post('/password/reset', 'ResetPasswordController@reset');
});
Route::prefix('admin')->middleware('auth')->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::post('/settings', [SettingController::class, 'store'])->name('settings.store');
    Route::resource('customers', CustomerController::class);


    //User
    Route::get('/users', [UsersController::class, 'index'])->name('users.index');
    Route::get('/users/create', [UsersController::class, 'create'])->name('users.create');
    Route::get('/users/{users}/edit', [UsersController::class, 'edit'])->name('users.edit');
    Route::delete('/users/{users}', [UsersController::class, 'destroy'])->name('users.destroy');
    Route::put('/users/{users}', [UsersController::class, 'update'])->name('users.update');
    Route::post('/users', [UsersController::class, 'store'])->name('users.store');
    Route::get('/users/{id}/add-points', [UsersController::class, 'addPointsForm'])->name('users.add_points');
    Route::post('/users/{id}/add-points', [UsersController::class, 'addPoints'])->name('users.store_points');


   
     //Trips  
     Route::get('/trips', [TripsController::class, 'index'])->name('trips.index');
     Route::get('/trips/create', [TripsController::class, 'create'])->name('trips.create');
     Route::get('/trips/{trips}/edit', [TripsController::class, 'edit'])->name('trips.edit');
     Route::delete('/trips/{trips}', [TripsController::class, 'destroy'])->name('trips.destroy');
     Route::put('/trips/{trips}', [TripsController::class, 'update'])->name('trips.update');
     Route::post('/trips', [TripsController::class, 'store'])->name('trips.store');
     Route::post('/trips/updateStatus', [TripsController::class, 'updateStatus'])->name('trips.updateStatus');
     Route::post('/trips/sendNotification', [TripsController::class, 'sendNotification'])->name('trips.sendNotification');


     //Chats  
     Route::get('/chats', [ChatsController::class, 'index'])->name('chats.index');
     Route::get('/chats/create', [ChatsController::class, 'create'])->name('chats.create');
     Route::get('/chats/{chats}/edit', [ChatsController::class, 'edit'])->name('chats.edit');
     Route::delete('/chats/{chats}', [ChatsController::class, 'destroy'])->name('chats.destroy');
     Route::put('/chats/{chats}', [ChatsController::class, 'update'])->name('chats.update');
     Route::post('/chats', [ChatsController::class, 'store'])->name('chats.store');
     

      //Points  
      Route::get('/points', [PointsController::class, 'index'])->name('points.index');
      Route::get('/points/create', [PointsController::class, 'create'])->name('points.create');
      Route::get('/points/{points}/edit', [PointsController::class, 'edit'])->name('points.edit');
      Route::delete('/points/{points}', [PointsController::class, 'destroy'])->name('points.destroy');
      Route::put('/points/{points}', [PointsController::class, 'update'])->name('points.update');
      Route::post('/points', [PointsController::class, 'store'])->name('points.store');

        //Chat Points  
        Route::get('/chat_points', [Chat_pointsController::class, 'index'])->name('chat_points.index');
        Route::get('/chat_points/create', [Chat_pointsController::class, 'create'])->name('chat_points.create');
        Route::get('/chat_points/{chat_points}/edit', [Chat_pointsController::class, 'edit'])->name('chat_points.edit');
        Route::delete('/chat_points/{chat_points}', [Chat_pointsController::class, 'destroy'])->name('chat_points.destroy');
        Route::put('/chat_points/{chat_points}', [Chat_pointsController::class, 'update'])->name('chat_points.update');
        Route::post('/chat_points', [Chat_pointsController::class, 'store'])->name('chat_points.store');

    

      //Points  
      Route::get('/plans', [PlansController::class, 'index'])->name('plans.index');
      Route::get('/plans/create', [PlansController::class, 'create'])->name('plans.create');
      Route::get('/plans/{plans}/edit', [PlansController::class, 'edit'])->name('plans.edit');
      Route::delete('/plans/{plans}', [PlansController::class, 'destroy'])->name('plans.destroy');
      Route::put('/plans/{plans}', [PlansController::class, 'update'])->name('plans.update');
      Route::post('/plans', [PlansController::class, 'store'])->name('plans.store');
    
       //friends  
       Route::get('/friends', [FriendsController::class, 'index'])->name('friends.index');
       Route::delete('/friends/{friends}', [FriendsController::class, 'destroy'])->name('friends.destroy');


        //Notifications  
        Route::get('/notifications', [NotificationsController::class, 'index'])->name('notifications.index');
        Route::get('/notifications/create', [NotificationsController::class, 'create'])->name('notifications.create');
        Route::get('/notifications/{notifications}/edit', [NotificationsController::class, 'edit'])->name('notifications.edit');
        Route::delete('/notifications/{notifications}', [NotificationsController::class, 'destroy'])->name('notifications.destroy');
        Route::put('/notifications/{notifications}', [NotificationsController::class, 'update'])->name('notifications.update');
        Route::post('/notifications', [NotificationsController::class, 'store'])->name('notifications.store');


        
        //Professions  
        Route::get('/professions', [ProfessionsController::class, 'index'])->name('professions.index');
        Route::get('/professions/create', [ProfessionsController::class, 'create'])->name('professions.create');
        Route::get('/professions/{professions}/edit', [ProfessionsController::class, 'edit'])->name('professions.edit');
        Route::delete('/professions/{professions}', [ProfessionsController::class, 'destroy'])->name('professions.destroy');
        Route::put('/professions/{professions}', [ProfessionsController::class, 'update'])->name('professions.update');
        Route::post('/professions', [ProfessionsController::class, 'store'])->name('professions.store');


          //UserNotifications  
          Route::get('/usernotifications', [UserNotificationsController::class, 'index'])->name('usernotifications.index');
          Route::get('/usernotifications/create', [UserNotificationsController::class, 'create'])->name('usernotifications.create');
          Route::get('/usernotifications/{usernotifications}/edit', [UserNotificationsController::class, 'edit'])->name('usernotifications.edit');
          Route::delete('/usernotifications/{usernotifications}', [UserNotificationsController::class, 'destroy'])->name('usernotifications.destroy');
          Route::put('/usernotifications/{usernotifications}', [UserNotificationsController::class, 'update'])->name('usernotifications.update');
          Route::post('/usernotifications', [UserNotificationsController::class, 'store'])->name('usernotifications.store');
  

    
        Route::get('news/1/edit', [NewsController::class, 'edit'])->name('news.edit');
        Route::post('news/1/update', [NewsController::class, 'update'])->name('news.update');

        Route::get('appsettings/{id}/edit', [AppsettingsController::class, 'edit'])->name('appsettings.edit');
        Route::put('appsettings/{id}/update', [AppsettingsController::class, 'update'])->name('appsettings.update');
        

        //Verifications  
        Route::get('/verifications', [VerificationsController::class, 'index'])->name('verifications.index');
        Route::delete('/verifications/{verification}', [VerificationsController::class, 'destroy'])->name('verifications.destroy');
        Route::get('/verifications/{verifications}/edit', [VerificationsController::class, 'edit'])->name('verifications.edit');
        Route::put('/verifications/{verifications}', [VerificationsController::class, 'update'])->name('verifications.update');
        Route::post('/verifications/verify', [VerificationsController::class, 'verify'])->name('verifications.verify');
        Route::delete('/verifications/{verification}/deleteImage', [VerificationsController::class, 'deleteImage'])->name('verifications.deleteImage');

           //fakechats  
           Route::get('/fakechats', [FakechatsController::class, 'index'])->name('fakechats.index');
           Route::delete('/fakechats/{fakechat}', [FakechatsController::class, 'destroy'])->name('fakechats.destroy');
           Route::post('/fakechats/verify', [FakechatsController::class, 'verify'])->name('fakechats.verify');
           Route::post('/fakechats/not-fake', [FakechatsController::class, 'notFake'])->name('fakechats.notFake');

            //Verifications  
            Route::get('/transactions', [TransactionsController::class, 'index'])->name('transactions.index');
            Route::delete('/transactions/{transactions}', [TransactionsController::class, 'destroy'])->name('transactions.destroy');
            

               //Feedbacks  
               Route::get('/feedbacks', [FeedbacksController::class, 'index'])->name('feedbacks.index');
               Route::delete('/feedbacks/{feedback}', [FeedbacksController::class, 'destroy'])->name('feedbacks.destroy');
                    
                         //Recharge Trans  
               Route::get('/recharge_trans', [Recharge_transController::class, 'index'])->name('recharge_trans.index');
               Route::delete('/recharge_trans/{recharge_trans}', [Recharge_transController::class, 'destroy'])->name('recharge_trans.destroy');

                    //Feedbacks  
                    Route::get('/verification_trans', [Verification_transController::class, 'index'])->name('verification_trans.index');
                    Route::delete('/verification_trans/{verification_trans}', [Verification_transController::class, 'destroy'])->name('verification_trans.destroy');
                    
                    Route::get('/wallets', [WalletsController::class, 'index'])->name('wallets.index');
                    Route::delete('/wallets/{wallets}', [WalletsController::class, 'destroy'])->name('wallets.destroy');
                    
                      
                    Route::get('/withdrawals', [WithdrawalsController::class, 'index'])->name('withdrawals.index');
                    Route::delete('/withdrawals/{withdrawal}', [WithdrawalsController::class, 'destroy'])->name('withdrawals.destroy');
                    Route::get('/withdrawals/{withdrawal}/edit', [WithdrawalsController::class, 'edit'])->name('withdrawals.edit');
                    Route::put('/withdrawals/{withdrawal}', [WithdrawalsController::class, 'update'])->name('withdrawals.update');
                    Route::post('/withdrawals/verify', [WithdrawalsController::class, 'verify'])->name('withdrawals.verify');
                    Route::post('/withdrawals/cancel', [WithdrawalsController::class, 'cancel'])->name('withdrawals.cancel');
                    Route::get('withdrawals/export', [WithdrawalsController::class, 'export'])->name('withdrawals.export');
                    Route::get('withdrawals/exportUsers', [WithdrawalsController::class, 'exportUsers'])->name('withdrawals.exportUsers');
                    
                    Route::get('/reports', [ReportsController::class, 'index'])->name('reports.index');
                    Route::delete('/reports/{reports}', [ReportsController::class, 'destroy'])->name('reports.destroy');
                    
                     //Verifications  
        Route::get('/user_verifications', [UserVerificationController::class, 'index'])->name('user_verifications.index');
        Route::post('/user_verifications/verify', [UserVerificationController::class, 'verify'])->name('user_verifications.verify');
        Route::post('/user-verifications/reject', [UserVerificationController::class, 'reject'])->name('user_verifications.reject');


                    Route::get('/bankdetails', [BankDetailsController::class, 'index'])->name('bankdetails.index');
                 
                    
               

        //Bulk Users
       // web.php
// Define the route for the "Upload Bulk Users" page
Route::get('bulk-users/upload', [BulkUserController::class, 'create'])->name('bulk-users.upload');
Route::post('bulk-users/upload', [BulkUserController::class, 'store'])->name('bulk-users.store');

    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart', [CartController::class, 'store'])->name('cart.store');
    Route::post('/cart/change-qty', [CartController::class, 'changeQty']);
    Route::delete('/cart/delete', [CartController::class, 'delete']);
    Route::delete('/cart/empty', [CartController::class, 'empty']);
});
// OneSignal service worker route
Route::get('/OneSignalSDKWorker.js', function () {
    return response()->file(public_path('OneSignalSDKWorker.js'));
});