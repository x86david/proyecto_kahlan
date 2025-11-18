
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

## 📂 Estructura del proyecto

```
proyecto_kahlan/
├── composer.json
├── kahlan-config.php
├── src/
│   ├── EmailValidator.php
│   ├── Entity/
│   │   └── User.php
│   ├── Repository/
│   │   ├── UserRepository.php
│   │   └── UserDatabaseRepository.php
│   └── DatabaseConnection.php
└── spec/
    ├── EmailValidatorSpec.php
    └── UserRepositorySpec.php
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

## 📖 Patrón Repositorio

El **patrón repositorio** organiza el acceso a datos en aplicaciones orientadas a objetos.  
Su idea principal es **separar la lógica de negocio de la lógica de persistencia**:

- La **lógica de negocio** trabaja con objetos (`User`).
- La **lógica de persistencia** (repositorio) se encarga de obtener y guardar esos objetos en la base de datos (o en memoria, o en un API).

### Beneficios
- **Desacoplamiento**: el código de negocio no depende de cómo se accede a los datos.  
- **Testabilidad**: podemos sustituir la implementación real por una simulada en pruebas.  
- **Flexibilidad**: podemos tener varias implementaciones (`UserDatabaseRepository`, `UserInMemoryRepository`).  
- **Claridad**: el repositorio define un contrato claro (`UserRepository`) que todas las implementaciones deben cumplir.  

---

## 📄 Ejemplo aplicado

### Entidad de dominio (`User`)
```php
namespace App\Entity;

class User {
    private int $id;
    private string $nombre;

    public function __construct(int $id, string $nombre) {
        $this->id = $id;
        $this->nombre = $nombre;
    }

    public function getId(): int { return $this->id; }
    public function getNombre(): string { return $this->nombre; }
}
```

### Interfaz del repositorio (`UserRepository`)
```php
namespace App\Repository;

use App\Entity\User;

interface UserRepository {
    public function findById(int $id): ?User;
    public function findAll(): array;
    public function save(User $user): void;
}
```

### Implementación con base de datos simulada (`UserDatabaseRepository`)
```php
namespace App\Repository;

use App\DatabaseConnection;
use App\Entity\User;

class UserDatabaseRepository implements UserRepository {
    private DatabaseConnection $db;

    public function __construct(DatabaseConnection $db) {
        $this->db = $db;
    }

    public function findById(int $id): ?User {
        $row = $this->db->query($id);
        return $row ? new User($row['id'], $row['nombre']) : null;
    }

    public function findAll(): array {
        $rows = $this->db->queryArray("SELECT * FROM users");
        return array_map(fn($row) => new User($row['id'], $row['nombre']), $rows);
    }

    public function save(User $user): void {
        if ($user->getId() < 0) {
            throw new \InvalidArgumentException("El ID del usuario no puede ser negativo");
        }
        $this->db->insertOrUpdate($user);
    }
}
```

### Conexión simulada (`DatabaseConnection`)
```php
namespace App;

use App\Entity\User;

class DatabaseConnection {
    private array $data = [
        ['id' => 1, 'nombre' => 'Carlos'],
        ['id' => 2, 'nombre' => 'Ana'],
    ];

    public function queryArray(string $sql): array {
        return $this->data;
    }

    public function query(int $id): ?array {
        foreach ($this->data as $row) {
            if ($row['id'] === $id) return $row;
        }
        return null;
    }

    public function insertOrUpdate(User $user): void {
        foreach ($this->data as &$row) {
            if ($row['id'] === $user->getId()) {
                $row['nombre'] = $user->getNombre();
                return;
            }
        }
        $this->data[] = ['id' => $user->getId(), 'nombre' => $user->getNombre()];
    }
}
```

---

## 🧪 Pruebas con Kahlan

Ejemplo de prueba para el repositorio:

```php
use Kahlan\Plugin\Double;
use App\Repository\UserDatabaseRepository;
use App\Entity\User;

describe("UserRepository", function() {
    beforeEach(function() {
        $this->dbConnection = Double::instance(['extends' => DatabaseConnection::class]);
        $this->userRepo = new UserDatabaseRepository($this->dbConnection);
    });

    it("Devuelve un User cuando se encuentra por ID", function() {
        allow($this->dbConnection)->toReceive('query')->andReturn(['id' => 1, 'nombre' => 'Carlos']);
        $user = $this->userRepo->findById(1);
        expect($user->getNombre())->toBe('Carlos');
    });
});
```

---

## ▶️ Ejecutar las pruebas

<<<<<<< HEAD
```bash
vendor/bin/kahlan
```

Salida esperada:
```
UserRepository
  ✓ Devuelve un User cuando se encuentra por ID

Passed 1 of 1 PASS in 0.02 seconds
```

---

## ✅ Conclusión

Con este proyecto tienes:
- Composer instalado globalmente.
- Kahlan configurado como dependencia de desarrollo.
- Autoload de Composer apuntando a `src/`.
- Configuración de Kahlan en `kahlan-config.php`.
- Aplicación del **patrón repositorio** para desacoplar negocio y persistencia.
- Pruebas unitarias con Kahlan que validan el comportamiento de tus repositorios.

Esto asegura un flujo de trabajo **limpio, escalable y fácil de presentar** en tu proyecto de pruebas.
=======
El proyecto aplica el **patrón repositorio** para:
- Definir un contrato (`UserRepository`).
- Implementar una versión concreta (`UserDatabaseRepository`).
- Simular la base de datos (`DatabaseConnection`).
- Facilitar pruebas unitarias con Kahlan gracias a la **inyección de dependencias**.
- Podríamos hacer una separación más exhaustiva dejando la lógica de negocio como validaciones, lanzar excepciones desde un UserService
---

Perfecto 🙌, aquí tienes un snippet listo para añadir a tu README que muestra cómo introducir un **UserService** para separar la lógica de negocio de la persistencia. Esto complementa tu conclusión y deja claro dónde deberían ir las validaciones:


## 🛠️ Ejemplo de UserService

Para mantener una separación más clara entre **lógica de negocio** y **persistencia**, podemos introducir un `UserService`.  
El servicio aplica reglas de negocio (validaciones, excepciones) antes de delegar en el repositorio.

```php
namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;

class UserService {
    private UserRepository $repo;

    public function __construct(UserRepository $repo) {
        $this->repo = $repo;
    }

    public function registerUser(User $user): void {
        // ✅ Lógica de negocio: validación
        if ($user->getId() <= 1) {
            throw new \DomainException("El ID del usuario debe ser mayor que 1");
        }

        if (empty($user->getNombre())) {
            throw new \DomainException("El nombre no puede estar vacío");
        }

        // 👉 Delegamos en el repositorio para persistir
        $this->repo->save($user);
    }

    public function listUsers(): array {
        return $this->repo->findAll();
    }
}
```

### 🔑 Puntos clave
- El **UserService** aplica reglas de negocio (validaciones, restricciones).
- El **UserRepository** se limita a la persistencia (guardar, buscar, listar).
- Esto permite que las pruebas unitarias validen tanto la lógica de negocio como la persistencia de forma independiente.


---
>>>>>>> b69597d (readme)
