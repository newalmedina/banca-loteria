<?php

namespace App\Modules\Newsletter\Models;

use App\Models\Idioma;
use Illuminate\Database\Eloquent\Model;

class NewsletterField extends Model
{
    use \Astrotomic\Translatable\Translatable;

    public $useTranslationFallback = true;

    public $translatedAttributes = ['body'];

    public $translationForeignKey = 'newsletter_row_field_id';

    protected $table = 'newsletter_row_fields';
    protected $fillable = [];
    protected $guarded = [];

    public function post()
    {
        return $this->belongsTo("App\Modules\Posts\Models\Post");
    }

    public function row()
    {
        return $this->belongsTo("App\Modules\Newsletter\Models\NewsletterRow", 'newsletter_row_id', 'id');
    }

    public function getPost()
    {
        $post = $this->post;

        $imgsrc = "";
        if ($this->image_custom != '') {
            $imgsrc = $this->image_custom;
        } elseif ($post->images()->count() > 0) {
            $imgsrc = $post->images[0]->path;
        }

        $a_post = array();
        $a_post["id"] = $this->id;
        $a_post["title"] = $post->title;
        $wordsTotal = ($this->text_length != '') ? $this->text_length : (500 / $this->row->cols);
        $a_post["text"] = ($this->complete_post == '0') ? $post->leadNewsletter($wordsTotal) : $post->resumen;
        if ($this->text_length == '0') {
            $a_post["text"] = "";
        }
        $a_post["img"] = $imgsrc;
        $a_post["type"] = $this->type;
        $a_post["cols"] = $this->row->cols;
        $a_post["image_position"] = $this->image_position;
        $a_post["title_color"] = $this->title_color;
        $a_post["text_color"] = $this->text_color;
        $a_post["url_seo"] = $post->url_seo;
        $a_post["fuente"] = $post->fuente;
        $a_post["fecha"] = $post->date_post_formatted;
        $a_post["in_box"] = $this->in_box;

        return $a_post;
    }

    public function getPostMultilang()
    {
        $a_post = $this->getPost();
        $post = $this->post;
        $idiomas = Idioma::active()->get();

        $wordsTotal = ($this->text_length != '') ? $this->text_length : (500 / $this->row->cols);
        foreach ($idiomas as $idioma) {
            $a_post["title_" . $idioma->code] = $post->{'title:' . $idioma->code};
            $a_post["url_seo_" . $idioma->code] = $post->{'url_seo:' . $idioma->code};
            $a_post["text_" . $idioma->code] = ($this->complete_post == '0') ?
                $post->leadNewsletter($wordsTotal, $idioma->code) : $post->{'resumen:' . $idioma->code};
            if ($this->text_length == '0') {
                $a_post["text_" . $idioma->code] = "";
            }
        }

        return $a_post;
    }

    public function getContent()
    {
        $a_post = array();
        $a_post["id"] = $this->id;
        $a_post["resume"] = $this->resumen;
        $a_post["text"] = $this->body;
        $a_post["type"] = $this->type;
        $a_post["cols"] = $this->row->cols;
        $a_post["in_box"] = $this->in_box;

        return $a_post;
    }

    public function getContentMultilang()
    {
        $idiomas = Idioma::active()->get();
        $a_post = $this->getContent();
        foreach ($idiomas as $idioma) {
            $a_post["resume_" . $idioma->code] = $this->{'resumen:' . $idioma->code};
            $a_post["text_" . $idioma->code] = $this->{'body:' . $idioma->code};
        }
        return $a_post;
    }
}
