<?php

namespace App\Helper;

use Symfony\Component\Form\Form;

final class HandleErrors
{

    /**
     * 
     * @param Form $form
     * @return array
     */
    public static function getMessages(Form $form): array
    {

        $errors = [];

        foreach ($form->all() as $child) {
            foreach ($child->getErrors() as $error) {
                $name = $child->getName();
                $errors[$name]['message'] = $error->getMessage();
            }
        }

        return $errors;
    }
}
