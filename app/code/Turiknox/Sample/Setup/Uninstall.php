<?php
namespace Turiknox\Sample\Setup;
/*
 * Turiknox_Sample

 * @category   Turiknox
 * @package    Turiknox_Sample
 * @copyright  Copyright (c) 2017 Turiknox
 * @license    https://github.com/Turiknox/magento2-sample-uicomponent/blob/master/LICENSE.md
 * @version    1.0.0
 */
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UninstallInterface;
use Magento\Config\Model\ResourceModel\Config\Data;
use Magento\Config\Model\ResourceModel\Config\Data\CollectionFactory;

class Uninstall implements UninstallInterface
{
    /**
     * @var CollectionFactory
     */
    protected $_collectionFactory;
    /**
     * @var Data
     */
    protected $_configResource;

    /**
     * @param CollectionFactory $collectionFactory
     * @param Data $configResource
     */
    public function __construct(
        CollectionFactory $collectionFactory,
        Data $configResource
    )
    {
        $this->_collectionFactory = $collectionFactory;
        $this->_configResource    = $configResource;
    }

    /**
     * Uninstall
     *
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function uninstall(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        if ($setup->tableExists('turiknox_sample_data')) {
            $setup->getConnection()->dropTable('turiknox_sample_data');
        }
        $collection = $this->_collectionFactory->create()
            ->addPathFilter('turiknox_sample');
        foreach ($collection as $config) {
            $this->deleteConfig($config);
        }
    }

    /**
     * @param AbstractModel $config
     * @throws \Exception
     */
    protected function deleteConfig(AbstractModel $config)
    {
        $this->_configResource->delete($config);
    }
}
