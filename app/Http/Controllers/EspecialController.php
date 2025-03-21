<?php

namespace App\Http\Controllers;

use App\Models\Especial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class EspecialController extends Controller {
  public function index() {
    try {
      $especiales = Especial::with('fotos')->get();
      return response()->json($especiales);
    } catch (\Exception $e) {
      Log::error('Error en index: ' . $e->getMessage());
      return response()->json(['error' => 'Error al obtener especiales'], 500);
    }
  }

  public function store(Request $request) {
    try {
      Log::info('Iniciando almacenamiento de especial', $request->all());
      $validatedData = $request->validate([
        'nombre' => 'required|string|max:255',
        'descripcion' => 'required|string',
        'categoria' => 'required|in:Textil,Promocional,Otros|string|max:255',
        'fotos.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
      ]);

      $especial = new Especial();
      $especial->nombre = $validatedData['nombre'];
      $especial->descripcion = $validatedData['descripcion'];
      $especial->categoria = $validatedData['categoria'];
      if ($request->hasFile('foto')) {
        $path = $request->file('foto')->store('public/uploads');
        $especial->foto_path = str_replace('public/', '', $path);
      }
      $especial->save();

      if ($request->hasFile('fotos')) {
        foreach ($request->file('fotos') as $foto) {
          $path = $foto->store('public/uploads');
          $fotoPath = str_replace('public/', '', $path);
          $especial->fotos()->create(['foto_path' => $fotoPath]);
        }
      }

      Log::info('Especial almacenado exitosamente', $especial->toArray());
      return response()->json($especial->load('fotos'), 201);
    } catch (\Illuminate\Validation\ValidationException $e) {
      Log::error('Validación fallida: ' . json_encode($e->errors()));
      return response()->json(['message' => 'Validación fallida', 'errors' => $e->errors()], 422);
    } catch (\Exception $e) {
      Log::error('Error en store: ' . $e->getMessage());
      return response()->json(['error' => 'Error al guardar el especial', 'details' => $e->getMessage()], 500);
    }
  }

  public function show($id) {
    try {
      $especial = Especial::with('fotos')->findOrFail($id);
      return response()->json($especial);
    } catch (\Exception $e) {
      Log::error('Error en show: ' . $e->getMessage());
      return response()->json(['error' => 'Especial no encontrado'], 404);
    }
  }

  public function update(Request $request, $id) {
    try {
      Log::info('Iniciando actualización de especial con ID: ' . $id, $request->all());
      $validatedData = $request->validate([
        'nombre' => 'required|string|max:255',
        'descripcion' => 'required|string',
        'categoria' => 'required|in:Textil,Promocional,Otros|string|max:255',
        'fotos.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
      ]);

      $especial = Especial::findOrFail($id);
      $especial->nombre = $validatedData['nombre'];
      $especial->descripcion = $validatedData['descripcion'];
      $especial->categoria = $validatedData['categoria'];
      $especial->save();

      if ($request->hasFile('fotos')) {
        foreach ($request->file('fotos') as $foto) {
          $path = $foto->store('public/uploads');
          $fotoPath = str_replace('public/', '', $path);
          $especial->fotos()->create(['foto_path' => $fotoPath]);
        }
      }

      Log::info('Especial actualizado exitosamente', $especial->toArray());
      return response()->json($especial->load('fotos'));
    } catch (\Illuminate\Validation\ValidationException $e) {
      Log::error('Validación fallida: ' . json_encode($e->errors()));
      return response()->json(['message' => 'Validación fallida', 'errors' => $e->errors()], 422);
    } catch (\Exception $e) {
      Log::error('Error en update: ' . $e->getMessage());
      return response()->json(['error' => 'Error al actualizar el especial', 'details' => $e->getMessage()], 500);
    }
  }

  public function destroy($id) {
    try {
      $especial = Especial::findOrFail($id);
      $especial->delete();
      return response()->json(null, 204);
    } catch (\Exception $e) {
      Log::error('Error en destroy: ' . $e->getMessage());
      return response()->json(['error' => 'Error al eliminar el especial'], 500);
    }
  }
}