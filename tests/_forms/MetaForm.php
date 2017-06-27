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

class MetaForm extends Model
{
    public $title;
    public $description;

    public function rules()
    {
        return [
            [['title'], 'required'],
            [['title', 'description'], 'string'],
        ];
    }
}