<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Participant extends Model {
    protected $table = 'participants';
    protected $fillable = ['name', 'email', 'company', 'phone'];
    public $timestamps = false;

    public function event() {
        return $this->belongsTo('App\Models\Event');
    }
}
