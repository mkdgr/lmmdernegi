<?php

namespace App\Http\Controllers;

use App\Models\MembershipApplication;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MembershipController extends Controller
{
    public function create()
    {
        set_alternates('membership.create');

        return view('forms.membership', [
            'info' => Page::published()->where('legacy_id', 22)->first(),
        ]);
    }

    public function store(Request $request)
    {
        if ($this->isSpam($request)) {
            return redirect(lroute('membership.create'))->with('sent', true);
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'tckn' => ['nullable', 'digits:11'],
            'birth_date' => ['nullable', 'date', 'before:today'],
            'email' => ['required', 'email:rfc', 'max:160'],
            'phone' => ['required', 'string', 'max:30'],
            'city' => ['nullable', 'string', 'max:60'],
            'address' => ['nullable', 'string', 'max:500'],
            'occupation' => ['nullable', 'string', 'max:120'],
            'relation' => ['nullable', Rule::in(array_keys(MembershipApplication::RELATIONS))],
            'note' => ['nullable', 'string', 'max:2000'],
            'kvkk' => ['accepted'],
        ]);

        $app = MembershipApplication::create([
            ...collect($data)->except('kvkk')->all(),
            'consent_at' => now(),
            'ip' => $request->ip(),
        ]);

        $this->notifyAdmin('Yeni üyelik başvurusu', [
            'Ad soyad' => $app->name, 'E-posta' => $app->email, 'Telefon' => $app->phone,
            'Şehir' => $app->city, 'Meslek' => $app->occupation,
            'Yakınlık' => MembershipApplication::RELATIONS[$app->relation] ?? null, 'Not' => $app->note,
        ], url('/admin/membership-applications/'.$app->id.'/edit'), $app->email);

        return redirect(lroute('membership.create'))->with('sent', true);
    }
}
