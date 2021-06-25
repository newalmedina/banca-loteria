<?php
namespace App\Models;

use Kalnoy\Nestedset\NodeTrait;
use Illuminate\Database\Eloquent\Model;

/**
* PermissionsTree
*/
class PermissionsTree extends Model
{
    use NodeTrait;

    /**
     * Table name.
     *
     * @var string
     */
    protected $table = 'permissions_tree';


    public function permission()
    {
        return $this->hasOne('App\Models\Permission', 'id', 'permissions_id');
    }
}
