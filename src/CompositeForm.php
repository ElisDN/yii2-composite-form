<?php
/**
 * This file is part of the elisdn/yii2-composite-form library
 *
 * @copyright Copyright (c) Dmitry Eliseev <mail@elisdn.ru>
 * @license https://github.com/ElisDN/yii2-composite-form/blob/master/LICENSE.md
 * @link https://github.com/ElisDN/yii2-composite-form
 */

namespace elisdn\compositeForm;

use yii\base\Model;
use yii\helpers\ArrayHelper;

abstract class CompositeForm extends Model
{
    /**
     * @var Model[]|array[]
     */
    protected $_forms = [];

    /**
     * @return array of internal forms like ['meta', 'values']
     */
    abstract protected function internalForms();

    public function load($data, $formName = null)
    {
        $success = parent::load($data, $formName);
        foreach ($this->_forms as $name => $form) {
            if (is_array($form)) {
                $success = Model::loadMultiple($form, $data, $formName === null ? null : $name) || $success;
            } else {
                $success = $form->load($data, $formName !== '' ? null : $name) || $success;
            }
        }
        return $success;
    }

    public function validate($attributeNames = null, $clearErrors = true)
    {
        if ($attributeNames !== null) {
            $parentNames = array_filter($attributeNames, 'is_string');
            $success = $parentNames ? parent::validate($parentNames, $clearErrors) : true;
        } else {
            $success = parent::validate(null, $clearErrors);
        }
        foreach ($this->_forms as $name => $form) {
            if ($attributeNames === null || array_key_exists($name, $attributeNames) || in_array($name, $attributeNames, true)) {
                $innerNames = ArrayHelper::getValue($attributeNames, $name);
                if (is_array($form)) {
                    $success = Model::validateMultiple($form, $innerNames) && $success;
                } else {
                    $success = $form->validate($innerNames, $clearErrors) && $success;
                }
            }
        }
        return $success;
    }

    public function hasErrors($attribute = null)
    {
        if ($attribute !== null && mb_strpos($attribute, '.') === false) {
            return parent::hasErrors($attribute);
        }
        if (parent::hasErrors($attribute)) {
            return true;
        }
        foreach ($this->_forms as $name => $form) {
            if (is_array($form)) {
                foreach ($form as $i => $item) {
                    if ($attribute === null) {
                        if ($item->hasErrors()) {
                            return true;
                        }
                    } elseif (mb_strpos($attribute, $name . '.' . $i . '.') === 0) {
                        if ($item->hasErrors(mb_substr($attribute, mb_strlen($name . '.' . $i . '.')))) {
                            return true;
                        }
                    }
                }
            } else {
                if ($attribute === null) {
                    if ($form->hasErrors()) {
                        return true;
                    }
                } elseif (mb_strpos($attribute, $name . '.') === 0) {
                    if ($form->hasErrors(mb_substr($attribute, mb_strlen($name . '.')))) {
                        return true;
                    }
                }
            }
        }
        return false;
    }

    public function getErrors($attribute = null)
    {
        $result = parent::getErrors($attribute);
        foreach ($this->_forms as $name => $form) {
            if (is_array($form)) {
                /** @var Model[] $form */
                foreach ($form as $i => $item) {
                    foreach ($item->getErrors() as $attr => $errors) {
                        /** @var array $errors */
                        $errorAttr = $name . '.' . $i . '.' . $attr;
                        if ($attribute === null) {
                            foreach ($errors as $error) {
                                $result[$errorAttr][] = $error;
                            }
                        } elseif ($errorAttr === $attribute) {
                            foreach ($errors as $error) {
                                $result[] = $error;
                            }
                        }
                    }
                }
            } else {
                foreach ($form->getErrors() as $attr => $errors) {
                    /** @var array $errors */
                    $errorAttr = $name . '.' . $attr;
                    if ($attribute === null) {
                        foreach ($errors as $error) {
                            $result[$errorAttr][] = $error;
                        }
                    } elseif ($errorAttr === $attribute) {
                        foreach ($errors as $error) {
                            $result[] = $error;
                        }
                    }
                }
            }
        }
        return $result;
    }

    public function getFirstErrors()
    {
        $result = parent::getFirstErrors();
        foreach ($this->_forms as $name => $form) {
            if (is_array($form)) {
                foreach ($form as $i => $item) {
                    foreach ($item->getFirstErrors() as $attr => $error) {
                        $result[$name . '.' . $i . '.' . $attr] = $error;
                    }
                }
            } else {
                foreach ($form->getFirstErrors() as $attr => $error) {
                    $result[$name . '.' . $attr] = $error;
                }
            }
        }
        return $result;
    }

    public function __get($name)
    {
        if (isset($this->_forms[$name])) {
            return $this->_forms[$name];
        }
        return parent::__get($name);
    }

    public function __set($name, $value)
    {
        if (in_array($name, $this->internalForms(), true)) {
            $this->_forms[$name] = $value;
        } else {
            parent::__set($name, $value);
        }
    }

    public function __isset($name)
    {
        return isset($this->_forms[$name]) || parent::__isset($name);
    }
}