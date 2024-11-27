<?php
namespace Application\Form;

use Zend\Form\Fieldset;

use Zend\Form\FormInterface;


/**
 * This is a filedset that is used only for grouping of elements.
 * It does not assume a logical relationship between elements all elements are treated 
 * independently.
 * Therefore, names/ids of fields are not changed to include fieldset name.
 * 
 */
class GroupingFieldset extends Fieldset
{

    /**
     * We override this method so we can avoid appending the name of the fieldsets to every elements 
     *
     * @param  FormInterface $form
     * @return mixed|void
     */
    public function prepareElement(FormInterface $form)
    {
        $name = $this->getName();

        foreach ($this->byName as $elementOrFieldset) {
            #$elementOrFieldset->setName($name . '[' . $elementOrFieldset->getName() . ']');

            // Recursively prepare elements
            if ($elementOrFieldset instanceof ElementPrepareAwareInterface) {
                $elementOrFieldset->prepareElement($form);
            }
        }
    }

}
