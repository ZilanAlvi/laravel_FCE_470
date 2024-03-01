<?php

// File: app/Http/Controllers/TicketController.php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Ticket as ModelsTicket;
use App\Ticket;

class TicketController extends Controller {
    public function purchaseTicket(Request $request, $ticketId) {
        $ticket = ModelsTicket::findOrFail($ticketId);

        if (!$ticket) {
            return redirect()->back()->with('error', 'Ticket not found');
        }

        if (!$ticket->is_available) {
            return redirect()->back()->with('error', 'Ticket is not available for purchase');
        }

        $ticket->is_available = false;
        $ticket->is_purchased = true;
        $ticket->save();

        return redirect()->route('ticket_purchased')->with('success', 'Ticket purchased successfully');
    }
}

