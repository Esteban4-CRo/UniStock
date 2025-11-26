# Diagrama de Secuencia: Crear Usuario

```mermaid
sequenceDiagram
    actor Usuario
    participant Vista as usuarios/create.blade.php
    participant Controlador as UserController
    participant Modelo as User
    participant ModeloProfile as UserProfile
    participant ModeloProveedor as Proveedor
    participant BD as Base de Datos

    Usuario->>Vista: Accede a /usuarios/create
    Vista->>Controlador: GET /usuarios/create (create)
    Controlador-->>Vista: Retorna formulario vacío
    Vista-->>Usuario: Muestra formulario de registro
    
    Usuario->>Vista: Completa formulario y envía
    Vista->>Controlador: POST /usuarios (store)
    Controlador->>Controlador: Validar datos del request
    
    alt Validación exitosa
        Controlador->>Controlador: Hash::make(password)
        Controlador->>Modelo: User::create([datos])
        Modelo->>BD: INSERT INTO users...
        BD-->>Modelo: Retorna usuario creado
        Modelo-->>Controlador: $user
        
        alt Si tiene foto
            Controlador->>Controlador: $request->file('photo')->store()
            Controlador->>Modelo: $user->update(['photo' => $path])
            Modelo->>BD: UPDATE users SET photo...
        end
        
        alt Si rol es Proveedor
            Controlador->>ModeloProveedor: Proveedor::create([user_id, empresa...])
            ModeloProveedor->>BD: INSERT INTO proveedores...
        else Si rol NO es Proveedor
            Controlador->>ModeloProfile: UserProfile::create([user_id, telefono...])
            ModeloProfile->>BD: INSERT INTO user_profiles...
        end
        
        Controlador-->>Vista: redirect()->route('usuarios.index')->with('success')
        Vista-->>Usuario: Redirige con mensaje de éxito
    else Validación fallida
        Controlador-->>Vista: redirect()->back()->withErrors()
        Vista-->>Usuario: Muestra errores en formulario
    end
```

## Descripción
Muestra el proceso completo de creación de un usuario, incluyendo validación, hash de contraseña, subida de foto, y creación condicional de perfil de proveedor o usuario regular.
