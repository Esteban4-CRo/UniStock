# Diagrama de Secuencia: Eliminar Usuario

```mermaid
sequenceDiagram
    actor Usuario
    participant Vista as usuarios/index.blade.php
    participant Controlador as UserController
    participant Modelo as User
    participant BD as Base de Datos

    Usuario->>Vista: Click en botón Eliminar
    Vista->>Vista: Confirmar con SweetAlert2
    
    alt Usuario confirma eliminación
        Vista->>Controlador: DELETE /usuarios/{id} (destroy)
        Controlador->>Modelo: User::findOrFail(id)
        Modelo->>BD: SELECT * FROM users WHERE id = ?
        BD-->>Modelo: Retorna usuario
        Modelo-->>Controlador: $user
        
        Controlador->>Controlador: Verificar si es Super Usuario
        
        alt NO es Super Usuario
            alt Usuario tiene foto
                Controlador->>Controlador: Storage::delete($user->photo)
            end
            
            Controlador->>Modelo: $user->delete()
            Modelo->>BD: DELETE FROM users WHERE id = ?
            
            Note over Modelo,BD: Laravel elimina automáticamente<br/>registros relacionados<br/>(cascade delete)
            
            BD->>BD: DELETE FROM user_profiles WHERE user_id = ?
            BD->>BD: DELETE FROM proveedores WHERE user_id = ?
            
            BD-->>Controlador: Confirmación
            Controlador-->>Vista: redirect()->back()->with('success')
            Vista-->>Usuario: Muestra mensaje de éxito
        else Es Super Usuario
            Controlador-->>Vista: redirect()->back()->with('error')
            Vista-->>Usuario: Muestra error (no se puede eliminar)
        end
    else Usuario cancela
        Vista-->>Usuario: No hace nada
    end
```

## Descripción
Flujo de eliminación de un usuario con validación de permisos (protección del Super Usuario), eliminación en cascada de relaciones, y limpieza de archivos asociados.
