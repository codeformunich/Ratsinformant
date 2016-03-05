<?php

class ExportController extends RISBaseController
{
    public function actionFraktionantraege($fraktion_id, $limit = 30, $offset = 0) {
        header("Content-Type: application/json; charset=UTF-8");

        /** @var Fraktion $fraktion */
        $fraktion = Fraktion::model()->findByPk($fraktion_id);
        $strIds   = [];
        foreach ($fraktion->stadtraetInnenFraktionen as $strFrakt) {
            $strIds[] = $strFrakt->stadtraetIn_id;
        }
        $strIds = implode(", ", array_map("IntVal", $strIds));

        $SQL       = "SELECT a.id FROM antraege a JOIN antraege_stadtraetInnen b ON a.id = b.antrag_id ".
                     "WHERE b.stadtraetIn_id IN ($strIds) GROUP BY a.id ORDER BY a.gestellt_am DESC";
        if ($limit > 0 || $offset > 0) {
            $SQL .= " LIMIT ".intval($offset).",".intval($limit);
        }
        $antragIds = Yii::app()->db->createCommand($SQL)->queryAll();

        $return = [];
        foreach ($antragIds as $antragId) {
            /* @var StadtraetInFraktion[] $strs */
            $antrag = Antrag::model()->findByPk($antragId);

            $antragData = [
                'id'                 => intval($antrag->id),
                'typ'                => $antrag->antrag_typ,
                'betreff'            => RISTools::korrigiereTitelZeichen($antrag->betreff),
                'gestellt_von'       => $antrag->gestellt_von,
                'gestellt_am'        => $antrag->gestellt_am,
                'erledigt_am'        => $antrag->erledigt_am,
                'bearbeitungsfrist'  => $antrag->bearbeitungsfrist,
                'registriert_am'     => $antrag->registriert_am,
                'fristverlaengerung' => $antrag->fristverlaengerung,
                'referat'            => $antrag->referat_id,
                'referent'           => $antrag->referent,
                'antrags_nr'         => $antrag->antrags_nr,
                'status'             => $antrag->status,
                'stadtraetInnen'     => [],
                'initiatorInnen'     => [],
                'dokumente'          => [],
            ];
            foreach ($antrag->antraegePersonen as $person) {
                if ($person->person->stadtraetIn) {
                    $arr = [
                        'id'   => $person->person->stadtraetIn->id,
                        'name' => $person->person->stadtraetIn->name,
                    ];
                } else {
                    $arr = [
                        'id'   => null,
                        'name' => $person->person->name,
                    ];
                }
                if ($person->typ == AntragPerson::$TYP_GESTELLT_VON) {
                    $antragData['stadtraetInnen'][] = $arr;
                }
                if ($person->typ == AntragPerson::$TYP_INITIATORIN) {
                    $antragData['initiatorInnen'][] = $arr;
                }
            }
            foreach ($antrag->dokumente as $dokument) {
                $antragData['dokumente'][] = [
                    'id'    => intval($dokument->id),
                    'pdf'   => $dokument->getOriginalLink(),
                    'datum' => $dokument->datum_dokument,
                    'titel' => $dokument->name_title,
                ];
            }

            $return[] = $antragData;
        }
        echo json_encode($return);

        Yii::app()->end();
    }
}
