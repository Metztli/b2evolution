<?php
/**
 * This file implements the ChecklistLine class.
 *
 * This file is part of the b2evolution/evocms project - {@link http://b2evolution.net/}.
 * See also {@link https://github.com/b2evolution/b2evolution}.
 *
 * @license GNU GPL v2 - {@link http://b2evolution.net/about/gnu-gpl-license}
 *
 * @copyright (c)2003-2020 by Francois Planque - {@link http://fplanque.com/}.
*
 * @license http://b2evolution.net/about/license.html GNU General Public License (GPL)
 *
 * @package evocore
 */
if (! defined('EVO_MAIN_INIT')) {
    die('Please, do not access this page directly.');
}

load_class('_core/model/dataobjects/_dataobject.class.php', 'DataObject');


/**
 * ItemTag Class
 *
 * @package evocore
 */
class ChecklistLine extends DataObject
{
    /**
     * The item (parent) of this Comment (lazy-filled).
     * @see ChecklistLine::get_Item()
     * @see ChecklistLine::set_Item()
     * @access protected
     * @var Item
     */
    public $Item;

    /**
     * The ID of the comment's Item.
     * @var integer
     */
    public $item_ID;

    public $checked;

    public $label;

    public $order;

    /**
     * Constructor
     *
     * @param object table Database row
     */
    public function __construct($db_row = null)
    {
        // Call parent constructor:
        parent::__construct('T_items__checklist_lines', 'check_', 'check_ID');

        if ($db_row != null) {
            $this->ID = $db_row->check_ID;
            $this->item_ID = $db_row->check_item_ID;
            $this->checked = $db_row->check_checked;
            $this->label = $db_row->check_label;
            $this->order = $db_row->check_order;
        }
    }

    /**
     * Get the Item this comment relates to
     *
     * @return Item
     */
    public function &get_Item()
    {
        if (! isset($this->Item)) {
            $ItemCache = &get_ItemCache();
            $this->Item = &$ItemCache->get_by_ID($this->item_ID, false, false);
        }

        return $this->Item;
    }

    /**
     * Set Item this comment relates to
     * @param Item
     */
    public function set_Item(&$Item)
    {
        $this->Item = &$Item;
        parent::set_param('item_ID', 'number', $Item->ID);
    }

    /**
     * Insert object into DB based on previously recorded changes.
     *
     * Note: DataObject does not require a matching *Cache object.
     * Therefore it will not try to update the Cache.
     * If something like that was needed, sth like *Cache->add() should be called.
     * ATTENTION: Any dbinsert should typically be followed by a 303 redirect. Updating the Cache before redirect is generally not needed.
     *
     * @return boolean true on success
     */
    public function dbinsert()
    {
        global $DB;

        if (empty($this->order)) {
            $SQL = new SQL('Get max');
            $SQL->SELECT('MAX(check_order)');
            $SQL->FROM('T_items__checklist_lines');
            $SQL->WHERE('check_item_ID =' . $DB->quote($this->item_ID));
            $max_order = intval($DB->get_var($SQL)) + 1;
            $this->set('order', $max_order);
        }

        $r = parent::dbinsert();

        return $r;
    }
}
