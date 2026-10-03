<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;

/**
 * Yeni sitede eklenen içerikler: "Şu an neredesiniz?" rehberleri, Hakkımızda,
 * Gönüllü olun ve yasal metinler.
 *
 * ÖNEMLİ: Bu metinler taslaktır. Sağlıkla ilgili sayfalar derneğin hekimleri,
 * yasal metinler (KVKK, çerez) bir hukukçu tarafından gözden geçirilmeden yayına alınmamalıdır.
 */
class ContentSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->pages() as $p) {
            $page = Page::query()->where('section', $p['section'])->where('slug->tr', $p['slug']['tr'])->first() ?? new Page;
            $page->fill([
                'section' => $p['section'],
                'title' => $p['title'],
                'slug' => $p['slug'],
                'summary' => $p['summary'],
                'body' => $p['body'],
                'image' => $p['image'] ?? null,
                'sort' => $p['sort'],
                'is_published' => true,
            ])->save();
        }
    }

    private function pages(): array
    {
        return [
            // ---------------------------------------------------------------
            // "Şu an neredesiniz?" — hastalık sürecine göre rehberler
            // ---------------------------------------------------------------
            [
                'section' => 'rehber', 'sort' => 1,
                'title' => ['tr' => 'Endişeliyim', 'en' => 'I\'m worried'],
                'slug' => ['tr' => 'endiseliyim', 'en' => 'im-worried'],
                'summary' => ['tr' => 'Belirtilerim var ya da tetkik sonucu bekliyorum.', 'en' => 'I have symptoms or I\'m waiting for test results.'],
                'body' => [
                    'tr' => <<<'HTML'
<p class="lead">Kan tahlilinizde bir değişiklik çıktıysa ya da geçmeyen şikâyetleriniz varsa endişelenmeniz çok doğal. Bu sayfa, bu belirsiz dönemde neler yapabileceğinizi anlatıyor.</p>
<h2>Hangi belirtiler hekime başvurmayı gerektirir?</h2>
<p>Lösemi, lenfoma ve miyelomun belirtileri çoğu zaman başka, daha sık görülen hastalıklara benzer. Aşağıdaki şikâyetler <strong>birkaç haftadan uzun sürüyorsa</strong> bir hekime başvurun:</p>
<ul>
<li>Açıklanamayan halsizlik, çabuk yorulma, nefes darlığı</li>
<li>Sık tekrarlayan ya da geçmeyen enfeksiyonlar, nedeni bulunamayan ateş</li>
<li>Kolay morarma, diş eti veya burun kanaması, ciltte nokta şeklinde kanamalar</li>
<li>Boyun, koltuk altı ya da kasıkta ağrısız, büyüyen şişlikler</li>
<li>Gece terlemesi ve istemsiz kilo kaybı</li>
<li>Geçmeyen kemik ya da sırt ağrısı</li>
</ul>
<p>Bu belirtilerin olması kan kanseri olduğunuz anlamına gelmez; ancak nedenin araştırılması gerekir.</p>
<h2>Tetkikleri beklerken</h2>
<ul>
<li><strong>Sorularınızı not edin.</strong> Muayeneye giderken şikâyetlerinizi, ne zaman başladıklarını ve kullandığınız ilaçları yazılı götürün.</li>
<li><strong>Yanınızda biri olsun.</strong> Sonuçları konuşurken bir yakınınızın yanınızda olması, söylenenleri hatırlamanızı kolaylaştırır.</li>
<li><strong>İnternette okuduklarınıza dikkat edin.</strong> Her tahlil sonucu ve her belirti farklı yorumlanabilir; kesin bilgiyi hekiminiz verir.</li>
<li><strong>Kendinize iyi bakın.</strong> Uyku, beslenme ve güvendiğiniz insanlarla konuşmak bekleme sürecini kolaylaştırır.</li>
</ul>
<h2>Kan kanserleri nasıl teşhis edilir?</h2>
<p>Teşhis genellikle tam kan sayımı ve kan yayması ile başlar. Gerekirse hematoloji uzmanı kemik iliği incelemesi, lenf bezi biyopsisi ya da görüntüleme tetkikleri ister. Hastalıkların her biri için ayrıntılı bilgiyi <a href="/hastaliklar">hastalıklar</a> sayfamızda bulabilirsiniz.</p>
<h2>Konuşmak isterseniz</h2>
<p>Bu dönemde aklınıza takılan her soruyu <a href="/uzmana-sorun">uzmanlarımıza sorabilir</a> ya da bizi arayabilirsiniz. Yalnız değilsiniz.</p>
HTML,
                    'en' => <<<'HTML'
<p class="lead">If a blood test showed something unusual or you have symptoms that won't go away, it is natural to feel worried. This page explains what you can do during this uncertain time.</p>
<h2>Which symptoms should be checked by a doctor?</h2>
<p>Symptoms of leukemia, lymphoma and myeloma often look like those of other, more common illnesses. See a doctor if any of the following <strong>last longer than a few weeks</strong>:</p>
<ul>
<li>Unexplained tiredness, weakness or breathlessness</li>
<li>Frequent or persistent infections, or fever without a clear cause</li>
<li>Easy bruising, bleeding gums or nosebleeds, tiny red spots on the skin</li>
<li>Painless, growing lumps in the neck, armpit or groin</li>
<li>Night sweats and unintended weight loss</li>
<li>Persistent bone or back pain</li>
</ul>
<p>Having these symptoms does not mean you have a blood cancer, but the cause should be investigated.</p>
<h2>While you wait for test results</h2>
<ul>
<li><strong>Write down your questions</strong> and bring a list of your symptoms and medicines.</li>
<li><strong>Bring someone with you</strong> when you discuss results.</li>
<li><strong>Be careful with what you read online.</strong> Your doctor is the best source of information about your own results.</li>
<li><strong>Look after yourself</strong> — sleep, food and talking to people you trust all help.</li>
</ul>
<h2>Want to talk?</h2>
<p>You can <a href="/en/ask-an-expert">send your questions to our specialists</a> or call us. You are not alone.</p>
HTML,
                ],
            ],
            [
                'section' => 'rehber', 'sort' => 2,
                'title' => ['tr' => 'Yeni tanı aldım', 'en' => 'Newly diagnosed'],
                'slug' => ['tr' => 'yeni-tani-aldim', 'en' => 'newly-diagnosed'],
                'summary' => ['tr' => 'Ne olduğunu ve beni neyin beklediğini anlamak istiyorum.', 'en' => 'I want to understand what this means and what comes next.'],
                'body' => [
                    'tr' => <<<'HTML'
<p class="lead">Kan kanseri tanısı almak, hem sizin hem de sevdikleriniz için hayatı bir anda değiştirebilir. İlk günlerde korku, şaşkınlık ve öfke hissetmek çok doğal. Bu sayfa ilk haftalarda işinize yarayacak bilgileri bir araya getiriyor.</p>
<h2>Önce hastalığınızı tanıyın</h2>
<p>Lösemi, lenfoma ve miyelomun pek çok türü vardır ve her birinin seyri ve tedavisi farklıdır. Hekiminize hastalığınızın <strong>tam adını ve alt türünü</strong> sorun ve not alın. Ardından <a href="/hastaliklar">hastalıklar</a> sayfamızdan ilgili bölümü okuyabilirsiniz.</p>
<h2>Hekiminize sorabileceğiniz sorular</h2>
<ul>
<li>Hastalığımın tam adı ve alt türü nedir? Hangi ek tetkikler gerekiyor?</li>
<li>Tedaviye hemen başlamam gerekiyor mu? Tedavinin amacı nedir?</li>
<li>Tedavi ne kadar sürecek, hastanede yatmam gerekecek mi?</li>
<li>Olası yan etkiler nelerdir? Hangi durumlarda hemen sizi aramalıyım?</li>
<li>Çalışmaya, araç kullanmaya, seyahate devam edebilir miyim?</li>
<li>Çocuk sahibi olma planlarım varsa neler yapmalıyım?</li>
</ul>
<h2>İlk haftalarda işinizi kolaylaştıracaklar</h2>
<ul>
<li><strong>Bir dosya tutun.</strong> Tahlil sonuçlarınızı, raporlarınızı ve ilaç listenizi bir arada saklayın.</li>
<li><strong>İletişim bilgilerini kaydedin.</strong> Tedavi gördüğünüz servisin ve acil durumda arayacağınız numaranın telefonda kayıtlı olsun.</li>
<li><strong>Destek isteyin.</strong> Yakınlarınızdan alışveriş, ulaşım gibi pratik işlerde yardım istemekten çekinmeyin.</li>
<li><strong>Haklarınızı öğrenin.</strong> <a href="/size-destek/hasta-ve-hekim-haklari">Hasta ve hekim hakları</a> sayfamız, rapor, ilaç ve sosyal güvenlik konularında yol gösterir.</li>
<li><strong>Alternatif tedavi vaatlerine karşı dikkatli olun.</strong> Bitkisel ürünler tedavinizle etkileşebilir. <a href="/size-destek/alternatif-tedaviler">Ayrıntılar</a></li>
</ul>
<h2>Duygularınız da önemli</h2>
<p>Tanıyı kabullenmek zaman alır. Kaygı ya da çökkünlük günlük hayatınızı zorlaştırıyorsa hekiminizden psikolojik destek isteyebilirsiniz. Benzer yoldan geçmiş insanlarla tanışmak da çok iyi gelir: <a href="/etkinlikler">hasta buluşmalarımıza</a> katılabilir, <a href="/hikayeler">hikâyeleri</a> okuyabilirsiniz.</p>
HTML,
                    'en' => <<<'HTML'
<p class="lead">A blood cancer diagnosis can change life overnight, for you and for the people you love. Feeling fear, shock or anger in the first days is completely natural. This page brings together information that helps in the first weeks.</p>
<h2>Get to know your condition</h2>
<p>There are many types of leukemia, lymphoma and myeloma, and each behaves and is treated differently. Ask your doctor for the <strong>exact name and subtype</strong> of your condition, then read the relevant page under <a href="/en/conditions">conditions</a>.</p>
<h2>Questions you may want to ask</h2>
<ul>
<li>What exactly is my diagnosis? Do I need further tests?</li>
<li>Do I need to start treatment straight away? What is the goal of treatment?</li>
<li>How long will treatment last? Will I need to stay in hospital?</li>
<li>What side effects might I have, and when should I call you immediately?</li>
<li>Can I keep working, driving and travelling?</li>
<li>What should I do if I plan to have children?</li>
</ul>
<h2>Practical tips</h2>
<ul>
<li>Keep all test results, reports and your medicine list in one folder.</li>
<li>Save the phone number of your treatment unit and the number to call in an emergency.</li>
<li>Accept help with shopping, transport and other practical tasks.</li>
<li>Be careful with "alternative" treatments — herbal products can interact with your medicines.</li>
</ul>
<h2>Your feelings matter too</h2>
<p>If anxiety or low mood makes daily life hard, ask your care team about psychological support. Meeting people who have been through the same can help a lot.</p>
HTML,
                ],
            ],
            [
                'section' => 'rehber', 'sort' => 3,
                'title' => ['tr' => 'Tedavi görüyorum', 'en' => 'In treatment'],
                'slug' => ['tr' => 'tedavi-goruyorum', 'en' => 'in-treatment'],
                'summary' => ['tr' => 'Yan etkiler, beslenme ve enfeksiyondan korunma.', 'en' => 'Side effects, nutrition and avoiding infection.'],
                'body' => [
                    'tr' => <<<'HTML'
<p class="lead">Tedavi dönemi; kemoterapi, hedefe yönelik ilaçlar, immünoterapi ya da kök hücre nakli gibi farklı yöntemleri içerebilir. Bu sayfada tedavi sırasında günlük hayatınızı kolaylaştıracak genel bilgileri bulacaksınız.</p>
<h2>Hemen hekiminizi aramanız gereken durumlar</h2>
<div class="callout callout--warn"><p><strong>Ateşinizin 38 °C ve üzerine çıkması, titreme, durmayan kanama, nefes darlığı, bilinç değişikliği ya da şiddetli ishal/kusma</strong> durumlarında beklemeden tedavi gördüğünüz merkezi arayın veya en yakın acil servise başvurun. Tedavi sırasında bağışıklığınız zayıf olduğundan enfeksiyonlar hızla ilerleyebilir.</p></div>
<h2>Enfeksiyonlardan korunma</h2>
<p>Kemoterapi sonrası beyaz kan hücreleri azalır ve vücut mikroplara karşı savunmasız kalabilir. Ellerinizi sık yıkamak, kalabalık ve kapalı ortamlardan uzak durmak, hasta kişilerle temastan kaçınmak en önemli önlemlerdir. Ayrıntılı öneriler için <a href="/size-destek/enfeksiyonlardan-korunma">enfeksiyonlardan korunma rehberimizi</a> okuyun.</p>
<h2>Beslenme</h2>
<p>Tedavi döneminde iştahsızlık, bulantı ve tat değişiklikleri sık görülür. Az ve sık yemek, bol sıvı almak ve besin güvenliğine dikkat etmek önemlidir. Çiğ ya da az pişmiş et, yumurta ve pastörize edilmemiş süt ürünlerinden kaçınılması önerilir. <a href="/size-destek/beslenme">Beslenme rehberimiz</a></p>
<h2>Yaygın yan etkilerle baş etmek</h2>
<ul>
<li><strong>Yorgunluk:</strong> Gün içinde kısa dinlenmeler planlayın; hafif yürüyüşler çoğu zaman iyi gelir.</li>
<li><strong>Ağız yaraları:</strong> Yumuşak diş fırçası kullanın, ağız bakımınızı hekiminizin önerdiği şekilde yapın.</li>
<li><strong>Saç dökülmesi:</strong> Çoğunlukla geçicidir; tedavi bittikten sonra saçlar yeniden çıkar.</li>
<li><strong>Bulantı:</strong> Size verilen bulantı ilaçlarını önerildiği gibi kullanın, tetikleyen kokulardan uzak durun.</li>
</ul>
<p>Her ilacın yan etkileri farklıdır. Yeni bir şikâyet fark ettiğinizde kendi başınıza ilaç almadan önce tedavi ekibinize danışın.</p>
<h2>Sorularınız için</h2>
<p>Tedaviyle ilgili aklınıza takılanları <a href="/uzmana-sorun">uzmanlarımıza sorabilirsiniz</a>.</p>
HTML,
                    'en' => <<<'HTML'
<p class="lead">Treatment may include chemotherapy, targeted medicines, immunotherapy or a stem cell transplant. This page gives general tips to make daily life easier during treatment.</p>
<h2>When to call your doctor immediately</h2>
<p><strong>A temperature of 38 °C or higher, shivering, bleeding that won't stop, breathlessness, confusion, or severe diarrhoea/vomiting</strong> — call your treatment centre straight away or go to the nearest emergency department. Your immunity is weak during treatment, so infections can progress quickly.</p>
<h2>Preventing infection</h2>
<p>Wash your hands often, avoid crowded indoor places and stay away from people who are unwell.</p>
<h2>Nutrition</h2>
<p>Eat small, frequent meals, drink plenty of fluids and pay attention to food safety. Avoid raw or undercooked meat and eggs and unpasteurised dairy products.</p>
<h2>Common side effects</h2>
<ul>
<li><strong>Fatigue:</strong> plan short rests; gentle walks often help.</li>
<li><strong>Mouth sores:</strong> use a soft toothbrush and follow your team's mouth-care advice.</li>
<li><strong>Hair loss:</strong> usually temporary; hair grows back after treatment.</li>
<li><strong>Nausea:</strong> take your anti-sickness medicines as prescribed.</li>
</ul>
<p>Always talk to your care team before taking any new medicine.</p>
HTML,
                ],
            ],
            [
                'section' => 'rehber', 'sort' => 4,
                'title' => ['tr' => 'Tedavi sonrası', 'en' => 'After treatment'],
                'slug' => ['tr' => 'tedavi-sonrasi', 'en' => 'after-treatment'],
                'summary' => ['tr' => 'Takip süreci ve günlük hayata dönüş.', 'en' => 'Follow-up care and getting back to everyday life.'],
                'body' => [
                    'tr' => <<<'HTML'
<p class="lead">Tedavinin bitmesi büyük bir rahatlama olsa da birçok kişi için yeni sorular da getirir: "Hastalık geri gelir mi?", "Ne zaman işe dönebilirim?" Bu sayfa tedavi sonrası dönemde size eşlik etmek için hazırlandı.</p>
<h2>Kontrollerinizi aksatmayın</h2>
<p>Tedavi sonrasında düzenli kontroller, hastalığın izlenmesi ve geç yan etkilerin erken fark edilmesi için çok önemlidir. Kontrol takviminizi hekiminizle birlikte belirleyin ve randevularınızı not edin. Bazı kan kanserlerinde tedavi uzun süre ağızdan alınan ilaçlarla sürer; ilaçlarınızı hekiminize danışmadan bırakmayın.</p>
<h2>Günlük hayata dönüş</h2>
<ul>
<li><strong>Enerjiniz yavaş yavaş geri gelir.</strong> Yorgunluğun aylarca sürmesi olağandır; aktivitelerinizi kademeli artırın.</li>
<li><strong>Aşılarınızı sorun.</strong> Özellikle kök hücre nakli sonrasında aşıların yeniden yapılması gerekebilir.</li>
<li><strong>Sağlıklı yaşam</strong> — dengeli beslenme, düzenli hareket, sigarayı bırakmak — genel sağlığınızı destekler.</li>
<li><strong>İşe dönüş</strong> konusunda işvereninizle esnek çalışma seçeneklerini konuşabilirsiniz.</li>
</ul>
<h2>Duygusal iyilik hâli</h2>
<p>Tedavi sonrası dönemde hastalığın tekrarlama endişesi sık görülür. Bu duyguları paylaşmak, benzer deneyimleri yaşamış insanlarla tanışmak iyi gelir. <a href="/hikayeler">Hikâyeleri okuyun</a>, dilerseniz kendi hikâyenizi bizimle paylaşın.</p>
HTML,
                    'en' => <<<'HTML'
<p class="lead">Finishing treatment is a huge relief, but it often brings new questions: "Will it come back?", "When can I go back to work?" This page is here to support you after treatment.</p>
<h2>Keep up with your check-ups</h2>
<p>Regular follow-up is essential to monitor the disease and catch late side effects early. Some blood cancers are treated long-term with oral medicines — never stop them without talking to your doctor.</p>
<h2>Back to everyday life</h2>
<ul>
<li>Energy returns gradually; tiredness lasting months is common.</li>
<li>Ask about vaccinations — they may need to be repeated after a stem cell transplant.</li>
<li>Healthy habits — balanced diet, regular activity, not smoking — support your overall health.</li>
<li>Talk to your employer about flexible arrangements when returning to work.</li>
</ul>
<h2>Emotional wellbeing</h2>
<p>Fear of the disease coming back is common. Sharing these feelings and meeting others with similar experiences can help.</p>
HTML,
                ],
            ],
            [
                'section' => 'rehber', 'sort' => 5,
                'title' => ['tr' => 'Hasta yakınıyım', 'en' => 'Family and friends'],
                'slug' => ['tr' => 'hasta-yakiniyim', 'en' => 'family-and-friends'],
                'summary' => ['tr' => 'Sevdiğime nasıl destek olabilirim, kendime nasıl bakarım?', 'en' => 'How can I support someone, and look after myself?'],
                'body' => [
                    'tr' => <<<'HTML'
<p class="lead">Sevdiğiniz birinin kan kanseri tanısı alması sizin hayatınızı da değiştirir. Hasta yakınları çoğu zaman hem güçlü durmaya çalışır hem de kendi duygularını geri plana atar. Bu sayfa hem sevdiğinize hem de kendinize nasıl bakabileceğinizi anlatıyor.</p>
<h2>Sevdiğinize nasıl destek olabilirsiniz?</h2>
<ul>
<li><strong>Dinleyin.</strong> Çoğu zaman çözüm önermek yerine yanında olmak ve dinlemek yeterlidir.</li>
<li><strong>Muayenelere birlikte gidin.</strong> Soruları not almak ve hekimin söylediklerini kaydetmek çok yardımcı olur.</li>
<li><strong>Pratik işlerde yardım edin.</strong> Yemek, ulaşım, ilaç takibi, resmi işler… Somut yardım teklif edin.</li>
<li><strong>Enfeksiyon önlemlerine uyun.</strong> Hastalandığınızda ziyareti erteleyin, el hijyenine dikkat edin.</li>
<li><strong>Kararlara saygı gösterin.</strong> Tedavi sürecinde sevdiğinizin kendi kararlarını vermesine alan tanıyın.</li>
</ul>
<h2>Kendinize de iyi bakın</h2>
<p>Bakım vermek yorucudur. Uykunuza, beslenmenize ve kendi sağlık kontrollerinize özen gösterin. Yardım istemek bir zayıflık değildir; diğer aile üyeleri ve arkadaşlarınızla görevleri paylaşın. Kendinizi tükenmiş hissediyorsanız bir uzmandan psikolojik destek almayı düşünün.</p>
<h2>Çocuklarla konuşmak</h2>
<p>Çocuklar ailede bir şeylerin değiştiğini fark eder. Yaşlarına uygun, sade ve dürüst bir dille konuşmak, onların kaygısını azaltır. Sorularını cevaplamaktan çekinmeyin.</p>
<h2>Yalnız değilsiniz</h2>
<p><a href="/etkinlikler">Hasta ve hasta yakını buluşmalarımız</a> ve canlı yayınlarımız, sorularınızı uzmanlara sormak ve benzer deneyimleri paylaşmak için iyi bir fırsat. Aklınıza takılan her şeyi <a href="/uzmana-sorun">bize yazabilirsiniz</a>.</p>
HTML,
                    'en' => <<<'HTML'
<p class="lead">When someone you love is diagnosed with a blood cancer, your life changes too. Family members often try to stay strong while putting their own feelings aside. This page is about caring for them — and for yourself.</p>
<h2>How you can help</h2>
<ul>
<li><strong>Listen.</strong> Being there often matters more than offering solutions.</li>
<li><strong>Go to appointments together</strong> and take notes.</li>
<li><strong>Offer practical help</strong> — meals, transport, medicines, paperwork.</li>
<li><strong>Follow infection precautions</strong> and postpone visits when you are unwell.</li>
<li><strong>Respect their choices</strong> about treatment and daily life.</li>
</ul>
<h2>Look after yourself too</h2>
<p>Caring is tiring. Protect your sleep, meals and own health check-ups, share tasks with others, and consider professional support if you feel exhausted.</p>
HTML,
                ],
            ],

            // ---------------------------------------------------------------
            // Kurumsal
            // ---------------------------------------------------------------
            [
                'section' => 'kurumsal', 'sort' => 1,
                'image' => 'legacy/galeri-170-1142-dsc2330jpg.jpg',
                'title' => ['tr' => 'Hakkımızda', 'en' => 'About us'],
                'slug' => ['tr' => 'hakkimizda', 'en' => 'about-us'],
                'summary' => ['tr' => '2011\'den beri lösemi, lenfoma ve miyelomla yaşayan insanların ve ailelerinin yanındayız.', 'en' => 'Standing with people living with leukemia, lymphoma and myeloma, and their families, since 2011.'],
                'body' => [
                    'tr' => <<<'HTML'
<p class="lead">Lösemi Lenfoma Miyelom Derneği, 2011 yılında Ankara'da kuruldu. Amacımız lösemi, lenfoma ve miyelomla yaşayan insanların ve ailelerinin doğru bilgiye ulaşmasını, haklarını bilmesini ve bu yolda yalnız kalmamasını sağlamak.</p>
<h2>Neler yapıyoruz?</h2>
<ul>
<li><strong>Hasta ve hasta yakınlarına yönelik bilgilendirme:</strong> Hasta kongreleri, hasta buluşmaları ve "Lenfoma Bitecek", "KML Bitecek" gibi canlı yayınlarla uzman hekimlerle hastaları bir araya getiriyoruz.</li>
<li><strong>Farkındalık çalışmaları:</strong> Her yıl düzenlediğimiz "Lenfomaya Karşı Yürü Bizimle" yürüyüşü ve "Ben de Varım" gibi kampanyalarla kan kanserlerine dikkat çekiyoruz.</li>
<li><strong>Bilimsel toplantılar:</strong> Hematoloji alanındaki güncel gelişmelerin hekimlerle paylaşıldığı kongre, sempozyum ve çalıştaylar düzenliyoruz.</li>
<li><strong>Yayınlar:</strong> Hastalık rehberleri, broşürler ve derneğimizin süreli yayını LLMBİR Bülten'i ücretsiz olarak paylaşıyoruz.</li>
<li><strong>Gençlik kampları:</strong> Genç hastaların bir araya geldiği, bilgi ve deneyim paylaştığı kamplar düzenliyoruz.</li>
<li><strong>Kök hücre bağışı farkındalığı:</strong> Kök hücre nakli bekleyen hastalar için gönüllü verici sayısının artmasını destekliyoruz.</li>
</ul>
<h2>Bize katılın</h2>
<p>Derneğimize <a href="/uye-olun">üye olarak</a>, <a href="/bagis">bağış yaparak</a> ya da <a href="/gonullu-olun">gönüllü olarak</a> bu dayanışmanın parçası olabilirsiniz.</p>
HTML,
                    'en' => <<<'HTML'
<p class="lead">The Leukemia Lymphoma Myeloma Association (Lösemi Lenfoma Miyelom Derneği) was founded in Ankara, Türkiye, in 2011. Our goal is to make sure people living with leukemia, lymphoma and myeloma and their families have access to reliable information, know their rights and never face this journey alone.</p>
<h2>What we do</h2>
<ul>
<li><strong>Patient information:</strong> patient congresses, patient meetings and livestreams such as "Lymphoma Will End" and "CML Will End" bring specialists and patients together.</li>
<li><strong>Awareness:</strong> our annual "Walk With Us Against Lymphoma" and campaigns such as "I Am Here Too".</li>
<li><strong>Scientific meetings</strong> for haematologists and healthcare professionals.</li>
<li><strong>Publications:</strong> free guides, brochures and our periodical, LLMBİR Bulletin.</li>
<li><strong>Youth camps</strong> for young patients.</li>
<li><strong>Stem cell donation awareness.</strong></li>
</ul>
HTML,
                ],
            ],
            [
                'section' => 'katilim', 'sort' => 2,
                'title' => ['tr' => 'Gönüllü olun', 'en' => 'Volunteer'],
                'slug' => ['tr' => 'gonullu-olun', 'en' => 'volunteer'],
                'summary' => ['tr' => 'Etkinliklerimizde, kampanyalarımızda ve hasta buluşmalarımızda bize destek olun.', 'en' => 'Support our events, campaigns and patient meetings.'],
                'body' => [
                    'tr' => <<<'HTML'
<p class="lead">Gönüllülerimiz, derneğimizin en büyük gücü. Bir yürüyüşte, bir hasta buluşmasında ya da bir kampanyada birkaç saatinizi ayırmanız bile fark yaratır.</p>
<h2>Nasıl destek olabilirsiniz?</h2>
<ul>
<li>"Lenfomaya Karşı Yürü Bizimle" gibi farkındalık etkinliklerinde görev almak</li>
<li>Hasta kongreleri ve buluşmalarında karşılama ve organizasyona yardım etmek</li>
<li>Tasarım, fotoğraf, video, sosyal medya ya da çeviri gibi uzmanlıklarınızı paylaşmak</li>
<li>Çevrenizde kök hücre bağışı ve kan kanserleri hakkında farkındalık oluşturmak</li>
<li>Kendi deneyiminizi paylaşarak yeni tanı almış hastalara umut olmak</li>
</ul>
<h2>Başvuru</h2>
<p>Gönüllü olmak için <a href="/iletisim">iletişim formumuzdan</a> bize yazın; konu kısmına "Gönüllülük" yazmanız yeterli. Size uygun bir görev için sizinle iletişime geçeceğiz.</p>
HTML,
                    'en' => <<<'HTML'
<p class="lead">Our volunteers are our greatest strength. Even a few hours at a walk, a patient meeting or a campaign make a difference.</p>
<h2>How you can help</h2>
<ul>
<li>Help at awareness events such as our annual lymphoma walk</li>
<li>Support patient congresses and meetings</li>
<li>Share your skills — design, photography, video, social media or translation</li>
<li>Raise awareness of blood cancers and stem cell donation</li>
</ul>
<p>To volunteer, <a href="/en/contact">contact us</a> with the subject "Volunteering".</p>
HTML,
                ],
            ],

            // ---------------------------------------------------------------
            // Yasal metinler (TASLAK — hukukçu onayı gerekir)
            // ---------------------------------------------------------------
            [
                'section' => 'yasal', 'sort' => 1,
                'title' => ['tr' => 'KVKK aydınlatma metni', 'en' => 'Privacy notice'],
                'slug' => ['tr' => 'kvkk-aydinlatma-metni', 'en' => 'privacy-notice'],
                'summary' => ['tr' => '6698 sayılı Kişisel Verilerin Korunması Kanunu kapsamında web sitemiz üzerinden işlenen kişisel veriler hakkında bilgilendirme.', 'en' => 'How we process personal data collected through this website.'],
                'body' => [
                    'tr' => <<<'HTML'
<p><em>Bu metin taslaktır; yayına alınmadan önce derneğin hukuk danışmanınca gözden geçirilmelidir.</em></p>
<h2>Veri sorumlusu</h2>
<p>Lösemi Lenfoma Miyelom Derneği ("Dernek"), Hoşdere Caddesi No: 198/5 Çankaya / Ankara adresinde mukim olup 6698 sayılı Kişisel Verilerin Korunması Kanunu ("KVKK") kapsamında veri sorumlusudur.</p>
<h2>İşlenen kişisel veriler ve amaçları</h2>
<ul>
<li><strong>Uzmana sorun formu:</strong> ad soyad, e-posta, telefon, hastalık bilgisi ve sorunuz — sorunuzun gönüllü hekimlerimizce yanıtlanması amacıyla.</li>
<li><strong>İletişim formu:</strong> ad soyad, e-posta, telefon ve mesajınız — talebinizin yanıtlanması amacıyla.</li>
<li><strong>Üyelik başvurusu:</strong> kimlik, iletişim ve adres bilgileri — dernek üyelik işlemlerinin mevzuata uygun yürütülmesi amacıyla.</li>
<li><strong>Bağış:</strong> ad soyad, iletişim bilgileri, T.C. kimlik/vergi numarası ve işlem bilgileri — bağışın alınması ve makbuz düzenlenmesi amacıyla. Kart bilgileriniz Dernek tarafından görülmez ve saklanmaz; ödeme Garanti BBVA'nın güvenli ödeme sayfasında gerçekleşir.</li>
<li><strong>Bülten aboneliği:</strong> e-posta adresi — etkinlik ve yayınlarımızdan haberdar edilmeniz amacıyla.</li>
</ul>
<h2>Özel nitelikli kişisel veriler</h2>
<p>Uzmana sorun formunda paylaştığınız sağlık bilgileri özel nitelikli kişisel veridir ve yalnızca açık rızanız ile, sorunuzun yanıtlanması amacıyla işlenir.</p>
<h2>Aktarım</h2>
<p>Kişisel verileriniz; sorunuzu yanıtlayacak gönüllü hekimlerle, bağış işlemleri için ödeme kuruluşuyla ve yasal yükümlülükler kapsamında yetkili kamu kurumlarıyla paylaşılabilir. Verileriniz pazarlama amacıyla üçüncü kişilere aktarılmaz.</p>
<h2>Saklama süresi</h2>
<p>Veriler, işleme amacının gerektirdiği süre ve ilgili mevzuatta öngörülen süreler boyunca saklanır, süre sonunda silinir veya anonim hâle getirilir.</p>
<h2>Haklarınız</h2>
<p>KVKK'nın 11. maddesi kapsamında verilerinizin işlenip işlenmediğini öğrenme, düzeltilmesini veya silinmesini isteme ve diğer haklarınızı kullanmak için <a href="mailto:info@losemilenfomamiyelom.org">info@losemilenfomamiyelom.org</a> adresine yazabilirsiniz.</p>
HTML,
                    'en' => <<<'HTML'
<p>The Leukemia Lymphoma Myeloma Association (Ankara, Türkiye) is the data controller for personal data collected through this website under Turkish Law No. 6698 on the Protection of Personal Data (KVKK).</p>
<p>We process the data you submit through our forms (ask an expert, contact, membership, donation, newsletter) only to respond to your request, manage membership and donations, and keep you informed if you subscribed. Health information you share is processed only with your explicit consent. Card details are never seen or stored by us; payments take place on Garanti BBVA's secure payment page.</p>
<p>To exercise your rights, write to <a href="mailto:info@losemilenfomamiyelom.org">info@losemilenfomamiyelom.org</a>. The Turkish version of this notice prevails.</p>
HTML,
                ],
            ],
            [
                'section' => 'yasal', 'sort' => 2,
                'title' => ['tr' => 'Çerez politikası', 'en' => 'Cookie policy'],
                'slug' => ['tr' => 'cerez-politikasi', 'en' => 'cookie-policy'],
                'summary' => ['tr' => 'Web sitemizde kullanılan çerezler hakkında bilgilendirme.', 'en' => 'About the cookies used on this website.'],
                'body' => [
                    'tr' => <<<'HTML'
<p>Web sitemiz yalnızca sitenin çalışması için <strong>zorunlu çerezleri</strong> kullanır:</p>
<ul>
<li><strong>Oturum çerezi</strong> — form gönderimlerinin ve bağış işleminin güvenle tamamlanması için.</li>
<li><strong>Güvenlik (CSRF) çerezi</strong> — formların kötüye kullanımını önlemek için.</li>
</ul>
<p>Yazı boyutu ve yüksek kontrast tercihleriniz çerez değil, yalnızca tarayıcınızda (yerel depolama) saklanır. Sitemizde reklam ya da izleme amaçlı üçüncü taraf çerez kullanılmaz. Ziyaret istatistikleri için bir araç eklenirse bu sayfa güncellenecek ve onayınız istenecektir.</p>
HTML,
                    'en' => <<<'HTML'
<p>This website only uses <strong>strictly necessary cookies</strong> (a session cookie and a security/CSRF cookie) so that forms and donations work securely. Your text-size and contrast preferences are stored only in your browser. We do not use advertising or tracking cookies.</p>
HTML,
                ],
            ],
            [
                'section' => 'yasal', 'sort' => 3,
                'title' => ['tr' => 'Erişilebilirlik', 'en' => 'Accessibility'],
                'slug' => ['tr' => 'erisilebilirlik', 'en' => 'accessibility'],
                'summary' => ['tr' => 'Sitemizi herkesin kolayca kullanabilmesi için yaptıklarımız.', 'en' => 'How we make this website easy for everyone to use.'],
                'body' => [
                    'tr' => <<<'HTML'
<p>Ziyaretçilerimizin önemli bir kısmı tedavi görmekte olan, yorgunluk ya da görme güçlüğü yaşayabilen kişiler. Bu yüzden sitemizi WCAG 2.1 AA düzeyini hedefleyerek tasarladık:</p>
<ul>
<li>Sayfanın üstündeki <strong>A / A / A</strong> düğmeleriyle yazı boyutunu büyütebilir, <strong>◐</strong> düğmesiyle yüksek kontrast moduna geçebilirsiniz.</li>
<li>Tüm sayfalar klavyeyle gezilebilir; "İçeriğe geç" bağlantısı menüyü atlamanızı sağlar.</li>
<li>Metin ve arka plan renkleri okunabilirlik için yeterli kontrastta seçildi.</li>
<li>Site, telefon ve tabletlerde de rahat kullanılacak şekilde tasarlandı.</li>
</ul>
<p>Sitemizi kullanırken bir güçlükle karşılaşırsanız lütfen <a href="/iletisim">bize bildirin</a>.</p>
HTML,
                    'en' => <<<'HTML'
<p>Many of our visitors are in treatment and may experience fatigue or visual difficulties, so we designed this website to meet WCAG 2.1 AA. You can enlarge text with the <strong>A / A / A</strong> buttons, switch to high contrast with <strong>◐</strong>, and navigate everything by keyboard. If you have any difficulty using the site, please <a href="/en/contact">let us know</a>.</p>
HTML,
                ],
            ],
        ];
    }
}
