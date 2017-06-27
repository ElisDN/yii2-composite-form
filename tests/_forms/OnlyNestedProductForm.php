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
 */
class OnlyNestedProductForm extends CompositeForm
{
    public function __construct($config = [])
    {
        $this->meta = new MetaForm();
        parent::__construct($config);
    }

    protected function internalForms()
    {
        return ['meta'];
    }
}