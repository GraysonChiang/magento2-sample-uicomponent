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
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;

class InstallData implements InstallDataInterface {

    /**
     * Install data
     *
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        if (version_compare($context->getVersion(), '1.0.0', '<')) {
            $data = [
                [
                    'data_title' => 'Hello World!',
                    'data_description' => 'This is the first description.',
                    'is_active' => 1
                ],
                [
                    'data_title' => 'Hello Again!',
                    'data_description' => 'This is the second description.',
                    'is_active' => 1
                ],
                [
                    'data_title' => 'Welcome To The Third Title',
                    'data_description' => 'Here we have a slightly longer description.',
                    'is_active' => 0
                ],
                [
                    'data_title' => 'Fourth Coming',
                    'data_description' => 'This is the fourth description.',
                    'is_active' => 1
                ],
                [
                    'data_title' => 'TQBFJOTLD',
                    'data_description' => 'The quick brown fox jumped over the lazy dog.',
                    'is_active' => 0
                ]
            ];

            foreach ($data as $datum) {
                $setup->getConnection()
                    ->insertForce($setup->getTable('turiknox_sample_data'), $datum);
            }
        }
    }
}