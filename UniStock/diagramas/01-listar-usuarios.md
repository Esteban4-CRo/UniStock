# Diagrama de Secuencia: Listar Usuarios

```mermaid
sequenceDiagram
    actor Usuario
    participant Vista as usuarios/index.blade.php
    participant Controlador as UserController
    participant Modelo as User
    participant BD as Base de Datos

    Usuario->>Vista: Accede a /usuarios
    Vista->>Controlador: GET /usuarios (index)
    Controlador->>Modelo: User::with(['userProfile', 'proveedor'])
    Modelo->>BD: SELECT * FROM users JOIN...
    BD-->>Modelo: Retorna usuarios con relaciones
    Modelo-->>Controlador: Collection de usuarios
    Controlador->>Controlador: Aplicar filtros (search, role, estado)
    Controlador->>Controlador: Paginar resultados (15 por página)
    Controlador-->>Vista: Retorna view('usuarios.index', $usuarios)
    Vista-->>Usuario: Muestra tabla con usuarios filtrados
```

## Descripción
Este diagrama muestra el flujo cuando un usuario accede al listado de usuarios, incluyendo la búsqueda, filtrado por rol/estado y paginación.
AIzaSyDS6XskwedP3QO0Y41TidY0Jc-JNbH_jP4