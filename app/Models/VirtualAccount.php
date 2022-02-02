<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VirtualAccount extends Model
{
    use HasFactory;

    public function rules()
    {
        return $this->morphMany(Rule::class, 'rulable');
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
