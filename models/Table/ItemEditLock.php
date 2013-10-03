<?php
/**
 * EditItemLockTable
 *
 * @copyright 
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * The EditItemLockTable page table class.
 *
 * @package EditItemLockTable
 */
class Table_ItemEditLock extends Omeka_Db_Table
{

    public function isLocked($item){
        $recordId = $item->id;
        if ($recordId){
            return $this->findByRecordId($recordId) ? True : False;
        }
        else{
            return False;
        }
    }
    
    public function isLockedByMe($item){
        $recordId = $item->id;
        if ($recordId){
            $recordId = $item->id;
            return $this->findByRecordIdAndCurrentUser($recordId) ? True : False;
        }
        else{
            return False;
        }

    }

    /**
     * Find all metametadata, ordered by $order_by.
     *
     * @return array The pages ordered alphabetically by their slugs
     */
    public function findAllLockedItems()
    {
        $select = $this->getSelect()->order('added');
        return $this->fetchObjects($select);
    }

    /**
     * Find all metametadata, ordered by $order_by.
     *
     * @return array The pages ordered alphabetically by their slugs
     */
    public function findAllLockedItemsByUser($owner_id)
    {
        $select = $this->getSelect()->where('owner_id = ?', $owner_id);
        return $this->fetchObjects($select);
    }

    public function findByRecordIdAndCurrentUser($recordId)
    {
        $select = $this->getSelect()->where('item_id = ?', $recordId)->where('owner_id = ?', current_user()->id);
        return $this->fetchObject($select);
    }
    
    public function findByRecordId($recordId)
    {
        $select = $this->getSelect();
        $select->where("item_id = ?");
        $select->limit(1);
        return $this->fetchObject($select, array($recordId));
    }

}
