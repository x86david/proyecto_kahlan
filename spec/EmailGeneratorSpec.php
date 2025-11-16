<?php

use Kahlan\Plugin\Double;
use App\EmailGenerator;

describe("EmailGenerator", function() {
    it("Genera un correo electrónico con el formato correcto", function() {
        $emailGenerator = new EmailGenerator();
        $email = $emailGenerator->generateEmail();

        $regex = '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/';
        expect($email)->toMatch($regex);
    });
});
?>