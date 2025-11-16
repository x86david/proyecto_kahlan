
```markdown
# Proyecto de pruebas con Kahlan

Este proyecto utiliza **Composer** para la gestión de dependencias y **Kahlan** como framework de pruebas unitarias estilo BDD para PHP.

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

3. Verificar el instalador (opcional pero recomendado):
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
├── src/
│   └── EmailGenerator.php
└── spec/
    └── EmailGeneratorSpec.php
```

---

## ⚙️ Configuración de Autoload

En `composer.json` añade:

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

---

## 🖥️ Ejemplo de clase

`src/EmailGenerator.php`:
```php
<?php

namespace App;

class EmailGenerator
{
    public function generateEmail()
    {
        $name = substr(md5(uniqid(rand(), true)), 0, 10);
        $domain = 'example.com';
        return $name . '@' . $domain;
    }
}
```

---

## 🧪 Ejemplo de prueba con Kahlan

`spec/EmailGeneratorSpec.php`:
```php
<?php

use App\EmailGenerator;

describe("EmailGenerator", function() {
    it("genera un correo electrónico con el formato correcto", function() {
        $emailGenerator = new EmailGenerator();
        $email = $emailGenerator->generateEmail();

        $regex = '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/';
        expect($email)->toMatch($regex);
    });
});
```

---

## ▶️ Ejecutar las pruebas

```bash
vendor/bin/kahlan
```

Salida esperada:
```
EmailGenerator
  ✓ genera un correo electrónico con el formato correcto

Passed 1 of 1 PASS in 0.02 seconds
```

---

## ✅ Conclusión

Con estos pasos tienes:
- Composer instalado globalmente.
- Kahlan configurado como dependencia de desarrollo.
- Autoload de Composer apuntando a `src/`.
- Pruebas en `spec/` que se ejecutan con `vendor/bin/kahlan`.

```

