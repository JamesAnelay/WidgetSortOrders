<?php class Anelay_WidgetSortOrders_Model_Core_Layout_Update extends Mage_Core_Model_Layout_Update
{
    public function load($handles=array())
    {
        if (is_string($handles)) {
            $handles = array($handles);
        } elseif (!is_array($handles)) {
            throw Mage::exception('Mage_Core', Mage::helper('core')->__('Invalid layout update handle'));
        }

        foreach ($handles as $handle) {
            $this->addHandle($handle);
        }

        if ($this->loadCache()) {
            return $this;
        }

//        foreach ($this->getHandles() as $handle) {
//            $this->merge($handle);
//        }
        $this->mergeWithAllHandles();

        $this->saveCache();
        return $this;
    }

    public function mergeWithAllActiveHandles()
    {
        $handles = $this->getHandles();

        foreach($handles as $handle){
            $packageUpdatesStatus = $this->fetchPackageLayoutUpdates($handle);
        }

        $this->fetchDbLayoutUpdatesForAllActiveHandles($handles);
    }

    public function fetchDbLayoutUpdatesForAllActiveHandles(array $handles)
    {
        $_profilerKey = 'layout/db_update_all_active_handles';
        Varien_Profiler::start($_profilerKey);
        $updateStr = $this->_getUpdateStringForHandles($handles);
        if (!$updateStr) {
            return false;
        }
        $updateStr = '<update_xml>' . $updateStr . '</update_xml>';
        $updateStr = str_replace($this->_subst['from'], $this->_subst['to'], $updateStr);
        $updateXml = simplexml_load_string($updateStr, $this->getElementClass());
        $this->fetchRecursiveUpdates($updateXml);
        $this->addUpdate($updateXml->innerXml());

        Varien_Profiler::stop($_profilerKey);
        return true;
    }

    protected function _getUpdateStringForHandles($handles)
    {
        return Mage::getResourceModel('anelay_widgetsortorders/core_layout')->fetchUpdatesByMultipleHandles($handles);
    }



}