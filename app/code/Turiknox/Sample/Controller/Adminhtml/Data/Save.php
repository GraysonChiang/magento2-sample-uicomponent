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
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Magento\Backend\Model\View\Result\ForwardFactory;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Message\Manager;
use Magento\Framework\Api\DataObjectHelper;
use Turiknox\Sample\Api\DataRepositoryInterface;
use Turiknox\Sample\Api\Data\DataInterface;
use Turiknox\Sample\Api\Data\DataInterfaceFactory;
use Turiknox\Sample\Controller\Adminhtml\Data;

class Save extends Data
{
    /**
     * @var Manager
     */
    protected $_messageManager;

    /**
     * @var DataRepositoryInterface
     */
    protected $_dataRepository;

    /**
     * @var DataInterfaceFactory
     */
    protected $_dataFactory;

    /**
     * @var DataObjectHelper
     */
    protected $_dataObjectHelper;


    public function __construct(
        Registry $registry,
        DataRepositoryInterface $dataRepository,
        PageFactory $resultPageFactory,
        ForwardFactory $resultForwardFactory,
        Manager $messageManager,
        DataInterfaceFactory $dataFactory,
        DataObjectHelper $dataObjectHelper,
        Context $context
    )
    {
        $this->_messageManager   = $messageManager;
        $this->_dataFactory      = $dataFactory;
        $this->_dataRepository   = $dataRepository;
        $this->_dataObjectHelper  = $dataObjectHelper;
        parent::__construct($registry, $dataRepository, $resultPageFactory, $resultForwardFactory, $context);
    }

    /**
     * Save action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();

        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
            $id = $this->getRequest()->getParam('data_id');
            if ($id) {
                $model = $this->_dataRepository->getById($id);
            } else {
                unset($data['data_id']);
                $model = $this->_dataFactory->create();
            }

            try {
                $this->_dataObjectHelper->populateWithArray($model, $data, DataInterface::class);
                $this->_dataRepository->save($model);
                $this->_messageManager->addSuccessMessage(__('You saved this data.'));
                $this->_getSession()->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['data_id' => $model->getId(), '_current' => true]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->_messageManager->addErrorMessage($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->_messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->_messageManager->addException($e, __('Something went wrong while saving the data.'));
            }

            $this->_getSession()->setFormData($data);
            return $resultRedirect->setPath('*/*/edit', ['data_id' => $this->getRequest()->getParam('data_id')]);
        }
        return $resultRedirect->setPath('*/*/');
    }
}