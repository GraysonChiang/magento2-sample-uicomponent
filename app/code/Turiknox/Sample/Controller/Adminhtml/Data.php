<?php
namespace Turiknox\Sample\Controller\Adminhtml;
/*
 * Turiknox_Sample

 * @category   Turiknox
 * @package    Turiknox_Sample
 * @copyright  Copyright (c) 2017 Turiknox
 * @license    https://github.com/Turiknox/magento2-sample-uicomponent/blob/master/LICENSE.md
 * @version    1.0.0
 */
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Magento\Backend\Model\View\Result\ForwardFactory;
use Turiknox\Sample\Api\DataRepositoryInterface;

abstract class Data extends Action
{
    /**
     * @var string
     */
    const ACTION_RESOURCE = 'Turiknox_Sample::data';
    /**
     * Data repostory
     *
     * @var DataRepositoryInterface
     */
    protected $_dataRepository;

    /**
     * Core registry
     *
     * @var Registry
     */
    protected $_coreRegistry;

    /**
     * Result Page Factory
     *
     * @var PageFactory
     */
    protected $_resultPageFactory;

    /**
     * Result Forward Factory
     *
     * @var ForwardFactory
     */
    protected $_resultForwardFactory;

    /**
     * Data constructor.
     *
     * @param Registry $registry
     * @param DataRepositoryInterface $dataRepository
     * @param PageFactory $resultPageFactory
     * @param ForwardFactory $resultForwardFactory
     * @param Context $context
     */
    public function __construct(
        Registry $registry,
        DataRepositoryInterface $dataRepository,
        PageFactory $resultPageFactory,
        ForwardFactory $resultForwardFactory,
        Context $context

    ) {
        $this->_coreRegistry         = $registry;
        $this->_dataRepository       = $dataRepository;
        $this->_resultPageFactory    = $resultPageFactory;
        $this->_resultForwardFactory = $resultForwardFactory;
        parent::__construct($context);
    }
}