<?php
namespace App\Imports;

use App\Models\Question;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class QuestionsImport implements ToModel, WithHeadingRow, WithValidation
{
    private $hierarchy;

    public function __construct(array $hierarchy)
    {
        $this->hierarchy = $hierarchy;
    }

    public function model(array $row)
    {
        return new Question([
            'trade_group_id' => $this->hierarchy['trade_group_id'],
            'trade_id' => $this->hierarchy['trade_id'],
            'subject_id' => $this->hierarchy['subject_id'],
            'chapter_id' => $this->hierarchy['chapter_id'],
            'topic_id' => $this->hierarchy['topic_id'],
            'question_text' => $row['question_text'],
            'option_a' => $row['option_a'],
            'option_b' => $row['option_b'],
            'option_c' => $row['option_c'],
            'option_d' => $row['option_d'],
            'correct_option' => strtolower($row['correct_option']),
            'solution' => $row['solution'] ?? null,
        ]);
    }

    public function rules(): array
    {
        return [
            'question_text' => 'required|string',
            'option_a' => 'required|string',
            'option_b' => 'required|string',
            'option_c' => 'required|string',
            'option_d' => 'required|string',
            'correct_option' => 'required|in:a,b,c,d',
            'solution' => 'nullable|string',
        ];
    }
}
