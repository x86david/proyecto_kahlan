<?php

namespace App;

class EmailGenerator
{
    public function generateEmail()
    {
        $name = substr(md5(uniqid(rand(), true)), 0, 10); // Nombre aleatorio
        $domain = 'example.com'; // Dominio fijo para el ejemplo

        return $name . '@' . $domain;
    }
}
?>