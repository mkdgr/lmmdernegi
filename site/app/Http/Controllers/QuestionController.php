<?php

namespace App\Http\Controllers;

use App\Models\Disease;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class QuestionController extends Controller
{
    public function create()
    {
        set_alternates('ask.create');

        return view('forms.ask', ['diseases' => Disease::published()->orderBy('sort')->get()]);
    }

    public function store(Request $request)
    {
        if ($this->isSpam($request)) {
            return redirect(lroute('ask.create'))->with('sent', true);
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email:rfc', 'max:160'],
            'phone' => ['nullable', 'string', 'max:30'],
            'relation' => ['required', Rule::in(array_keys(Question::RELATIONS))],
            'disease_id' => ['nullable', 'exists:diseases,id'],
            'question' => ['required', 'string', 'min:10', 'max:5000'],
            'kvkk' => ['accepted'],
        ]);

        $question = Question::create([
            ...collect($data)->except('kvkk')->all(),
            'consent_at' => now(),
            'ip' => $request->ip(),
            'locale' => app()->getLocale(),
        ]);

        $this->notifyAdmin('Uzmana sorulan yeni soru', [
            'Ad soyad' => $question->name,
            'E-posta' => $question->email,
            'Telefon' => $question->phone,
            'Yakınlık' => Question::RELATIONS[$question->relation] ?? $question->relation,
            'Hastalık' => $question->disease?->getTranslation('name', 'tr'),
            'Soru' => $question->question,
        ], url('/admin/questions/'.$question->id.'/edit'), $question->email);

        return redirect(lroute('ask.create'))->with('sent', true);
    }
}
