<?php

namespace App\Http\Controllers;

use App\Models\NewsletterSubscriber;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class NewsletterController extends Controller
{
    public function store(Request $request)
    {
        if ($this->isSpam($request)) {
            return back()->with('newsletter', true);
        }

        $data = $request->validateWithBag('newsletter', [
            'email' => ['required', 'email:rfc', 'max:160'],
            'kvkk' => ['accepted'],
        ]);

        $sub = NewsletterSubscriber::firstOrNew(['email' => mb_strtolower($data['email'])]);
        $sub->fill([
            'locale' => app()->getLocale(),
            'consent_at' => now(),
            'unsubscribed_at' => null,
            'ip' => $request->ip(),
        ]);
        $sub->token ??= Str::random(48);
        $sub->save();

        return back()->with('newsletter', true)->withFragment('bulten');
    }

    public function unsubscribe(string $token)
    {
        $sub = NewsletterSubscriber::where('token', $token)->first();
        $sub?->update(['unsubscribed_at' => now()]);

        return view('message', [
            'title' => __('Bülten aboneliğiniz sonlandırıldı'),
            'text' => __('Artık size bülten göndermeyeceğiz. Fikrinizi değiştirirseniz ana sayfadan yeniden abone olabilirsiniz.'),
        ]);
    }
}
