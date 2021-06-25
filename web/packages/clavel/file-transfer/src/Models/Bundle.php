<?php
namespace Clavel\FileTransfer\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Bundle extends Model
{
    protected $table = "ft_bundles";

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'expires_at',
    ];

    /**
     * Get the comments for the blog post.
     */
    public function files()
    {
        return $this->hasMany('Clavel\FileTransfer\Models\BundleFile');
    }

    public function getExpiresAtFormattedAttribute()
    {
        if (!empty($this->expires_at)) {
            return (Carbon::createFromFormat('Y-m-d', $this->expires_at))->format('d/m/Y');
        }

        return '';
    }

    public function getExpiresAtFormattedHumansAttribute()
    {
        if (!empty($this->expires_at)) {
            return $this->expires_at->diffForHumans();
        }

        return '';
    }
}
