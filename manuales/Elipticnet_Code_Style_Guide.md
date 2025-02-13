
# Code Style Guide para Elipticnet

Este documento establece las convenciones de estilo que deben seguirse para mantener consistencia en el código del proyecto Elipticnet.

---

## 1. Estilo para Clases CSS

### Convención:
- **Formato**: Utilizar **BEM (Block Element Modifier)**.
- **Ejemplo**:
  ```css
  .boton--primario {
      background-color: blue;
  }

  .boton__icono {
      margin-right: 10px;
  }
  ```

### Reglas:
- Usar nombres en **minúsculas** con palabras separadas por guiones (`-`).

#### Ejemplos:
- **Válidos:**
  - `.menu-principal`
  - `.boton-enviar`
  - `.formulario-contacto`

- **Inválidos:**
  - `.menuPrincipal` (usa camelCase en lugar de guiones).
  - `.Menu_principal` (mezcla mayúsculas y guiones bajos).
  - `.menuPrincipal-enviar` (mezcla camelCase con guiones).

---

## 2. Estilo para IDs en HTML

### Convención:
- **Formato**: Usar **camelCase**.
- **Ejemplo**:
  ```html
  <div id="mainContent"></div>
  ```

### Reglas:
- Deben ser descriptivos y únicos.
- Emplear nombres relacionados al propósito del elemento.
- Evitar incluir estilos específicos en el nombre (e.g., `#blueBox` no es adecuado).

---

## 3. Variables en JavaScript, PHP y Python

### JavaScript
- **Formato**: Usar **camelCase**.
- **Ejemplo**:
  ```javascript
  let userName = "John";
  const maxRetries = 3;
  ```

### PHP
- **Formato**: Usar **snake_case**.
- **Ejemplo**:
  ```php
  $user_name = "John";
  $max_retries = 3;
  ```

### Python
- **Formato**: Usar **snake_case**.
- **Ejemplo**:
  ```python
  user_name = "John"
  max_retries = 3
  ```

---

## 4. Funciones en JavaScript, PHP y Python

### JavaScript
- **Formato**: Usar **camelCase**.
- **Ejemplo**:
  ```javascript
  function getUserName() {
      return "John";
  }
  ```

### PHP
- **Formato**: Usar **snake_case**.
- **Ejemplo**:
  ```php
  function get_user_name() {
      return "John";
  }
  ```

### Python
- **Formato**: Usar **snake_case**.
- **Ejemplo**:
  ```python
  def get_user_name():
      return "John"
  ```

---

## 5. Indentación

- **HTML, CSS y JavaScript**: Usar **2 espacios**.
- **PHP y Python**: Usar **4 espacios**.

---

## 6. Comentarios

### JavaScript, PHP y Python
- Usar comentarios para explicar el **por qué**, no el **qué**.
- **Ejemplo en JavaScript**:
  ```javascript
  // Calcula la suma de dos números
  function sum(a, b) {
      return a + b;
  }
  ```

- **Ejemplo en PHP**:
  ```php
  // Devuelve el nombre del usuario
  function get_user_name() {
      return "John";
  }
  ```

- **Ejemplo en Python**:
  ```python
  # Devuelve el nombre del usuario
  def get_user_name():
      return "John"
  ```

---

## 7. Estructura de Archivos

### Organización General

- **Carpeta `public/`**:
  - Contiene los archivos HTML y PHP que se sirven al usuario.
  - Ejemplo:
    ```
    public/
      index.html
      ping_agent_view.html
      user_dashboard.php
    ```

- **Carpeta `php/`**:
  - Scripts de backend, organizados en subcarpetas:
    - `API/`: Scripts que exponen servicios backend.
    - Archivos auxiliares:
      - `.env` para variables de entorno.
      - Funciones de validación y utilidades generales.
    - Ejemplo:
      ```
      php/
        API/
          ping_service.php
        helpers.php
        config.env
      ```

- **Carpeta `js/`**:
  - Contiene scripts JavaScript, organizados por funcionalidad:
    - `API/`: Archivos que manejan comunicación con el backend.
    - `components/`: Scripts asociados a componentes HTML específicos.
    - Ejemplo:
      ```
      js/
        API/
          ping_agent_view_get_data.js
        components/
          sidebar_toggle.js
        main.js
      ```

- **Carpeta `css/`**:
  - Contiene estilos CSS organizados según los nombres de los archivos HTML que los utilizan.
  - Ejemplo:
    ```
    css/
      ping_agent_view.css
      user_dashboard.css
    ```

### Convención de Nombres

- **HTML**: Nombre descriptivo en snake_case, reflejando su propósito.
  - Ejemplo: `ping_agent_view.html`

- **CSS**: Usar el mismo nombre del archivo HTML asociado.
  - Ejemplo: `ping_agent_view.css`

- **JavaScript**:
  - Para scripts API: Nombre del HTML asociado + acción principal.
    - Ejemplo: `ping_agent_view_get_data.js`
  - Para componentes: Nombre descriptivo del componente.
    - Ejemplo: `sidebar_toggle.js`

---

## 8. Variables CSS

### Convención:
- Usar prefijos para categorizar los colores por propósito.
- **Formato**: `--categoria-subcategoria-atributo`.
- **Ejemplo**:
  ```css
  /* Alertas */
  --alert-bg: #170F2F;
  --alert-success: #B5F730;
  --alert-warning: #FFD000;
  --alert-error: #F73051;

  /* Scrollbar */
  --scrollbar-bg: #170F2F;
  --scrollbar-thumb: #392574;
  --scrollbar-hover: #392574;

  /* Tooltip */
  --tooltip-bg: #26184E;

  /* Cargadores */
  --loader-spinner: #F5F0FE88;

  /* Menús desplegables */
  --dropdown-bg: #26184E;
  --dropdown-item-hover: #392574;
  --dropdown-divider: #1E133D;

  /* Diálogos */
  --dialog-bg: #26184E;
  --dialog-header-bg: #170F2F;

  /* Colores especiales */
  --color-accent-green: #B5F730;
  --color-soft-green: #8BC81A;
  --color-accent-red: #F73051;
  --color-accent-red-hover: #D12945;
  --color-soft-red: #FF6A6A;
  --color-accent-yellow: #FFEF41;
  --color-soft-violet: #5853FF;
  --color-accent-blue: #0600EF;
  --color-accent-blue-hover: #1300D0;

  /* Otros */
  --divider-line: #5E3DC2;
  --log-divider-line: #5E3DC27C;
  ```

### Reglas:
1. Prefijos estándar:
   - `--alert-` para colores de alertas.
   - `--scrollbar-` para elementos del scrollbar.
   - `--tooltip-` para tooltips.
   - `--dropdown-` para menús desplegables.
   - `--dialog-` para diálogos.
   - `--color-` para colores generales o especiales.
   - `--divider-` para líneas divisorias.

2. Usar nombres claros y relacionados al propósito del color.
3. Agrupar variables por categoría y documentarlas dentro del archivo CSS principal.

---

## 9. Buenas Prácticas Adicionales

1. **Control de Versiones**:
   - Usar Git con convenciones claras de commits.
   - Ejemplo:
     ```
     git commit -m "fix: corrige bug en cálculo de latencia"
     ```

2. **Validaciones**:
   - Validar inputs de usuario en frontend y backend.

3. **Nombres Descriptivos**:
   - Usar nombres que sean claros y expliquen el propósito de variables, funciones y clases.
