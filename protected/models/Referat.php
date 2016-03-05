<?php

/**
 * The followings are the available columns in table 'gremien':.
 *
 * @property int $id
 * @property string $name
 * @property string $urlpart
 * @property string $strasse
 * @property string $ort
 * @property string $plz
 * @property string $email
 * @property string $telefon
 * @property string $website
 * @property string $kurzbeschreibung
 * @property int $aktiv
 *
 * The followings are the available model relations:
 * @property Antrag[] $antraege
 * @property StadtraetInReferat[] $stadtraetInnenReferate
 */
class Referat extends CActiveRecord implements IRISItem
{
    /**
     * Returns the static model of the specified AR class.
     *
     * @param string $className active record class name.
     *
     * @return Referat the static model class
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
        return 'referate';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return [
            ['id, name, urlpart', 'required'],
            ['id, aktiv', 'numerical', 'integerOnly' => true],
            ['name, email, telefon', 'length', 'max' => 100],
            ['kurzbeschreibung, website', 'length', 'max' => 200],
            ['strasse, urlpart', 'length', 'max' => 45],
            ['plz', 'length', 'max' => 10],
            ['ort', 'length', 'max' => 30],
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
            'antraege'               => [self::HAS_MANY, 'Antrag', 'referat_id'],
            'stadtraetInnenReferate' => [self::HAS_MANY, 'StadtraetInReferat', 'referat_id'],
        ];
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return [
            'id'               => 'ID',
            'name'             => 'Name',
            'urlpart'          => 'URL',
            'plz'              => 'PLZ',
            'ort'              => 'Ort',
            'strasse'          => 'Straße',
            'email'            => 'E-Mail',
            'telefon'          => 'Telefonnummer',
            'website'          => 'Website',
            'kurzbeschreibung' => 'Kurzbeschreibung',
            'aktiv'            => 'Aktiv',
        ];
    }

    /**
     * @param array $add_params
     *
     * @return string
     */
    public function getLink($add_params = [])
    {
        return Yii::app()->createUrl("themen/referat", array_merge(["id" => $this->id], $add_params));
    }

    /** @return string */
    public function getTypName()
    {
        return "Referat";
    }

    /**
     * @param bool $kurzfassung
     *
     * @return string
     */
    public function getName($kurzfassung = false)
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getDate()
    {
        return "0000-00-00 00:00:00";
    }

    /**
     * @param string $name
     *
     * @return null|Referat
     */
    public static function getByHtmlName($name)
    {
        $name = trim(strip_tags($name));
        $ref  = self::model()->findByAttributes(["name" => $name]);

        return $ref;
    }

    /**
     * @param int    $referat_id
     * @param string $zeit_von
     * @param string $zeit_bis
     * @param int    $limit
     *
     * @return $this
     */
    public function neueste_dokumente($referat_id, $zeit_von = "", $zeit_bis = "", $limit = 0)
    {
        $time_condition = '';
        if ($zeit_von != "") $time_condition .= 'c.datum >= "'.addslashes($zeit_von).'"';
        if ($zeit_von != "" && $zeit_bis != "") $time_condition .= ' AND ';
        if ($zeit_bis != "") $time_condition .= 'c.datum <= "'.addslashes($zeit_bis).'"';

        $params = [
            'alias'     => 'a',
            'condition' => 'a.id = '.intval($referat_id),
            'order'     => 'c.datum DESC',
            'with'      => [
                'antraege'           => [
                    'alias' => 'b',
                ],
                'antraege.dokumente' => [
                    'alias'     => 'c',
                    'condition' => $time_condition,
                ],
            ], ];
        if ($limit > 0) $params['limit'] = $limit;
        $this->getDbCriteria()->mergeWith($params);

        return $this;
    }
}
