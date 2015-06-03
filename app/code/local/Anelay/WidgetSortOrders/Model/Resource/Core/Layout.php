<?php class Anelay_WidgetSortOrders_Model_Resource_Core_Layout extends Mage_Core_Model_Resource_Layout
{
    public function fetchUpdatesByHandles($handles, $params = array())
    {
        $bind = array(
            'store_id'  => Mage::app()->getStore()->getId(),
            'area'      => Mage::getSingleton('core/design_package')->getArea(),
            'package'   => Mage::getSingleton('core/design_package')->getPackageName(),
            'theme'     => Mage::getSingleton('core/design_package')->getTheme('layout')
        );

        foreach ($params as $key => $value) {
            if (isset($bind[$key])) {
                $bind[$key] = $value;
            }
        }

        $result = '';

        $readAdapter = $this->_getReadAdapter();
        if ($readAdapter) {
            $select = $readAdapter->select()
                ->from(array('layout_update' => $this->getMainTable()), array('xml'))
                ->join(array('link'=>$this->getTable('core/layout_link')),
                    'link.layout_update_id=layout_update.layout_update_id',
                    '')
                ->where('link.store_id IN (0, :store_id)')
                ->where('link.area = :area')
                ->where('link.package = :package')
                ->where('link.theme = :theme')
                ->where('layout_update.handle IN(?)',$handles)
                ->order('layout_update.sort_order ' . Varien_Db_Select::SQL_ASC);

            $result = join('', $readAdapter->fetchCol($select, $bind));
        }
        return $result;
    }
}