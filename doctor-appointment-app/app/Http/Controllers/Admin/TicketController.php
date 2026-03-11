<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use Illuminate\Http\Request;

/**
 * Controlador para la gestión de tickets de soporte en el panel de administración.
 * Listado, creación y almacenamiento de tickets.
 */
class TicketController extends Controller
{
    /**
     * Muestra el listado de tickets de soporte.
     */
    public function index()
    {
        return view('admin.tickets.index');
    }

    /**
     * Muestra el formulario para crear un nuevo ticket.
     */
    public function create()
    {
        return view('admin.tickets.create');
    }

    /**
     * Almacena un nuevo ticket en la base de datos.
     * El usuario asociado es el autenticado.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'required|string|max:5000',
            'priority'    => 'required|in:baja,media,alta',
        ], [], [
            'title'       => 'título del problema',
            'description' => 'descripción detallada',
            'priority'    => 'prioridad',
        ]);

        Ticket::create([
            'user_id'        => auth()->id(),
            'title'          => $data['title'],
            'description'    => $data['description'],
            'status'         => Ticket::STATUS_ABIERTO,
            'priority'       => $data['priority'],
            'admin_response' => null,
        ]);

        session()->flash('swal', [
            'icon'  => 'success',
            'title' => 'Ticket creado',
            'text'  => 'Tu ticket de soporte ha sido registrado exitosamente.',
        ]);

        return redirect()->route('admin.tickets.index');
    }

    /**
     * Muestra el detalle de un ticket.
     */
    public function show(Ticket $ticket)
    {
        $ticket->load('user');
        return view('admin.tickets.show', compact('ticket'));
    }

    /**
     * Mostrar formulario para editar un ticket (estado, prioridad, respuesta admin).
     */
    public function edit(Ticket $ticket)
    {
        $ticket->load('user');
        return view('admin.tickets.edit', compact('ticket'));
    }

    /**
     * Actualizar el ticket (estado, prioridad, respuesta del admin).
     */
    public function update(Request $request, Ticket $ticket)
    {
        $data = $request->validate([
            'status'         => 'required|in:abierto,en_proceso,cerrado',
            'priority'       => 'required|in:baja,media,alta',
            'admin_response' => 'nullable|string|max:5000',
        ], [], [
            'status'         => 'estado',
            'priority'       => 'prioridad',
            'admin_response' => 'respuesta del administrador',
        ]);

        $ticket->update($data);

        session()->flash('swal', [
            'icon'  => 'success',
            'title' => 'Ticket actualizado',
            'text'  => 'El ticket ha sido actualizado correctamente.',
        ]);

        return redirect()->route('admin.tickets.show', $ticket);
    }

    /**
     * Elimina un ticket de soporte.
     */
    public function destroy(Ticket $ticket)
    {
        $ticket->delete();
        session()->flash('swal', [
            'icon'  => 'success',
            'title' => 'Ticket eliminado',
            'text'  => 'El ticket de soporte ha sido eliminado.',
        ]);
        return redirect()->route('admin.tickets.index');
    }
}
