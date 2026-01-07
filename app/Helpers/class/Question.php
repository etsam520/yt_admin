<?php
namespace App\Helpers\class;

use App\Models\QuestionMeta;
use App\Models\QuestionTbl;
use App\Models\UploadedFile;
use Faker\Core\File;

class Question {
    private $question;
    private $options;
    private $answer;
    private $solution;
    private $positive_marks;
    private $negative_marks;
    private $category_id;
    private $category_depth_index;
    private $is_public;
    private $meta;
    private $id;

    public function __construct(QuestionTbl $question_tbl) {
        $this->question = $question_tbl->question;
        $this->options = $question_tbl->options;
        $this->answer = $question_tbl->answer;
        $this->solution = $question_tbl->solution;
        $this->positive_marks = $question_tbl->positive_marks;
        $this->negative_marks = $question_tbl->negative_marks;
        $this->id = $question_tbl->id;
        $this->meta = $question_tbl->meta;
    }


    public function toObject(): object {
        return (object) [
            'id' => $this->id,
            'question' => $this->getQuestion(),
            'options' => $this->getOptions(),
            'answer' => $this->getAnswer() ,
            'solution' => $this->getSolution()->toObject(),
            'positive_marks' => $this->getPositiveMarks(),
            'negative_marks' => $this->getNegativeMarks(),
            'is_public' => $this->is_public,
            'meta' => $this->getMeta(),
        ];

    }

      





    public function getQuestion(): object {
        return (object) [
            'text' => (new Text($this->question['text']['en'], $this->question['text']['hi']))->toObject(),
            'images' => $this->question['images'] ?? []
        ];
    }

    public function getOptions(): array {
        $options = [];
        foreach ($this->options as $option) {
            $options[] = $this->option($option);
        }
        return $options;
    }

    public function getAnswer(): ?string {
        return  $this->answer ;
    }

    public function getSolution(): Solution {
        return new Solution(
            $this->text($this->solution['text']),
            $this->images($this->solution['images'] ?? [])
        );
    }

    public function getPositiveMarks() {
        return $this->positive_marks;
    }


    public function getNegativeMarks() {
        return $this->negative_marks;
    }
    
    private function text(array $arr): Text {
        return new Text($arr['en'] ?? '', $arr['hi'] ?? '');  
    }

    private function images(array $images): array {
        $result = [];
        foreach ($images as $image) {
            $fileUpload = UploadedFile::where('id', $image)->first();

            if (!$fileUpload) continue;
            $result[] = new Image($fileUpload->path, $fileUpload->id);
        }
        return $result;
    }

    private function option(array $option): object {
        return (object) [
            'text' => $this->text($option['text'])->toObject(),
            'images' => array_map(fn($image) => $image->toObject(), $this->images($option['images'] ?? []))
        ];
    }

    public function getMeta(): object {
        $meta = [];
        foreach ($this->meta as $item) {
            
            $metadata = new MetaData($item);
            $meta[$metadata->getKey()] = $metadata->getValue();
        }
        return (object) $meta;
    }
}



class Solution {
    private $text;
    private $images;

    public function __construct(Text $text, array $images) {
        $this->text = $text;
        $this->images = $images;
    }

    public function getText(): Text {
        return $this->text;
    }

    public function getImages(): array {
        return $this->images;
    }

    public function toObject(): object {
        return (object) [
            'text' => $this->text->toObject(),
            'images' => array_map(fn($image) => $image->toObject(), $this->images)
        ];
    }
}

class Text {
    private $en;
    private $hi;

    public function __construct(string $en, string $hi) {
        $this->en = $en;
        $this->hi = $hi;
    }

    public function getEn(): string {
        return $this->en;
    }

    public function getHi(): string {
        return $this->hi;
    }

    public function __toString(): string {
        return $this->en . ' ' . $this->hi;
    }
    
    public function toArray(): array {
        return [
            'en' => $this->en,
            'hi' => $this->hi
        ];
    }

    public function toObject(): object {
        return (object) [
            'en' => $this->en,
            'hi' => $this->hi
        ];
    }
}

class Image {
    private $path;
    private $serverId;

    public function __construct(string $path, string $serverId) {
        $this->path = $path;
        $this->serverId = $serverId;
    }

    public function getPath(): string {
        return $this->path;
    }

    public function getServerId(): string {
        return $this->serverId;
    }

    public function toArray(): array {
        return [
            'path' => $this->path,
            'serverId' => $this->serverId
        ];
    }

    public function toObject(): object {
        return (object) [
            'path' => $this->path,
            'serverId' => $this->serverId
        ];
    }
}
class MetaData {
    private $key;
    private $value;
    public function __construct(QuestionMeta $question_meta) {
        $this->key = convertToCamelCase($question_meta->meta_key); //$question_meta->meta_key;
        $this->value = $question_meta->meta_value;
    }
    public function getKey(): string {
        return $this->key;
    }
    public function getValue(): string {
        return $this->value;
    }
    public function toObject(): object {
        return (object) [
            'key' => $this->key,
            'value' => $this->value
        ];
    }
}