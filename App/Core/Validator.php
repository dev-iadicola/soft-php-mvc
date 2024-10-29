<?php

namespace App\Core;

class Validator
{
    protected $errors = [];

    public function __construct($errors = [])
    {
        $this->errors = $errors;
    }

    public static function validate(array $inputsAndRusels, array $RulesAndmessages)
    {
        $errors = [];
        foreach ($inputsAndRusels as $input => $rule) {
            if (!self::checkRule($input, $rule)) {
                $errors[] = $RulesAndmessages[$rule];
            }
        }
        return new self($errors);
    }

    protected static function checkRule($input, $rule)
    {
        switch ($rule) {
            case 'email':
                return filter_var($input, FILTER_VALIDATE_EMAIL) !== false;
            case 'string':
                return is_string($input);
            case 'length':
                return strlen($input) > 5;
            case 'image':
                return self::validateImage($input); // esempio di validazione lunghezza
            default:
                return false;
        }
    }

    public static function validatePDF(array $input){
        $format = array('.pdf');
        foreach ($format  as $item){
            if(preg_match("/$item\$/i", $input['name'])){
                return true;
            }
              return  false;
            
        }
    }
   public static function validateImage($file)
{
    // Controlla se il file è un array e ha una chiave 'tmp_name'
    if (is_array($file) && isset($file['tmp_name'])) {
        // Verifica se il file è stato caricato senza errori
        if (is_uploaded_file($file['tmp_name'])) {
            // Ottieni le dimensioni dell'immagine
            $imageSize = @getimagesize($file['tmp_name']);
            return is_array($imageSize);
        }
    }
    return false;
}


    public function fails()
    {
        return !empty($this->errors);
    }

    public function errors()
    {
        $errors = $this->errors;
        $errors =  array_map(fn ($error) => "<li>" . $error . "</li>", $errors);
        $stringErrors =  implode(' ', $errors);
        $stringErrors = '<ul>' . $stringErrors . '</ul>';
        return  $stringErrors;
    }

    public static function confirmedPassword($data)
    {
        return ($data['password'] === $data['confirmed']) ? true : false;
    }
}
