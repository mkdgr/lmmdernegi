<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function create()
    {
        set_alternates('contact.create');

        return view('forms.contact');
    }

    public function store(Request $request)
    {
        if ($this->isSpam($request)) {
            return redirect(lroute('contact.create'))->with('sent', true);
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email:rfc', 'max:160'],
            'phone' => ['nullable', 'string', 'max:30'],
            'subject' => ['nullable', 'string', 'max:160'],
            'message' => ['required', 'string', 'min:5', 'max:5000'],
            'kvkk' => ['accepted'],
        ]);

        $msg = ContactMessage::create([
            ...collect($data)->except('kvkk')->all(),
            'consent_at' => now(),
            'ip' => $request->ip(),
            'locale' => app()->getLocale(),
        ]);

        $this->notifyAdmin('Web sitesinden yeni mesaj', [
            'Ad soyad' => $msg->name, 'E-posta' => $msg->email, 'Telefon' => $msg->phone,
            'Konu' => $msg->subject, 'Mesaj' => $msg->message,
        ], url('/admin/contact-messages/'.$msg->id.'/edit'), $msg->email);

        return redirect(lroute('contact.create'))->with('sent', true);
    }
}
