<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class UserVerifications extends Authenticatable
{
    use Notifiable;

    protected $guard = 'users';

    protected $table = 'users';


    protected $fillable = [
        'name', 'email', 'mobile', 'age', 'profile','gender','refer_code','referred_by','profession_id','datetime','points','total_points','state','city','unique_name','verified','last_seen','online_status','cover_img','dummy','introduction','message_notify','add_friend_notify','view_notify','profile_verified','cover_img_verified','verification_end_date','selfi_image','proof_image', // Add 'mobile' to the fillable fields
    ];
    public function user()
    {
        return $this->belongsTo(Users::class, 'user_id');  // Assuming 'user_id' is the foreign key
    }

    public function professions()
    {
        return $this->belongsTo(Professions::class, 'profession_id');
    }

    public function profession()
    {
        return $this->belongsTo(Professions::class);
    }
    public function bankDetails()
    {
        return $this->hasOne(BankDetails::class, 'user_id');
    }
    public $timestamps = true;

    protected $hidden = [
        'password', 'remember_token',
    ];

    public function findForPassport($mobile)
    {
        return $this->where('mobile', $mobile)->first();
    }
    public function getFullname()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function getAvatar()
    {
        return 'https://www.gravatar.com/avatar/' . md5($this->email);
    }
}
