# Diagrama de Secuencia: Ver Detalles de Usuario

```mermaid
sequenceDiagram
    actor Usuario
    participant Vista as usuarios/show.blade.php
    participant Controlador as UserController
    participant Modelo as User
    participant BD as Base de Datos
    participant GoogleMaps as Google Maps API

    Usuario->>Vista: Accede a /usuarios/{id}
    Vista->>Controlador: GET /usuarios/{id} (show)
    Controlador->>Modelo: User::with(['userProfile', 'proveedor'])->findOrFail(id)
    Modelo->>BD: SELECT * FROM users WHERE id = ? JOIN...
    BD-->>Modelo: Retorna usuario con relaciones
    Modelo-->>Controlador: $user
    Controlador-->>Vista: Retorna view('usuarios.show', compact('user'))
    Vista-->>Usuario: Muestra información detallada
    
    alt Si es Proveedor y tiene ubicación
        Vista->>GoogleMaps: Cargar Maps API con coordenadas
        GoogleMaps-->>Vista: Renderiza mapa con marcador
        Vista-->>Usuario: Muestra mapa de ubicación
    end
```

## Descripción
Diagrama que representa la visualización de detalles de un usuario específico, incluyendo la carga de su perfil completo y, si es proveedor, su ubicación en Google Maps.
