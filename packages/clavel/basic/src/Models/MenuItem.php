<?php

namespace Clavel\Basic\Models;

use Astrotomic\Translatable\Translatable;
use Kalnoy\Nestedset\NodeTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class MenuItem extends Model
{
    use Translatable;

    use NodeTrait;

    public $useTranslationFallback = true;
    protected $table = 'menu_items';

    public function getLftName()
    {
        return 'lft';
    }

    public function getRgtName()
    {
        return 'rgt';
    }

    public $translatedAttributes = ['title','url', 'generate_url'];

    public function menu()
    {
        return $this->belongsTo('Clavel\Basic\Models\Menu');
    }

    public function menuItemType()
    {
        return $this->hasOne('Clavel\Basic\Models\MenuItemTypes', 'id', 'item_type_id');
    }

    public function roles()
    {
        return $this->belongsToMany('App\Models\Role', 'menu_items_role')
            ->withPivot('menu_item_id')
            ->withTimestamps();
    }

    public function page()
    {
        return $this->hasOne('Clavel\Basic\Models\Page', 'id', 'page_id');
    }

    public function pageAuthorized()
    {
        $page = $this->page()->first();
        if ($page->permission=='0') {
            return true;
        } else {
            if (Auth::user()!=null && Auth::user()->can($page->permission_name)) {
                return true;
            } else {
                return (Auth::user()!=null) ? false : true;
            }
        }
    }

    public function hiddenMenuPermission()
    {
        switch ($this->permission) {
            case "0":
                // Siempre visible
                return false;
                break;
            case "1":
                // Solo usuarios autenticados
                if ($this->roles()->count()>0 && auth()->check()) {
                    foreach ($this->roles as $rol) {
                        if (auth()->user()->hasRole($rol->name)) {
                            return false;
                        }
                    }
                } else {
                    return auth()->guest();
                }
                return true;
                break;
            case "2":
                // Solo usuarios anónimos
                return auth()->check();
                break;
        }

        return true;
    }
}
