# Diagrama de Secuencia: Editar Usuario

```mermaid
sequenceDiagram
    actor Usuario
    participant Vista as usuarios/edit.blade.php
    participant Controlador as UserController
    participant Modelo as User
    participant ModeloProfile as UserProfile
    participant ModeloProveedor as Proveedor
    participant BD as Base de Datos

    Usuario->>Vista: Accede a /usuarios/{id}/edit
    Vista->>Controlador: GET /usuarios/{id}/edit (edit)
    Controlador->>Modelo: User::with(['userProfile', 'proveedor'])->findOrFail(id)
    Modelo->>BD: SELECT * FROM users WHERE id = ?
    BD-->>Modelo: Retorna usuario
    Modelo-->>Controlador: $user
    Controlador-->>Vista: Retorna formulario prellenado
    Vista-->>Usuario: Muestra formulario con datos actuales
    
    Usuario->>Vista: Modifica datos y envía
    Vista->>Controlador: PUT /usuarios/{id} (update)
    Controlador->>Controlador: Validar datos del request
    
    alt Validación exitosa
        alt Si cambió la contraseña
            Controlador->>Controlador: Hash::make(new_password)
        end
        
        alt Si subió nueva foto
            Controlador->>Controlador: Storage::delete(old_photo)
            Controlador->>Controlador: $request->file('photo')->store()
        end
        
        Controlador->>Modelo: $user->update([datos])
        Modelo->>BD: UPDATE users SET...
        
        alt Si tiene UserProfile
            Controlador->>ModeloProfile: $user->userProfile->update()
            ModeloProfile->>BD: UPDATE user_profiles SET...
        end
        
        alt Si tiene Proveedor
            Controlador->>ModeloProveedor: $user->proveedor->update()
            ModeloProveedor->>BD: UPDATE proveedores SET...
        end
        
        BD-->>Controlador: Confirmación
        Controlador-->>Vista: redirect()->route('usuarios.show')->with('success')
        Vista-->>Usuario: Redirige con mensaje de éxito
    else Validación fallida
        Controlador-->>Vista: redirect()->back()->withErrors()
        Vista-->>Usuario: Muestra errores
    end
```

## Descripción
Proceso de actualización de un usuario existente, incluyendo la gestión de contraseñas, fotos, y actualización en cascada de perfiles relacionados (UserProfile o Proveedor).
