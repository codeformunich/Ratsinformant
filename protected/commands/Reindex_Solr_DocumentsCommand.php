<?php

class Reindex_Solr_DocumentsCommand extends CConsoleCommand
{
    public function run($args)
    {

        if (!isset($args[0])) die("./yiic reindexsolr_documents [id]|stadtrat_beschluss|ba_beschluss|alle [offset]\n");

        if ($args[0] > 0) {
            $data = [$args[0]];
        } elseif ($args[0] == "alle") {
            $sql = Yii::app()->db->createCommand();
            $sql->select("id")->from("dokumente")->where("id >= 0")->order("id");
            $data = $sql->queryColumn(["id"]);
        } elseif (isset(Dokument::$TYPEN_ALLE[$args[0]])) {
            $sql = Yii::app()->db->createCommand();
            $sql->select("id")->from("dokumente")->where("typ = '".addslashes($args[0])."'")->order("id");
            $data = $sql->queryColumn(["id"]);
        } else {
            die("./yiic reindexsolr_documents [id]|stadtrat_beschluss|ba_beschluss|rathausumschau|alle\n");
        }

        $offset = (isset($args[1]) && $args[1] > 0 ? intval($args[1]) : 0);

        $anz = count($data);
        for ($i = $offset; $i < $anz; $i++) {
            $dok_id = $data[$i];
            echo "$i / $anz => $dok_id\n";
            /** @var Dokument $dokument */
            $dokument = Dokument::model()->findByPk($dok_id);
            $dokument->solrIndex();
        }
    }
}
