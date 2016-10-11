<?php
/**
 * @var IndexController $this
 * @var \Solarium\QueryType\Select\Result\Result $ergebnisse
 * @var RISSucheKrits $krits
 * @var array $available_facets
 * @var bool $email_bestaetigt
 * @var bool $email_angegeben
 * @var bool $eingeloggt
 * @var bool $wird_benachrichtigt
 * @var BenutzerIn $ich
 * @var null|array $geodata
 * @var null|array $geodata_overflow
 */

$this->pageTitle = "Suchergebnisse";

?>
<section class="well suchoptionen">
    <h1> <?= ($krits->getKritsCount() > 1) ? "Suche" : CHtml::encode($krits->getBeschreibungDerSuche()) ?> </h1>

    <? // Button mit Extras, die rechts sind ?>
    <div class="pull-right">
        <?
        $this->renderPartial("suchergebnisse_benachrichtigungen", array(
            "eingeloggt" => $eingeloggt,
            "email_angegeben" => $email_angegeben,
            "email_bestaetigt" => $email_bestaetigt,
            "wird_benachrichtigt" => $wird_benachrichtigt,
            "ich" => $ich,
            "krits" => $krits
        ));
        ?>
        <a href="<?= CHtml::encode($krits->getFeedUrl()) ?>">
            <button type="button" name="<?= AntiXSS::createToken("benachrichtigung_add") ?>"
                    class="btn btn-info benachrichtigung_std_button">
                <? /* class=glyphicon, damit die css-Styles die gleichen wie bei dem Benachrichtigungs-Button sind */ ?>
                <span class="glyphicon fontello-rss"></span> Suchergebnisse als RSS-Feed
            </button>
        </a>
    </div>

    <?
    // Anzeigen der Suchkriterien und die Möglichkeit, diese zu entfernen
    if ($krits->getKritsCount() > 1) { ?>
    <div class="suchkrits_interaktiv">
        <h4 class="suchkriterien">Gefunden wurden Dokumente mit den folgenden Kriterien:</h4>
        <ul>
            <? foreach ($krits->krits as $krit) {
                $single_krit = new RISSucheKrits([$krit]);
                $one_removed = new RISSucheKrits();
                foreach ($krits->krits as $krit2) {
                    if ($krit2 != $krit)
                        $one_removed->krits[] = $krit2;
                } ?>
                <li>
                    <a href="<?= $one_removed->getUrl() ?>"
                       title='Kriterium "<?= $single_krit->getBeschreibungDerSuche() ?>" enfernen'>
                        <span class="fontello fontello-cancel"></span><?= $single_krit->getBeschreibungDerSuche() ?>
                    </a>
                </li>
            <? } ?>
        </ul>
    </div>
    <? } ?>

    <?
    // Möglichkeiten, die Suche weiter einzuschränken
    $has_facets = false;
    foreach ($available_facets as $name => $facets) if (count($facets) > 1) $has_facets = true;

    if ($has_facets) {
        ?>
        <section class="suchergebnis_eingrenzen">
            <h4 class="suchkriterien">Suche einschränken</h4>
            <ul>
                <? foreach ($available_facets as $name => $facets) if (count($facets) > 1) { ?>
                    <li class="dropdown">
                        <button class="asdf btn btn-default dropdown-toggle" type="button" data-toggle="dropdown">
                            <?= CHtml::encode($name) ?> <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu">
                            <? foreach ($facets as $facet) { ?>
                                <li>
                                    <a href="<?= $facet['url'] ?>"><?= $facet['name'] . ' (' . $facet['count'] . ')' ?></a>
                                </li>
                            <? } ?>
                        </ul>
                    </li>
                <? } ?>
            </ul>
        </section>
    <? } ?>

    <?
    // Eine Karte (?!)
    if (!is_null($geodata) && count($geodata) > 0) {
        $geokrit = $krits->getGeoKrit();
        ?>
        <div id="mapholder">
            <div id="map"></div>
        </div>
        <div id="overflow_hinweis" <? if (count($geodata_overflow) == 0) echo "style='display: none;'"; ?>>
            <label><input type="checkbox" name="zeige_overflow">
                Zeige <span class="anzahl">
                    <?= (count($geodata_overflow) == 1 ? "1 Dokument" : count($geodata_overflow) . " Dokumente") ?>
                </span> mit über 20 Ortsbezügen
            </label>
        </div>

        <script>
            $(function () {
                var $map = $("#map").AntraegeKarte({
                    lat: <?=$geokrit["lat"]?>,
                    lng: <?=$geokrit["lng"]?>,
                    size: 14,
                    antraege_data: <?=json_encode($geodata)?>,
                    antraege_data_overflow: <?=json_encode($geodata_overflow)?>,
                });
            });
        </script>
    <? } ?>

</section>

<section class="well suchergebnisse">
    <h1>Suchergebnisse</h1>
    <?
    if ($krits->getKritsCount() > 0) $this->renderPartial("../benachrichtigungen/suchergebnisse_liste", array(
        "ergebnisse" => $ergebnisse,
    ));
    ?>
</section>
