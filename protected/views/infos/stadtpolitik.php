<?
/**
 * @var InfosController $this
 * @var Text $text
 * @var string $my_url
 */

$this->pageTitle = "So funktioniert Stadtpolitik";
$this->load_mediaelement = true;

$html_text = preg_replace_callback("/CREATE_URL\((?<url>[^\)]+)\)/siu", function($matches) {
    return CHtml::encode(Yii::app()->createUrl($matches["url"]));
}, $text->text);
$html_text = RISTools::insertTooltips($html_text);

?>

<section class="well">

    <ul class="breadcrumb" style="margin-bottom: 5px;">
        <li><a href="<?= CHtml::encode(Yii::app()->createUrl("index/startseite")) ?>">Startseite</a><br></li>
        <li class="active">Stadtpolitik</li>
    </ul>


    <div class="row teaser_buttons">
        <div class="col col-md-6">
            <a href="<?= CHtml::encode(Yii::app()->createUrl("infos/glossar")) ?>"
               class="btn btn-success">
                <h2><span class="glyphicon glyphicon-info-sign"></span>Glossar</h2>

                <div class="description">
                    Wichtige Begriffe erklärt
                </div>
            </a>
        </div>

        <div class="col col-md-6">
            <a href="<?= CHtml::encode(Yii::app()->createUrl("infos/stadtrecht")) ?>"
               class="btn btn-success">
                <h2><span class="glyphicon paragraph">§</span> Stadtrecht</h2>

                <div class="description">
                    Satzungen &amp; Verordnungen
                </div>
            </a>
        </div>
    </div>

</section>


<div class="row" id="listen_holder">
    <div class="col col-md-8">
        <section class="start_berichte well std_fliesstext">
            <?
            if ($this->binContentAdmin()) { ?>
                <a href="#" style="display: inline; float: right;" id="text_edit_caller">
                    <span class="mdi-content-create"></span> Bearbeiten
                </a>
                <a href="#" style="display: none; float: right;" id="text_edit_aborter">
                    <span class="mdi-content-clear"></span> Abbrechen
                </a>
            <? }
            ?>
            <h1>So entsteht Stadtpolitik</h1>

            <br>
            <video width="560" height="306" poster="/media/v1.jpg" controls="controls" preload="none">
                <source type="video/mp4" src="/media/v1.aac.mp4">
                <source type="video/ogg" src="/media/v1.ogv">
                <object width="560" height="306" type="application/x-shockwave-flash" data="/js/mediaelement/build/flashmediaelement.swf">
                    <param name="movie" value="/js/mediaelement/build/flashmediaelement.swf" />
                    <param name="flashvars" value="controls=true&file=/media/v1.mp4" />
                    <!-- Image as a last resort -->
                    <img src="/media/v1.jpg" width="560" height="305" title="No video playback capabilities" />
                </object>
            </video>
            <div style="color: gray; text-align: center;">
                Video: Lionel Koch; Skript u. Ton: Bernd Oswald
            </div>
            <br><br>
            <?

            if ($this->binContentAdmin()) { ?>
                <script src="/js/ckeditor/ckeditor.js"></script>
                <div id="text_content_holder" style="border: dotted 1px transparent;">
                    <?=$html_text?>
                </div>
                <form method="POST" action="<?=CHtml::encode($my_url)?>" id="text_edit_form" style="display: none; border: dotted 1px gray;">
                    <div id="text_orig_holder"><?=$text->text?></div>
                    <input type="hidden" name="text" value="<?=CHtml::encode($text->text)?>">
                    <div style="text-align: center;">
                        <button type="submit" name="<?=CHtml::encode(AntiXSS::createToken("save"))?>" class="btn btn-primary">Speichern</button>
                    </div>
                </form>

                <script>
                    $("#text_edit_caller").click(function(ev) {
                        ev.preventDefault();
                        $("#text_edit_caller").hide();
                        $("#text_edit_aborter").show();
                        $("#text_content_holder").hide();
                        $("#text_edit_form").show();
                        ckeditor_init($("#text_orig_holder"), "inline");
                    });
                    $("#text_edit_aborter").click(function(ev) {
                        ev.preventDefault();
                        $("#text_edit_caller").show();
                        $("#text_edit_aborter").hide();
                        $("#text_content_holder").show();
                        $("#text_edit_form").hide();
                    });
                    $("#text_edit_form").submit(function() {
                        $(this).find("input[name=text]").val(CKEDITOR.instances["text_orig_holder"].getData());
                    });
                </script>

            <? } else {
                echo $html_text;
            }
            ?>
            <script>
                $(function() {
                    $('[data-toggle="tooltip"]').tooltip({animation: true, delay:500});
                    $('video,audio').mediaelementplayer({

                    });
                });
            </script>
        </section>
    </div>


    <div class="col col-md-4">
        <section class="well">
            <h3>Informative Seiten</h3>

            <ul>
                <li><a href="http://www.opengov-muenchen.de/">Das OpenGovernment-Portal der Stadt München</a></li>
                <li><a href="http://codefor.de/muenchen/">OK Lab München</a></li>
                <li>...weitere Links über München und Open Data? <a href="<?=CHtml::encode(Yii::app()->createUrl("infos/feedback"))?>">Schreib uns!</a></li>
            </ul>
        </section>
    </div>
</div>
