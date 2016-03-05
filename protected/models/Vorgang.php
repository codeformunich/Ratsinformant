<?php

/**
 * This is the model class for table "bezirksausschuesse".
 *
 * The followings are the available columns in table 'bezirksausschuesse':
 *
 * @property int $id
 * @property int $typ
 * @property string $betreff
 *
 * The followings are the available model relations:
 * @property Antrag[] $antraege
 * @property Tagesordnungspunkt[] $ergebnisse
 * @property Dokument[] $dokumente
 */
class Vorgang extends CActiveRecord implements IRISItemHasDocuments
{
    /**
     * @param string $className active record class name.
     *
     * @return Vorgang the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'vorgaenge';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return [
            ['id, typ', 'required'],
            ['id, typ', 'numerical', 'integerOnly' => true],
            ['betreff', 'length', 'max' => 200],
        ];
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return [
            'antraege'   => [self::HAS_MANY, 'Antrag', 'vorgang_id'],
            'ergebnisse' => [self::HAS_MANY, 'Tagesordnungspunkt', 'vorgang_id'],
            'dokumente'  => [self::HAS_MANY, 'Dokument', 'vorgang_id'],
        ];
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return [
            'id'      => 'ID',
            'typ'     => 'Typ',
            'betreff' => 'Betreff',
        ];
    }

    /**
     * @retun IRISItem[]
     */
    public function getRISItemsByDate()
    {
        /** @var IRISItem[] $items */
        $items                                       = [];
        foreach ($this->antraege as $ant) $items[]   = $ant;
        foreach ($this->dokumente as $dok) $items[]  = $dok;
        foreach ($this->ergebnisse as $erg) $items[] = $erg;

        usort($items, function ($it1, $it2) {
            /* @var IRISItem $it1 */
            /* @var IRISItem $it2 */
            $tim1 = RISTools::date_iso2timestamp($it1->getDate());
            $tim2 = RISTools::date_iso2timestamp($it2->getDate());
            if ($tim1 > $tim2) return 1;
            if ($tim1 < $tim2) return -1;

            return 0;
        });

        return $items;
    }

    /**
     * @return Dokument[]
     */
    public function getDokumente()
    {
        return $this->dokumente;
    }

    /**
     * @return IRISItem|null
     */
    public function wichtigstesRisItem()
    {
        foreach ($this->antraege as $ant) if ($ant->typ == Antrag::$TYP_STADTRAT_VORLAGE) return $ant;
        foreach ($this->antraege as $ant) return $ant;

        $items = $this->getRISItemsByDate();
        if (count($items) > 0) return $items[0];
        else return;
    }

    /**
     * @param BenutzerIn|null $benutzerIn
     *
     * @return bool
     */
    public function istAbonniert($benutzerIn)
    {
        if (!$benutzerIn) return false;
        foreach ($benutzerIn->abonnierte_vorgaenge as $vorg) if ($vorg->id == $this->id) return true;

        return false;
    }

    /**
     * @param BenutzerIn $benutzerIn
     *
     * @throws CDbException
     */
    public function abonnieren($benutzerIn)
    {
        try {
            Yii::app()->db
                ->createCommand("INSERT INTO `benutzerInnen_vorgaenge_abos` (`benutzerInnen_id`, `vorgaenge_id`) VALUES (:BenutzerInId, :VorgangId)")
                ->bindValues([':VorgangId' => $this->id, ':BenutzerInId' => $benutzerIn->id])
                ->execute();
        } catch (CDbException $e) {
            if ($e->errorInfo[1] != 1062) throw $e; // Duplicate Entry, ist ok
        }
    }

    /**
     * @param BenutzerIn $benutzerIn
     */
    public function deabonnieren($benutzerIn)
    {
        Yii::app()->db
            ->createCommand("DELETE FROM `benutzerInnen_vorgaenge_abos` WHERE `benutzerInnen_id` = :BenutzerInId AND `vorgaenge_id` = :VorgangId")
            ->bindValues([':VorgangId' => $this->id, ':BenutzerInId' => $benutzerIn->id])
            ->execute();
    }

    /**
     * @param int $vorgang_von_id
     * @param int $vorgang_zu_id
     */
    public static function vorgangMerge($vorgang_von_id, $vorgang_zu_id)
    {
        $vorgang_von_id = intval($vorgang_von_id);
        $vorgang_zu_id  = intval($vorgang_zu_id);

        $str = "Vorgang Merge: von $vorgang_von_id => $vorgang_zu_id\n";
        try {
            /** @var Vorgang $vorgang_von */
            $vorgang_von = self::model()->findByPk($vorgang_von_id);
            if (!$vorgang_von) throw new Exception("Vorgangs-ID nicht gefunden: ".$vorgang_von_id);

            foreach ($vorgang_von->antraege as $ant) {
                $ant->vorgang_id = $vorgang_zu_id;
                $ant->save(false);
                $str .= "Antrag: ".$ant->getName()."\n";
            }
            foreach ($vorgang_von->dokumente as $dok) {
                $dok->vorgang_id = $vorgang_zu_id;
                $dok->save(false);
                $str .= "Dokument: ".$dok->name."\n";
            }
            foreach ($vorgang_von->ergebnisse as $erg) {
                $erg->vorgang_id = $vorgang_zu_id;
                $erg->save(false);
                $str .= "Ergebnis: ".$erg->getName()."\n";
            }

            Yii::app()->db
                ->createCommand("UPDATE IGNORE `benutzerInnen_vorgaenge_abos` SET vorgaenge_id = :VorgangIdNeu WHERE vorgaenge_id = :VorgangIdAlt")
                ->bindValues([':VorgangIdNeu' => $vorgang_zu_id, ':VorgangIdAlt' => $vorgang_von_id])
                ->execute();

            Yii::app()->db
                ->createCommand("DELETE FROM `benutzerInnen_vorgaenge_abos` WHERE vorgaenge_id = :VorgangIdAlt")
                ->bindValues([':VorgangIdAlt' => $vorgang_von_id])
                ->execute();

            $str .= "Gelöscht.\n";
        } catch (Exception $e) {
            $str .= $e;
        }
        RISTools::send_email(Yii::app()->params['adminEmail'], "Vorgang:vorgangMerge Error", $str, null, "system");
    }

    /**
     * @param array $add_params
     *
     * @return string
     */
    public function getLink($add_params = [])
    {
        // TODO: Implement getLink() method.
    }

    /** @return string */
    public function getTypName()
    {
        return "Vorgang";
    }

    /** @return string */
    public function getDate()
    {
        // TODO: Implement getDate() method.
    }

    /**
     * @param bool $kurzfassung
     *
     * @return string
     */
    public function getName($kurzfassung = false)
    {
        return $this->betreff;
    }
}
