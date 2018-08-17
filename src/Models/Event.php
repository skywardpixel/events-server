<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model {
    protected $table = 'events';
    protected $fillable = ['title', 'location', 'date_time', 'description'];
    public $timestamps = false;

    public function participants() {
        return $this->hasMany('App\Models\Participant');
    }
}
