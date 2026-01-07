<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuestionTbl extends Model
{
    protected $table = 'question_tbls';
    protected $fillable = [
        'question',
        'type',
        'options',
        'answer',
        'solution',
        'positive_marks',
        'negative_marks',
        'category_id',
        'category_depth_index',
        'created_by',
        'is_public',
    ];
    public $timestamps = true;

    protected $casts = [
        'question' => 'array',
        'options' => 'array',
        'answer' => 'string',
        'solution' => 'array',
    ];

    public function meta()
    {
        return $this->hasMany(QuestionMeta::class, 'question_id');
    }
    /**
     * Get the translations for the question.
     */
    public function translations()
    {
        return $this->hasMany(QuestionTrans::class, 'question_id');
    }

    /**
     * Get the question by ID.
     */
    public static function getQuestionById($id)
    {
        return self::find($id);
    }
    /**
     * Get all questions.
     */
    public static function getAllQuestions()
    {
        return self::orderBy('created_at', 'desc')->get();
    }
    /**
     * Get questions by public status.
     */
    public static function getQuestionsByPublicStatus($isPublic)
    {
        return self::where('is_public', $isPublic)
            ->orderBy('created_at', 'desc')
            ->get();
    }
    /**
     * Get questions by answer.
     */
    public static function getQuestionsByAnswer($answer)
    {
        return self::where('answer', $answer)
            ->orderBy('created_at', 'desc')
            ->get();
    }
    /**
     * Get questions by translation locale.
     */
    public static function getQuestionsByTranslationLocale($locale)
    {
        return self::whereHas('translations', function ($query) use ($locale) {
            $query->where('locale', $locale);
        })->orderBy('created_at', 'desc')->get();
    }
    /**
     * Get questions by translation title.
     */
    public static function getQuestionsByTranslationTitle($title)
    {
        return self::whereHas('translations', function ($query) use ($title) {
            $query->where('title', 'like', '%' . $title . '%');
        })->orderBy('created_at', 'desc')->get();
    }
    /**
     * Get questions by translation answer details.
     */
    public static function getQuestionsByTranslationAnswerDetails($details)
    {
        return self::whereHas('translations', function ($query) use ($details) {
            $query->where('answer_details', 'like', '%' . $details . '%');
        })->orderBy('created_at', 'desc')->get();
    }
    /**
     * Get questions by translation A option.
     */
    public static function getQuestionsByTranslationA($option)
    {
        return self::whereHas('translations', function ($query) use ($option) {
            $query->where('A', 'like', '%' . $option . '%');
        })->orderBy('created_at', 'desc')->get();
    }
    /**
     * Get questions by translation B option.
     */
    public static function getQuestionsByTranslationB($option)
    {
        return self::whereHas('translations', function ($query) use ($option) {
            $query->where('B', 'like', '%' . $option . '%');
        })->orderBy('created_at', 'desc')->get();
    }
    /**
     * Get questions by translation C option.
     */
    public static function getQuestionsByTranslationC($option)
    {
        return self::whereHas('translations', function ($query) use ($option) {
            $query->where('C', 'like', '%' . $option . '%');
        })->orderBy('created_at', 'desc')->get();
    }
    /**
     * Get questions by translation D option.
     */
    public static function getQuestionsByTranslationD($option)
    {
        return self::whereHas('translations', function ($query) use ($option) {
            $query->where('D', 'like', '%' . $option . '%');
        })->orderBy('created_at', 'desc')->get();
    }
}
