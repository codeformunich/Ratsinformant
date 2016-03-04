<?php

use yii\helpers\Html;
use app\components\RISTools;

/**
 * @var AntraegeController $this
 * @var Antrag[] $related
 * @var bool $narrow
 */

$max_title_width = ($narrow ? 65 : 130);

if ($related && count($related) > 0) foreach ($related as $verw) {
    ?>
    <li class='list-group-item'>
        <div class="row-action-primary">
            <i class="glyphicon glyphicon-file"></i>
        </div>
        <div class="row-content">
            <h4 class="list-group-item-heading">
                <?
                $title = trim($verw->getName(true));
                if (mb_strlen($title) > $max_title_width) {
                    $title_short = mb_substr($title, 0, $max_title_width * 1.5);
                    ?>
                    <a href="<?= Html::encode($verw->getLink()) ?>" title="<?= Html::encode($title) ?>" class="overflow-fadeout-white"><span class="hyphenate">
                        <?= Html::encode($title_short) ?>
                    </span></a>
                    <?
                } else {
                    ?>
                    <a href="<?= Html::encode($verw->getLink()) ?>" class="overflow-fadeout-white"><?= Html::encode($title) ?></a>
                    <?
                }
                ?>
            </h4>
            <?
            $max_date = 0;
            foreach ($verw->dokumente as $dokument) {
                $dat = RISTools::date_iso2timestamp($dokument->getDate());
                if ($dat > $max_date) $max_date = $dat;
            }
            ?>
            <p class="list-group-item-text">
                <?
                echo (count($verw->dokumente) == 1 ? "1 Dokument" : count($verw->dokumente) . " Dokumente");
                ?>
            </p>
            <div class="metainformationen_antraege"><?
                $parteien = array();
                foreach ($verw->antraegePersonen as $person) {
                    $name   = $person->person->name;
                    $partei = $person->person->ratePartei($verw->gestellt_am);
                    if (!$partei) {
                        $parteien[$name] = array($name);
                    } else {
                        if (!isset($parteien[$partei])) $parteien[$partei] = array();
                        $parteien[$partei][] = $person->person->name;
                    }
                }

                $p_strs = array();
                foreach ($parteien as $partei => $personen) {
                    $personen_net = array();
                    foreach ($personen as $p) if ($p != $partei) $personen_net[] = $p;
                    $str_p = "<span class='partei' title='" . Html::encode(implode(", ", $personen_net)) . "'>";
                    $str_p .= Html::encode($partei);
                    $str_p .= "</span>";
                    $p_strs[] = $str_p;
                }
                if (count($p_strs) > 0) echo implode(", ", $p_strs) . ", ";

                if ($verw->ba_nr > 0) echo "<span title='" . Html::encode("Bezirksausschuss " . $verw->ba_nr . " (" . $verw->ba->name . ")") . "' class='ba'>BA " . $verw->ba_nr . "</span>, ";

                echo date((date("Y", $max_date) == date("Y") ? "d.m." : "d.m.Y"), $max_date);

                ?></div>
        </div>
    </li>
<?
}
else {
    echo "Keine gefunden";
}