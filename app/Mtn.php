<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 20/07/2017
 * Time: 11:26 PM
 */

namespace App;


use DateTime;
use Illuminate\Database\Eloquent\Model;
use SoapClient;

class Mtn extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = 'logs_mtn';
    protected $fillable = ['mesg', 'expiry', 'username', 'password', 'name', 'info', 'amt', 'mobile', 'billprompt', 'thirdpartyID'];

}