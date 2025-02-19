<?php

/*
 * This file is part of the Access to Memory (AtoM) software.
 *
 * Access to Memory (AtoM) is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Access to Memory (AtoM) is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Access to Memory (AtoM).  If not, see <http://www.gnu.org/licenses/>.
 */

class sfIsdfPluginRelatedFunctionComponent extends RelationEditComponent
{
    // Arrays not allowed in class constants
    public static $NAMES = [
        'resource',
        'type',
        'description',
        'startDate',
        'endDate',
        'date',
    ];

    public function execute($request)
    {
        parent::execute($request);

        $this->form->getWidgetSchema()->setNameFormat('relatedFunction[%s]');
    }

    protected function addField($name)
    {
        switch ($name) {
            case 'type':
                $this->form->setValidator('type', new sfValidatorString());

                $choices = [];
                $choices[null] = null;
                foreach (QubitTaxonomy::getTermsById(QubitTaxonomy::ISDF_RELATION_TYPE_ID) as $item) {
                    $choices[$this->context->routing->generate(null, [$item, 'module' => 'term'])] = $item;
                }

                $this->form->setWidget('type', new sfWidgetFormSelect(['choices' => $choices]));

                break;

            default:
                return parent::addField($name);
        }
    }

    protected function processField($field)
    {
        switch ($field->getName()) {
            case 'resource':
                // Update the object of the relation, unless the current resource is
                // the object
                if ($this->resource->id != $this->relation->objectId) {
                    unset($this->relation->object);
                } else {
                    unset($this->relation->subject);
                }

                $value = $this->form->getValue('resource');
                if (isset($value)) {
                    $params = $this->context->routing->parse(Qubit::pathInfo($value));
                    if ($this->resource->id != $this->relation->objectId) {
                        $this->relation->object = $params['_sf_route']->resource;
                    } else {
                        $this->relation->subject = $params['_sf_route']->resource;
                    }
                }

                break;

            default:
                return parent::processField($field);
        }
    }
}
