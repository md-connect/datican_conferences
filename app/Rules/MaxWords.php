<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class MaxWords implements Rule
{
    protected $maxWords;
    protected $currentWords;

    public function __construct($maxWords)
    {
        $this->maxWords = $maxWords;
    }

    public function passes($attribute, $value)
    {
        $this->currentWords = str_word_count(strip_tags($value));
        return $this->currentWords <= $this->maxWords;
    }

    public function message()
    {
        return "The :attribute must not exceed {$this->maxWords} words. Current count: {$this->currentWords} words.";
    }
}