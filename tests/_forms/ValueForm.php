<?php
/**
 * This file is part of the elisdn/yii2-composite-form library
 *
 * @copyright Copyright (c) Dmitry Eliseev <mail@elisdn.ru>
 * @license https://github.com/ElisDN/yii2-composite-form/blob/master/LICENSE.md
 * @link https://github.com/ElisDN/yii2-composite-form
 */

namespace elisdn\compositeForm\tests\_forms;

use yii\base\Model;

class ValueForm extends Model
{
    public $value;

    public function rules()
    {
        return [
            ['value', 'required'],
        ];
    }
}