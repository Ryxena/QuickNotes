<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string $categories_id
 * @property string $category_id
 * @property string $notes_id
 * @property string $note_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder|CategoryNote newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CategoryNote newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CategoryNote query()
 * @method static \Illuminate\Database\Eloquent\Builder|CategoryNote whereCategoriesId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CategoryNote whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CategoryNote whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CategoryNote whereNoteId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CategoryNote whereNotesId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CategoryNote whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class CategoryNote extends Model
{
    use HasUlids;

    protected $table = 'category_note';

    protected $fillable = ['categories_id', 'notes_id'];

    public function category()
    {
        return $this->belongsTo(Category::class, 'categories_id');
    }

    public function note()
    {
        return $this->belongsTo(Note::class, 'notes_id');
    }
}
