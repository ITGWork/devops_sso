<?php

namespace App\Models\Elicense\Rform;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $connection = 'mysql_elicense';
    protected $table      = 'ros_rform_product';
    public $timestamps    = false;

    protected $fillable = ['status_id', 'modified_by'];

    public function labs()
    {
        return $this->hasMany(ProductLab::class, 'product_id');
    }
}
