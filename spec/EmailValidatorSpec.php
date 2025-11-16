<?php

use Kahlan\Plugin\Double;
use App\EmailValidator;


describe("EmailValidator", function() {

    it("Devuelve true para un correo válido", function() {
        $emailValidator = new EmailValidator();
        $resultado = $emailValidator->validateEmail("usuario@dominio.com");

        expect($resultado)->toBe(true);
    });

    it("Devuelve false para un correo inválido", function() {
        $emailValidator = new EmailValidator();
        $resultado = $emailValidator->validateEmail("correo-invalido");

        expect($resultado)->toBe(false);
    });

});
?>