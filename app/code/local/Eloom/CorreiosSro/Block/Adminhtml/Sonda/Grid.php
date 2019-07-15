<?php

##eloom.licenca##

class Eloom_CorreiosSro_Block_Adminhtml_Sonda_Grid extends Mage_Adminhtml_Block_Widget_Grid {

  public function __construct() {
    parent::__construct();
    $this->setId('eloom_correiossro_sonda_grid');
    $this->setDefaultSort('created_at');
    $this->setDefaultDir('DESC');
    $this->setSaveParametersInSession(true);
    $this->setUseAjax(true);
  }

  /**
   * Retrieve collection class
   *
   * @return string
   */
  protected function _getCollectionClass() {
    return 'eloom_correiossro/sonda_collection';
  }

  protected function _prepareCollection() {
    $collection = Mage::getResourceModel($this->_getCollectionClass());
    $collection->join(array('so' => 'sales/order'), 'main_table.order_id=so.entity_id', array('increment_id' => 'increment_id'), null, 'left');
    $this->setCollection($collection);

    return parent::_prepareCollection();
  }

  public function getRowUrl($row) {
    //return $this->getUrl('*/*/edit', array('id' => $row->getId()));
  }

  protected function _prepareColumns() {
    $this->addColumn('number', array(
        'header' => $this->_getHelper()->__('Localizador'),
        'index' => 'number',
        'filter_index' => 'main_table.number',
    ));

    $this->addColumn('order_id', array(
        'header' => $this->_getHelper()->__('Order ID'),
        'type' => 'number',
        'index' => 'order_id',
        'filter_index' => 'so.increment_id',
        'renderer' => 'eloom_correiossro/adminhtml_sonda_widget_grid_column_renderer_order',
    ));

    if (!Mage::app()->isSingleStoreMode()) {
      $this->addColumn('store_id', array(
          'header' => Mage::helper('sales')->__('Purchased From (Store)'),
          'index' => 'store_id',
          'filter_index' => 'main_table.store_id',
          'type' => 'store',
          'store_view' => true,
          'display_deleted' => true,
      ));
    }

    $this->addColumn('created_at', array(
        'header' => Mage::helper('sales')->__('Cadastrado'),
        'index' => 'created_at',
        'filter_index' => 'main_table.created_at',
        'type' => 'datetime',
    ));

    $this->addColumn('message', array(
        'header' => Mage::helper('eloom_correiossro')->__('Mensagem'),
        'renderer' => 'eloom_correiossro/adminhtml_sonda_widget_grid_column_renderer_actions',
        'index' => 'message',
        'width' => '370px',
        'filter' => false,
        'sortable' => false,
    ));

    $this->addColumn('action', array(
        'header' => $this->_getHelper()->__('Action'),
        'width' => '150px',
        'type' => 'action',
        'actions' => array(
            array(
                'caption' => $this->_getHelper()->__('Edit'),
                'url' => array('base' => 'eloom_correiossro' . '/sonda/edit'),
                'field' => 'id'
            ),
        ),
        'filter' => false,
        'sortable' => false,
        'index' => 'entity_id',
    ));

    return parent::_prepareColumns();
  }

  protected function _getHelper() {
    return Mage::helper('eloom_correiossro');
  }

  public function getGridUrl() {
    return $this->getUrl('*/*/grid', array('_current' => true));
  }

}
