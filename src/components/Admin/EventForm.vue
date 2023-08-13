<template>
  <q-form>
    <div class="row q-col-gutter-md q-pb-sm">
      <TranslatableInput class="col-12" v-model="name" />

      <TranslatableInput class="col-12" v-model="note" type="wysiwyg" />
    </div>
  </q-form>
</template>

<script lang="ts">
import { defineComponent } from 'vue';
import TranslatableInput from 'components/Form/TranslatableInput.vue';

export default defineComponent({
  name: 'EventForm',
  components: { TranslatableInput },
  data() {
    return {
      name: {
        cs: 'Událost 1',
        en: 'Event 1',
      },
      beginning: '2019-08-21',
      end: '2019-08-23',
      place: 'Praha',
      soft_deadline: '2019-08-14 14:00:00',
      hard_deadline: '2019-08-16 20:00:00',
      note: {
        cs: '<p><em>Zveme vás na první debatní akci v novém školním roce!</em></p><hr><p>Nauč se získávat poznatky o aktuálních tématech, přesvědčivě formulovat svůj názor a prezentovat jej, argumentovat věcně a eticky, třídit a tříbit své myšlenky, mluvit před publikem bez trémy, hodnotit kvalitu informací, kriticky myslet, pracovat se zdroji, daty a statistikami, poznej nové přátele. Začni debatovat!</p><p>Asociace debatních klubů, z.s. a OPEN GATE – gymnázium a základní škola, s.r.o. vás zvou na společnou úvodní akci, která proběhne <strong>22.–24. září 2023</strong>. Vítáni jsou začínající debatéři, rozhodčí a učitelé, kteří se chtějí o debatování dozvědět více a zkusit koučování debatního klubu.</p><blockquote><p><strong><em>Jedete poprvé na debatní akci? <a href="https://debatovani.cz/liga/zacnete-debatovat/jak-se-pripravit-na-prvni-turnaj/">Podívejte se, jak se na ni připravit a nic nezapomenout.</a></em></strong></p></blockquote><h2>Školení debatérů</h2><p>Školení je jedinečnou příležitostí k osvojení si nových debatních schopností a dovedností pod vedením metodiků Ředitelství soutěží. Začínající debatéry čekají celkem čtyři školící bloky a dvě debaty, v nichž budou moci otestovat své nově nabyté dovednosti.</p><p>Školení je určeno pouze úplným začátečníkům. Debatérům, kteří se zúčastnili alespoň jednoho turnaje ve formátu Karl Popper nebude umožněna účast. Zkušenější debatéři se mohou zúčastnit školení rozhodčích (pokud splňují věkovou podmínku).</p><h2>Školení rozhodčích</h2><p>Školení je otevřeno všem zájemcům, kteří by chtěli rozšířit řady akreditovaných rozhodčích. Jedinou podmínkou je dosažení 18 let věku v této debatní sezóně, tj. do 29. dubna 2024. Předchozí zkušenosti s formou KPDP ani akademickým debatováním nejsou nutné. </p><h2>Školení učitelů</h2><p>Školení <em>Rozvoj klíčových kompetencí metodou debaty</em> akreditované MŠMT v systému dalšího vzdělávání pedagogických pracovníků je zaměřené především na učitele a všechny ostatní, kteří by se rádi dozvěděli, jak vypadá debatování, jak vést debatní klub nebo s čím může debatování pomoct ve výuce. Budete moct zhlédnout reálnou debatu a také Vás seznámíme s fungováním ADK i programy, které nabízíme. Všichni účastníci obdrží certifikát o absolvování.</p><p><a href="https://debatovani.cz/debata/skoleni-ucitelu/">Bližší informace o DVPP školeních, které ADK nabízí</a></p><h2>Organizační informace</h2><h3>Orientační harmonogram</h3><p><strong>pátek:</strong><br>16:00 příjezd a ubytování<br>18:00 večeře<br>18:45 zahájení (v jídelně)<br>19:00 první blok školení / ukázková debata<br><strong>sobota:</strong><br>8:00 snídaně<br>9:00 další bloky školení<br>12:00 oběd<br>13:00 debaty<br>18:00 večeře<br><strong>neděle:</strong><br>7:30 snídaně<br>8:30 debaty<br>11:30 oběd a vyhlášení výsledků</p><h3>Místo konání</h3><p>Celá akce proběhne v areálu <a href="http://www.opengate.cz/" target="_blank" rel="noopener noreferrer">Open Gate</a>, <a href="https://www.google.cz/maps/place/Open+Gate/@50.0046499,14.7171522,15z/data=!4m2!3m1!1s0x0:0x8fc5b485d0b85f56?sa=X&amp;ved=2ahUKEwj_zfqgj4PdAhWrDpoKHUY0D8UQ_BIwDXoECAcQCw" target="_blank" rel="noopener noreferrer">Na Návsi 5, 251 01 Babice</a>.</p><h3>Ubytování a stravování</h3><p>Ubytování je zajištěno na kolejích na Open Gate. Stravování je zajištěno formou plné penze od páteční večeře po nedělní oběd.</p><h3>Doprava</h3><p>Nejlepší doprava na Open Gate je autobusem linky 364 z pražského Depa Hostivař (konečná stanice metra A).</p><h3>Platby</h3><p>Poplatek pro účastníky školení sestává ze dvou částí:</p><ul><li>50 Kč – členský příspěvek ADK</li><li>950 Kč – členský příspěvek účastníka školení (pokrývá náklady na školení, ubytování a stravu)</li></ul><h3>Přihlášky</h3><p>Přihlášky odesílejte <strong>do pátku 15. září do 14:00</strong>. Po odeslání přihlášky vám systém vygeneruje fakturu s informacemi pro platbu.</p><blockquote><p><strong><em>Jak se zaregistrovat na debatní turnaj? <a href="/liga/zacnete-debatovat/jak-se-zaregistrovat-na-turnaj/" target="_blank" rel="noreferrer noopener">Tento návod Vám pomůže</a>.</em></strong></p></blockquote><h3>Kontakt</h3><p>Dotazy prosím zasílejte na adresu <a href="mailto:info@debatovani.cz">info@debatovani.cz</a>. V urgentních případech je k dispozici mobilní telefon: +420 725 992 360 (Václav Soukup).</p>',
        en: 'Any comment',
      },
      invoice_cs: 'Fakturujeme vám...',
      invoice_en: 'Invoice for...',
    };
  },
});
</script>
