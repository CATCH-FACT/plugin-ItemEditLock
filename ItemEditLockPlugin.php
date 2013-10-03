<?php

/**
 * @copyright University of Twente and Meertens Institute Amsterdam
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package ItemEditLockPlugin
 */

class ItemEditLockPlugin extends Omeka_Plugin_AbstractPlugin
{
    protected $_hooks = array(  "initialize",
                                "install",
                                "uninstall");

    protected $_filters = array('admin_items_form_tabs');


    #whenever a user is doing something else than editing, any items should be unlocked
    public function hookInitialize(){
        if (current_user()){
            $lockedItems = $this->_db->getTable('ItemEditLock')->findAllLockedItemsByUser(current_user()->id);
            foreach($lockedItems as $lockedItem){
                $lockedItem->delete();
            }
        }
    }

    public function hookUninstall()
    {
        // Delete the plugin options
        delete_option('itemeditlock_lock_items');
        // Drop the table
        $db = get_db();
        $db->query("DROP TABLE $db->ItemEditLock");        
    }
    
    public function hookInstall()
    {
        $db = get_db();
        $db->query("CREATE TABLE IF NOT EXISTS $db->ItemEditLock (
          `id` int(10) unsigned NOT NULL auto_increment,
          `item_id` BIGINT UNSIGNED NOT NULL ,
          `added` timestamp NOT NULL default '0000-00-00 00:00:00',
          `owner_id` int unsigned default NULL,
          PRIMARY KEY  (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");

        set_option('itemeditlock_lock_items', '1');
    }
    
    public function hookAfterSaveItem($args){
        #unlock the item (to be sure)
        $record = $args['record'];
        if ($this->_db->getTable('ItemEditLock')->isLocked($record)){
            $this->_db->getTable('ItemEditLock')->findByRecordIdAndCurrentUser($record)->delete();
        }
    }

    public function filterAdminItemsFormTabs($tabs, $args){
        $item = $args['item'];
        if ($this->_db->getTable('ItemEditLock')->isLocked($item)){
            #Well, this is ugly. More elegant solution needed here.
            return array("" => "<div style='height:130px; width:80%; border=3px; background-color: red; z-index:2000; position: absolute;'><center><H2><br><br>Item is being edited by someone else. Please try again later</H2></center>  </div>");
        }
        else{
            if ($item->id){
                $locked = new ItemEditLock;
                $locked->item_id = $item->id;
                $locked->save();
            }
        }
        return $tabs;
    }

}