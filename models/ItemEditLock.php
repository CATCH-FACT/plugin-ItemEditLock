<?php

/**
 * ItemEditLock
 * @package: Omeka
 */
class ItemEditLock extends Omeka_Record_AbstractRecord implements Zend_Acl_Resource_Interface
{
    public $item_id;
    public $added;
    public $owner_id;
    
    protected function _validate()
    {
/*        if (empty($this->item_id)) {
            $this->addError('item_id', 'ItemEditLock requires an item ID.');
        }
        // An item must exist.
        if (!$this->getTable('Item')->exists($this->item_id)) {
            $this->addError('item_id', __('Location requires a valid item ID.'));
        }*/
    }
    
    /**
     * Prepare special variables before saving the form.
     */
    protected function beforeSave($args)
    {
        if ($this->owner_id == ""){
            $this->owner_id = current_user()->id;
        }
        $this->added = date('Y-m-d H:i:s');
    }
    
    /**
     * Identify ItemEditLock records as relating to the ItemEditLock ACL resource.
     * 
     * @return string
     */
    public function getResourceId()
    {
        return 'ItemEditLock';
    }
    
    
}