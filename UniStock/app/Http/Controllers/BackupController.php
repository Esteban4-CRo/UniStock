<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class BackupController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function download()
    {
        if (!Auth::user()->isSuperUsuario()) {
            abort(403, 'No tienes permisos para realizar copias de seguridad.');
        }

        $dbPath = database_path('database.sqlite');

        if (!file_exists($dbPath)) {
            return back()->with('error', 'Base de datos no encontrada.');
        }

        $filename = 'Backup_UniStock_' . now()->format('Ymd_His') . '.sqlite';

        return response()->download($dbPath, $filename, [
            'Content-Type' => 'application/x-sqlite3',
        ]);
    }
}
