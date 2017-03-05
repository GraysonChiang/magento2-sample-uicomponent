<?php
namespace Turiknox\Sample\Controller\Adminhtml\Data;
/*
 * Turiknox_Sample

 * @category   Turiknox
 * @package    Turiknox_Sample
 * @copyright  Copyright (c) 2017 Turiknox
 * @license    https://github.com/Turiknox/magento2-sample-uicomponent/blob/master/LICENSE.md
 * @version    1.0.0
 */
use Magento\Backend\App\Action\Context;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Magento\Backend\Model\View\Result\ForwardFactory;
use Magento\Ui\Component\MassAction\Filter;
use Turiknox\Sample\Api\DataRepositoryInterface;
use Turiknox\Sample\Controller\Adminhtml\Data;
use Turiknox\Sample\Model\Data as DataModel;
use Turiknox\Sample\Model\ResourceModel\Data\CollectionFactory;

abstract class MassAction extends Data
{
    /**
     * @var Filter
     */
    protected $_filter;

    /**
     * @var CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @var DataRepositoryInterface
     */
    protected $_dataRepository;

    /**
     * @var ForwardFactory
     */
    protected $_resultForwardFactory;
    /**
     * @var string
     */
    protected $_successMessage;
    /**
     * @var string
     */
    protected $_errorMessage;

    /**
     * MassAction constructor.
     *
     * @param Filter $filter
     * @param Registry $registry
     * @param DataRepositoryInterface $dataRepository
     * @param PageFactory $resultPageFactory
     * @param Context $context
     * @param CollectionFactory $collectionFactory
     * @param ForwardFactory $resultForwardFactory
     * @param $successMessage
     * @param $errorMessage
     */
    public function __construct(
        Filter $filter,
        Registry $registry,
        DataRepositoryInterface $dataRepository,
        PageFactory $resultPageFactory,
        Context $context,
        CollectionFactory $collectionFactory,
        ForwardFactory $resultForwardFactory,
        $successMessage,
        $errorMessage
    ) {
        $this->_filter               = $filter;
        $this->_dataRepository       = $dataRepository;
        $this->_collectionFactory    = $collectionFactory;
        $this->_resultForwardFactory = $resultForwardFactory;
        $this->_successMessage       = $successMessage;
        $this->_errorMessage         = $errorMessage;
        parent::__construct($registry, $dataRepository, $resultPageFactory, $resultForwardFactory, $context);
    }

    /**
     * @param DataModel $data
     * @return mixed
     */
    protected abstract function massAction(DataModel $data);

    /**
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        try {
            $collection = $this->_filter->getCollection($this->_collectionFactory->create());
            $collectionSize = $collection->getSize();
            foreach ($collection as $data) {
                $this->massAction($data);
            }
            $this->messageManager->addSuccessMessage(__($this->_successMessage, $collectionSize));
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage($e, __($this->_errorMessage));
        }
        $redirectResult = $this->resultRedirectFactory->create();
        $redirectResult->setPath('sample/data/index');
        return $redirectResult;
    }
}
