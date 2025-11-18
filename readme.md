
# 🧪 Proyecto de pruebas con Kahlan y Patrón Repositorio

Este proyecto utiliza **Composer** para la gestión de dependencias y **Kahlan** como framework de pruebas unitarias estilo BDD para PHP.  
Además, aplica el **patrón repositorio** para desacoplar la lógica de negocio de la lógica de persistencia, logrando un flujo de trabajo limpio, escalable y fácil de probar.

---

## 🚀 Instalación de Composer (global)

1. Instalar dependencias necesarias:
   ```bash
   sudo apt update
   sudo apt install php-cli unzip curl -y
   ```

2. Descargar el instalador oficial:
   ```bash
   curl -sS https://getcomposer.org/installer -o composer-setup.php
   ```

3. Verificar el instalador:
   ```bash
   HASH=$(curl -sS https://composer.github.io/installer.sig)
   php -r "if (hash_file('SHA384', 'composer-setup.php') === '$HASH') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
   ```

4. Instalar Composer en `/opt/composer` y hacerlo accesible globalmente:
   ```bash
   sudo mkdir -p /opt/composer
   sudo php composer-setup.php --install-dir=/opt/composer --filename=composer
   sudo ln -s /opt/composer/composer /usr/local/bin/composer
   ```

5. Verificar instalación:
   ```bash
   composer --version
   ```

---

## 📦 Instalación de Kahlan

1. Inicializar Composer en el proyecto:
   ```bash
   composer init
   ```

2. Instalar Kahlan como dependencia de desarrollo:
   ```bash
   composer require --dev kahlan/kahlan
   ```

3. Ejecutar Kahlan:
   ```bash
   vendor/bin/kahlan
   ```

---

## ⚙️ Configuración de Autoload en `composer.json`

Ejemplo de configuración mínima:

```json
{
  "autoload": {
    "psr-4": {
      "App\\": "src/"
    }
  },
  "require-dev": {
    "kahlan/kahlan": "^5.0"
  }
}
```

Después ejecuta:
```bash
composer dump-autoload
```

Esto asegura que las clases en `src/` se carguen automáticamente bajo el namespace `App`.

---

## ⚙️ Configuración de Kahlan (`kahlan-config.php`)

```php
<?php
use Kahlan\Plugin\Double;

require 'vendor/autoload.php';

$config = [
    'autoload' => 'src',        // Carpeta donde está el código fuente
    'specs'    => 'spec'        // Carpeta donde están las pruebas
];

return $config;
```

---

# Proyecto de ejemplo: Arquitectura limpia + Testing con Kahlan

Este proyecto demuestra cómo organizar una aplicación PHP con **arquitectura por capas** y cómo escribir pruebas unitarias con **Kahlan**.  
El dominio principal es la gestión de usuarios (`User`), con reglas de negocio simples y persistencia simulada en memoria.

---

## 📂 Estructura del proyecto

```
src/
├── Domain/                        # Capa de dominio: entidades y contratos
│   ├── Entity/
│   │   └── User.php               # Entidad User: datos y lógica propia
│   └── Repository/
│       └── UserRepository.php     # Interfaz repositorio: contrato para persistencia
├── Application/                   # Capa de aplicación: casos de uso y validadores
│   └── Service/
│       └── UserService.php        # Servicio: aplica reglas de negocio y coordina repositorios
│   └── Validator/
│       └── EmailValidator.php     # Validador: lógica de validación (ej. correos electrónicos)
├── Infrastructure/                # Capa de infraestructura: detalles técnicos
│   └── Persistence/
│       ├── DatabaseConnection.php # Simulación de conexión a base de datos (en memoria)
│       └── UserDatabaseRepository.php # Implementación concreta del repositorio usando DatabaseConnection
└── EmailValidator.php             # (Duplicado anterior, ahora debería estar en Application/Validator)

spec/                              # Carpeta de pruebas con Kahlan
├── UserServiceSpec.php            # Pruebas de reglas de negocio en UserService
├── UserRepositorySpec.php         # Pruebas de persistencia en UserDatabaseRepository
└── EmailValidatorSpec.php         # Pruebas de validación de correos 
```

---


## 🏛️ Arquitectura por capas

### 1. Dominio
Contiene las **entidades** y **contratos**. No depende de nada externo.

#### 📂 src/Domain/Entity/User.php
```php
namespace App\Domain\Entity;

class User {
    private int $id;
    private string $nombre;

    public function __construct(int $id, string $nombre) {
        $this->id = $id;
        $this->nombre = $nombre;
    }

    public function getId(): int { return $this->id; }
    public function getNombre(): string { return $this->nombre; }
    public function setNombre(string $nombre): void { $this->nombre = $nombre; }
}
```

#### 📂 src/Domain/Repository/UserRepository.php
```php
namespace App\Domain\Repository;

use App\Domain\Entity\User;

interface UserRepository {
    public function findById(int $id): ?User;
    public function findAll(): array;
    public function save(User $user): void;
    public function deleteById(int $id): void;
}
```

---

### 2. Aplicación
Define los **casos de uso** y aplica reglas de negocio.

#### 📂 src/Application/Service/UserService.php
```php
namespace App\Application\Service;

use App\Domain\Entity\User;
use App\Domain\Repository\UserRepository;

class UserService {
    private UserRepository $repo;

    public function __construct(UserRepository $repo) {
        $this->repo = $repo;
    }

    public function registerUser(User $user): void {
        if ($user->getId() < 1) {
            throw new \DomainException("El ID del usuario debe ser mayor que 0");
        }
        if (empty($user->getNombre())) {
            throw new \DomainException("El nombre no puede estar vacío");
        }
        $this->repo->save($user);
    }

    public function listUsers(): array {
        return $this->repo->findAll();
    }

    public function deleteUser(int $id): void {
        $this->repo->deleteById($id);
    }
}
```

---

### 3. Infraestructura
Implementa detalles técnicos como persistencia.

#### 📂 src/Infrastructure/Persistence/DatabaseConnection.php
```php
namespace App\Infrastructure\Persistence;

use App\Domain\Entity\User;

class DatabaseConnection {
    private array $data = [
        ['id' => 1, 'nombre' => 'Carlos'],
        ['id' => 2, 'nombre' => 'Ana'],
    ];

    public function queryArray(string $sql): array { return $this->data; }
    public function query(int $id): ?array {
        foreach ($this->data as $row) if ($row['id'] === $id) return $row;
        return null;
    }
    public function insertOrUpdate(User $user): void {
        foreach ($this->data as &$row) {
            if ($row['id'] === $user->getId()) { $row['nombre'] = $user->getNombre(); return; }
        }
        $this->data[] = ['id' => $user->getId(), 'nombre' => $user->getNombre()];
    }
    public function deleteById(int $id): void {
        foreach ($this->data as $key => $row) if ($row['id'] === $id) unset($this->data[$key]);
    }
}
```

#### 📂 src/Infrastructure/Persistence/UserDatabaseRepository.php
```php
namespace App\Infrastructure\Persistence;

use App\Domain\Entity\User;
use App\Domain\Repository\UserRepository;

class UserDatabaseRepository implements UserRepository {
    private DatabaseConnection $db;

    public function __construct(DatabaseConnection $db) { $this->db = $db; }

    public function findById(int $id): ?User {
        $row = $this->db->query($id);
        return $row ? new User($row['id'], $row['nombre']) : null;
    }

    public function findAll(): array {
        $rows = $this->db->queryArray("SELECT * FROM users");
        return array_map(fn($row) => new User($row['id'], $row['nombre']), $rows);
    }

    public function save(User $user): void { $this->db->insertOrUpdate($user); }
    public function deleteById(int $id): void { $this->db->deleteById($id); }
}
```

---

### 4. Utilidades
Ejemplo de validador de email.

#### 📂 src/EmailValidator.php
```php
namespace App;

class EmailValidator {
    public function validateEmail(string $email): bool {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
}
```

---

## 🧪 Testing con Kahlan

### 📂 spec/UserRepositorySpec.php
Prueba la persistencia simulada.
```php
use App\Domain\Entity\User;
use App\Infrastructure\Persistence\DatabaseConnection;
use App\Infrastructure\Persistence\UserDatabaseRepository;

describe("UserDatabaseRepository", function () {
    beforeEach(function () { $this->userRepo = new UserDatabaseRepository(new DatabaseConnection()); });

    it("devuelve un User existente", function () {
        $user = $this->userRepo->findById(1);
        expect($user)->toBeAnInstanceOf(User::class);
        expect($user->getNombre())->toBe("Carlos");
    });

    it("devuelve null si no existe", function () {
        expect($this->userRepo->findById(999))->toBe(null);
    });

    it("guarda y recupera un nuevo User", function () {
        $nuevo = new User(3, "Lucía");
        $this->userRepo->save($nuevo);
        expect($this->userRepo->findById(3)->getNombre())->toBe("Lucía");
    });
});
```

---

### 📂 spec/UserServiceSpec.php
Prueba reglas de negocio.
```php
use App\Application\Service\UserService;
use App\Domain\Entity\User;
use App\Infrastructure\Persistence\DatabaseConnection;
use App\Infrastructure\Persistence\UserDatabaseRepository;

describe("UserService", function () {
    beforeEach(function () { $this->service = new UserService(new UserDatabaseRepository(new DatabaseConnection())); });

    it("lanza excepción si ID < 1", function () {
        $user = new User(0, "Prueba");
        expect(fn() => $this->service->registerUser($user))
            ->toThrow(new DomainException("El ID del usuario debe ser mayor que 0"));
    });

    it("lanza excepción si nombre vacío", function () {
        $user = new User(2, "");
        expect(fn() => $this->service->registerUser($user))
            ->toThrow(new DomainException("El nombre no puede estar vacío"));
    });
});
```

---

### 📂 spec/EmailValidatorSpec.php
```php
use App\EmailValidator;

describe("EmailValidator", function() {
    it("devuelve true para correo válido", function() {
        expect((new EmailValidator())->validateEmail("usuario@dominio.com"))->toBe(true);
    });
    it("devuelve false para correo inválido", function() {
        expect((new EmailValidator())->validateEmail("correo-invalido"))->toBe(false);
    });
});
```

---

## 🔑 Patrones y principios aplicados

- **Repositorio**: separa contrato (`UserRepository`) de implementación (`UserDatabaseRepository`).  
- **Inyección de dependencias**: `UserService` recibe un repositorio en el constructor.  
- **Mocks/Doubles**: se pueden usar en tests para aislar el servicio de la persistencia.  
- **SOLID**:  
  - SRP: cada clase tiene una responsabilidad única.  
  - DIP: el servicio depende de una abstracción, no de una implementación concreta.  
- **DRY**: lógica no duplicada.  
- **KISS**: persistencia simple en memoria para facilitar pruebas.  

---

## 🚀 Cómo ejecutar los tests

Instalar dependencias:
```bash
composer install
```

Ejecutar Kahlan:
```bash
vendor/bin/kahlan
```

---

## ✅ Conclusión

Este proyecto muestra cómo:
- Organizar el código en capas para separar responsabilidades.  
- Aplicar patrones como repositorio e inyección de dependencias.  
- Usar Kahlan para escribir specs claros y expresivos
