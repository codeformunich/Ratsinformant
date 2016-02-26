<?php

/**
 * This is the model class for table "tagesordnungspunkte_history".
 *
 * The followings are the available columns in table 'tagesordnungspunkte_history':
 *
 * @property int $id
 * @property int $vorgang_id
 * @property string $datum_letzte_aenderung
 * @property int $antrag_id
 * @property string $gremium_name
 * @property int $gremium_id
 * @property int $sitzungstermin_id
 * @property string $sitzungstermin_datum
 * @property string $beschluss_text
 * @property string $entscheidung
 * @property string $top_nr
 * @property int $top_ueberschrift
 * @property string $top_betreff
 * @property string $status
 *
 * The followings are the available model relations:
 * @property Termin $sitzungstermin
 * @property Gremium $gremium
 * @property Antrag $antrag
 * @property Dokument[] $dokumente
 */
class TagesordnungspunktHistory extends CActiveRecord
{
    /**
     * Returns the static model of the specified AR class.
     *
     * @param string $className active record class name.
     *
     * @return TagesordnungspunktHistory the static model class
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
        return 'tagesordnungspunkte_history';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return [
            ['top_betreff, sitzungstermin_id, sitzungstermin_datum, datum_letzte_aenderung', 'required'],
            ['antrag_id, gremium_id, sitzungstermin_id, top_ueberschrift, vorgang_id', 'numerical', 'integerOnly' => true],
            ['gremium_name', 'length', 'max' => 100],
            ['beschluss_text', 'length', 'max' => 500],
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
            'sitzungstermin' => [self::BELONGS_TO, 'Termin', 'sitzungstermin_id'],
            'gremium'        => [self::BELONGS_TO, 'Gremium', 'gremium_id'],
            'antrag'         => [self::BELONGS_TO, 'Antrag', 'antrag_id'],
            'dokumente'      => [self::HAS_MANY, 'Dokument', 'tagesordnungspunkt_id'],
            'vorgang'        => [self::BELONGS_TO, 'Vorgang', 'vorgang_id'],
        ];
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return [
            'id'                     => 'ID',
            'vorgang_id'             => 'Vorgangs-ID',
            'antrag_id'              => 'Antrag',
            'gremium_name'           => 'Gremium Name',
            'gremium_id'             => 'Gremium',
            'sitzungstermin_id'      => 'Sitzungstermin',
            'sitzungstermin_datum'   => 'Sitzungstermin Datum',
            'beschluss_text'         => 'Beschluss',
            'entscheidung'           => 'Entscheidung',
            'datum_letzte_aenderung' => 'Letzte Änderung',
            'top_nr'                 => 'Tagesordnungspunkt',
            'top_ueberschrift'       => 'Ist Überschrift',
            'top_betreff'            => 'Betreff',
            'status'                 => 'Status',
        ];
    }

    /**
     * @param bool $kurzfassung
     *
     * @return string
     */
    public function getName($kurzfassung = false)
    {
        if ($kurzfassung) {
            $betreff = str_replace(["\n", "\r"], [" ", " "], $this->top_betreff);
            $x       = explode(" Antrag Nr.", $betreff);

            return RISTools::korrigiereTitelZeichen($x[0]);
        } else {
            return RISTools::korrigiereTitelZeichen($this->top_betreff);
        }
    }
}
