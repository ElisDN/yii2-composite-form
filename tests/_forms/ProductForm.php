<?php
/**
 * This file is part of the elisdn/yii2-composite-form library
 *
 * @copyright Copyright (c) Dmitry Eliseev <mail@elisdn.ru>
 * @license https://github.com/ElisDN/yii2-composite-form/blob/master/LICENSE.md
 * @link https://github.com/ElisDN/yii2-composite-form
 */

namespace elisdn\compositeForm\tests\_forms;

use elisdn\compositeForm\CompositeForm;

/**
 * @property MetaForm $meta
 * @property ValueForm[] $values
 */
class ProductForm extends CompositeForm
{
    public $code;
    public $name;

    /**
     * @param integer $valuesCount
     * @param array $config
     */
    public function __construct($valuesCount, $config = [])
    {
        $this->meta = new MetaForm();
        $this->values = $valuesCount ? array_map(function () {
            return new ValueForm();
        }, range(1, $valuesCount)) : [];
        parent::__construct($config);
    }

    public function rules()
    {
        return [
            [['code'], 'required'],
            [['code', 'name'], 'string'],
        ];
    }

    protected function internalForms()
    {
        return ['meta', 'values'];
    }
}