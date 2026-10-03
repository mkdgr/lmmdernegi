<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
 * Çevrilebilir alanlar (TR/EN) JSON sütunlarda tutulur: {"tr": "...", "en": "..."}
 * (spatie/laravel-translatable). legacy_id: eski sitedeki /TR,{id}/ numarası.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('diseases', function (Blueprint $table) {
            $table->id();
            $table->string('abbr', 12);
            $table->json('abbr_translated')->nullable();
            $table->json('name');
            $table->json('slug');
            $table->string('group', 20)->default('losemi'); // losemi | lenfoma | miyelom
            $table->json('summary')->nullable();
            $table->json('body')->nullable();
            $table->json('faq')->nullable();               // [{question: {tr,en}, answer: {tr,en}}]
            $table->string('reviewed_by')->nullable();
            $table->date('reviewed_at')->nullable();
            $table->unsignedSmallInteger('sort')->default(0);
            $table->unsignedInteger('legacy_id')->nullable()->index();
            $table->boolean('is_published')->default(true);
            $table->timestamps();
        });

        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->string('section', 20)->default('kurumsal'); // kurumsal | rehber | destek | yasal
            $table->json('title');
            $table->json('slug');
            $table->json('summary')->nullable();
            $table->json('body')->nullable();
            $table->string('image')->nullable();
            $table->string('icon', 40)->nullable();
            $table->unsignedSmallInteger('sort')->default(0);
            $table->unsignedInteger('legacy_id')->nullable()->index();
            $table->boolean('is_published')->default(true);
            $table->timestamps();
        });

        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string('type', 20)->default('haber'); // haber | etkinlik | duyuru | bilimsel
            $table->string('audience', 20)->nullable();   // hasta | hekim | herkes
            $table->json('title');
            $table->json('slug');
            $table->json('excerpt')->nullable();
            $table->json('body')->nullable();
            $table->string('image')->nullable();
            $table->dateTime('event_starts_at')->nullable();
            $table->dateTime('event_ends_at')->nullable();
            $table->json('location')->nullable();
            $table->string('video_url')->nullable();
            $table->string('registration_url')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_published')->default(true);
            $table->dateTime('published_at')->nullable()->index();
            $table->unsignedInteger('legacy_id')->nullable()->index();
            $table->timestamps();
        });

        Schema::create('stories', function (Blueprint $table) {
            $table->id();
            $table->string('kind', 20)->default('iyilesen'); // iyilesen | yasayan | yakin
            $table->json('title');
            $table->json('slug');
            $table->string('person_name');
            $table->json('condition')->nullable();
            $table->json('quote')->nullable();
            $table->json('body')->nullable();
            $table->string('photo')->nullable();
            $table->boolean('has_consent')->default(false);
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_published')->default(false);
            $table->dateTime('published_at')->nullable();
            $table->unsignedInteger('legacy_id')->nullable()->index();
            $table->timestamps();
        });

        Schema::create('publications', function (Blueprint $table) {
            $table->id();
            $table->string('kind', 20)->default('brosur'); // brosur | bulten | rapor
            $table->json('title');
            $table->json('description')->nullable();
            $table->string('file')->nullable();
            $table->string('external_url')->nullable();
            $table->string('cover')->nullable();
            $table->unsignedSmallInteger('issue_no')->nullable();
            $table->date('published_on')->nullable();
            $table->boolean('is_published')->default(true);
            $table->unsignedInteger('legacy_id')->nullable()->index();
            $table->timestamps();
        });

        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('phone', 30)->nullable();
            $table->string('relation', 20); // hasta | yakin | diger
            $table->foreignId('disease_id')->nullable()->constrained()->nullOnDelete();
            $table->text('question');
            $table->string('status', 20)->default('yeni'); // yeni | yanitlandi | arsiv
            $table->text('answer')->nullable();
            $table->foreignId('answered_by')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('answered_at')->nullable();
            $table->timestamp('consent_at');
            $table->string('ip', 45)->nullable();
            $table->string('locale', 5)->default('tr');
            $table->timestamps();
        });

        Schema::create('contact_messages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('phone', 30)->nullable();
            $table->string('subject')->nullable();
            $table->text('message');
            $table->string('status', 20)->default('yeni');
            $table->timestamp('consent_at');
            $table->string('ip', 45)->nullable();
            $table->string('locale', 5)->default('tr');
            $table->timestamps();
        });

        Schema::create('membership_applications', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('tckn')->nullable(); // şifreli
            $table->date('birth_date')->nullable();
            $table->string('email');
            $table->string('phone', 30);
            $table->string('city', 60)->nullable();
            $table->text('address')->nullable();
            $table->string('occupation')->nullable();
            $table->string('relation', 20)->nullable(); // hasta | yakin | hekim | gonullu
            $table->text('note')->nullable();
            $table->string('status', 20)->default('yeni'); // yeni | onaylandi | reddedildi
            $table->timestamp('consent_at');
            $table->string('ip', 45)->nullable();
            $table->timestamps();
        });

        Schema::create('newsletter_subscribers', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique();
            $table->string('locale', 5)->default('tr');
            $table->timestamp('consent_at');
            $table->timestamp('unsubscribed_at')->nullable();
            $table->string('token', 64)->unique();
            $table->string('ip', 45)->nullable();
            $table->timestamps();
        });

        Schema::create('donations', function (Blueprint $table) {
            $table->id();
            $table->string('order_id', 40)->unique();
            $table->string('type', 20)->default('bagis'); // bagis | aidat | giris
            $table->string('frequency', 20)->default('tek'); // tek | aylik
            $table->unsignedInteger('amount'); // TL (tam sayı)
            $table->string('currency', 3)->default('TRY');
            $table->string('donor_type', 20)->default('bireysel');
            $table->string('name');
            $table->string('company')->nullable();
            $table->text('tckn')->nullable(); // şifreli
            $table->string('tax_no', 20)->nullable();
            $table->string('email');
            $table->string('phone', 30)->nullable();
            $table->boolean('is_anonymous')->default(false);
            $table->string('status', 20)->default('beklemede'); // beklemede | basarili | basarisiz
            $table->string('auth_code', 20)->nullable();
            $table->string('host_ref', 40)->nullable();
            $table->string('bank_code', 10)->nullable();
            $table->string('bank_message')->nullable();
            $table->string('masked_pan', 25)->nullable();
            $table->json('bank_response')->nullable();
            $table->dateTime('paid_at')->nullable();
            $table->timestamp('consent_at');
            $table->string('ip', 45)->nullable();
            $table->string('locale', 5)->default('tr');
            $table->timestamps();
        });

        Schema::create('settings', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->json('value')->nullable();
            $table->timestamps();
        });

        Schema::create('redirects', function (Blueprint $table) {
            $table->id();
            $table->string('from_path')->unique();
            $table->string('to_path');
            $table->unsignedSmallInteger('status_code')->default(301);
            $table->unsignedInteger('hits')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        foreach (['redirects', 'settings', 'donations', 'newsletter_subscribers', 'membership_applications',
            'contact_messages', 'questions', 'publications', 'stories', 'posts', 'pages', 'diseases'] as $t) {
            Schema::dropIfExists($t);
        }
    }
};
