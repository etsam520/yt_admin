<?php
namespace App\Imports;

use App\Models\TestQuestion;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Support\Facades\Log;
class TestQuestionsImport implements ToModel, WithHeadingRow, WithValidation
{
    private $testId;

    public function __construct($testId)
    {
        $this->testId = $testId;
    }

    public function model(array $row)
    {
        // Log the raw row data as received from Excel
        Log::info('Raw Excel row data: ' . json_encode($row, JSON_PRETTY_PRINT));

        // Preprocess the row to ensure all values are strings
        $processedRow = array_map(function ($value) {
            return $this->toString($value);
        }, $row);

        // Log the processed row data after conversion
        Log::info('Processed Excel row data (after string conversion): ' . json_encode($processedRow, JSON_PRETTY_PRINT));

        // Manual validation for required fields
        $validationErrors = [];
        if (empty($processedRow['question_text'])) {
            $validationErrors[] = 'question_text is required';
        }
        if (empty($processedRow['option_a'])) {
            $validationErrors[] = 'option_a is required';
        }
        if (empty($processedRow['option_b'])) {
            $validationErrors[] = 'option_b is required';
        }
        if (empty($processedRow['option_c'])) {
            $validationErrors[] = 'option_c is required';
        }
        if (empty($processedRow['option_d'])) {
            $validationErrors[] = 'option_d is required';
        }
        if (empty($processedRow['correct_option']) || !in_array(strtolower($processedRow['correct_option']), ['a', 'b', 'c', 'd'])) {
            $validationErrors[] = 'correct_option must be one of: a, b, c, or d';
        }

        if (!empty($validationErrors)) {
            Log::error('Validation failed for row: ' . json_encode($validationErrors, JSON_PRETTY_PRINT));
            throw new \Exception("Row validation failed: " . implode(', ', $validationErrors));
        }

        // Create the TestQuestion instance
        $question = new TestQuestion([
            'test_id' => $this->testId,
            'question_text' => $processedRow['question_text'],
            'question_text_hindi' => $processedRow['question_text_hindi'],
            'option_a' => $processedRow['option_a'],
            'option_a_hindi' => $processedRow['option_a_hindi'],
            'option_b' => $processedRow['option_b'],
            'option_b_hindi' => $processedRow['option_b_hindi'],
            'option_c' => $processedRow['option_c'],
            'option_c_hindi' => $processedRow['option_c_hindi'],
            'option_d' => $processedRow['option_d'],
            'option_d_hindi' => $processedRow['option_d_hindi'],
            'correct_option' => strtolower($processedRow['correct_option']),
            'solution' => $processedRow['solution'],
            'solution_hindi' => $processedRow['solution_hindi'],
        ]);

        Log::info('Created TestQuestion: ' . json_encode($question->toArray(), JSON_PRETTY_PRINT));
        return $question;
    }

    private function toString($value)
    {
        if (is_null($value) || $value === '') {
            Log::info('Converting empty/null value to empty string');
            return '';
        }
        Log::info("Converting value to string: " . (string)$value);
        return (string)$value;
    }

    public function rules(): array
    {
        return [
            'question_text' => 'required|string',
            'question_text_hindi' => 'nullable|string',
            'option_a' => 'required|string',
            'option_a_hindi' => 'nullable|string',
            'option_b' => 'required|string',
            'option_b_hindi' => 'nullable|string',
            'option_c' => 'required|string',
            'option_c_hindi' => 'nullable|string',
            'option_d' => 'required|string',
            'option_d_hindi' => 'nullable|string',
            'correct_option' => 'required|in:a,b,c,d',
            'solution' => 'nullable|string',
            'solution_hindi' => 'nullable|string',
        ];
    }
}
