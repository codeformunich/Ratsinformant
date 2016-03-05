<?php

/**
 * @property int $ba_nr
 * @property int $jahr
 * @property int $budget
 * @property int $vorjahr_rest
 * @property int $cache_aktuell
 *
 * The followings are the available model relations:
 * @property Bezirksausschuss $bezirksausschuss
 */
class BezirksausschussBudget extends CActiveRecord
{
    /**
     * Returns the static model of the specified AR class.
     *
     * @param string $className active record class name.
     *
     * @return StadtraetInGremium the static model class
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
        return 'bezirksausschuss_budget';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return [
            ['ba_nr, jahr, budget, vorjahr_rest', 'required'],
            ['ba_nr, jahr, budget, vorjahr_rest, cache_aktuell', 'numerical', 'integerOnly' => true],
            ['budget, vorjahr_rest, cache_aktuell', 'safe'],
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
            'bezirksausschuss' => [self::BELONGS_TO, 'Bezirksausschuss', 'ba_nr'],
        ];
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return [
            'ba_nr'         => 'Bezirksausschuss Nr.',
            'jahr'          => 'Jahr',
            'budget'        => 'Jahresbudget',
            'vorjahr_rest'  => 'Übertrag d. Vorjahr',
            'cache_aktuell' => 'Aktuelles Restguthaben',
        ];
    }
}
