<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Foto extends Model {
  use HasFactory;

  protected $fillable = ['especial_id', 'foto_path'];

  public function especial() {
    return $this->belongsTo(Especial::class);
  }
}
