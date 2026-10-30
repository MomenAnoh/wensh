<?php

return [
    'boolean' => 'The :attribute field must be true or false.',
    'digits' => 'The :attribute field must be :digits digits.',
    'email' => 'The :attribute field must be a valid email address.',
    'image' => 'The :attribute field must be an image.',
    'max' => [
        'file' => 'The :attribute field must not be greater than :max kilobytes.',
        'numeric' => 'The :attribute field must not be greater than :max.',
        'string' => 'The :attribute field must not be greater than :max characters.',
    ],
    'mimes' => 'The :attribute field must be a file of type: :values.',
    'regex' => 'The :attribute field format is invalid.',
    'required' => 'The :attribute field is required.',
    'string' => 'The :attribute field must be a string.',
    'unique' => 'The :attribute has already been taken.',
];
