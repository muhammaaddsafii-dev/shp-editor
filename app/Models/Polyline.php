<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Polyline extends Model
{
    use HasFactory;

    protected $table = 'polylines';
    protected $fillable = [
        'name',
        'image',
        'description',
        'geom'
    ];

    public function polylines()
    {
        return $this->select(DB::raw('id, name, description, image, ST_AsGeoJSON(geom) as geom, created_at, updated_at'))->get();
    }

    public function polyline($id)
    {
        return $this->select(DB::raw('id, name, description, image,
        ST_AsGeoJSON(geom) as geom, created_at,
        updated_at'))->where('id', $id)->get();
    }
}
