<?php

use Kahlan\Plugin\Double;
use App\DatabaseManager;
use App\DatabaseConnection;

describe("DatabaseManager", function() {

    beforeEach(function() {
        //Creamoa el doble de la conexión
        $this->dbConnection = Double::instance(['extends' => DatabaseConnection::class]);

        //Instanciamos Database Manager
        $this->dbManager = new DatabaseManager($this->dbConnection);
    });

    it("Debería llamar al método query con los parámetros correctos", function() {
        // Stub: cuando se llame a query, devolver true
        allow($this->dbConnection)->toReceive('query')->andReturn(true);

        // Ejecutamos el método
        $result = $this->dbManager->fetchData('users');

        // Comprobamos el resultado
        expect($result)->toBeAn('array');
    });

    it("Debería manejar un error la tabla no existe", function() {
        // Stub: cuando se llame a query, lanzar excepción
        allow($this->dbConnection)->toReceive('query')->andRun(function($sql) {
            throw new \Exception("Database connection failed");
        });

        // Verificamos que fetchData lanza la excepción esperada
        expect(function() {
            $this->dbManager->fetchData('user');
        })->toThrow(new \Exception("Table not found"));

    });

});
?>