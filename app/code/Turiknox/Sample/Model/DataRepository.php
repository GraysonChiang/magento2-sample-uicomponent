<?php
namespace Turiknox\Sample\Model;
/*
 * Turiknox_Sample

 * @category   Turiknox
 * @package    Turiknox_Sample
 * @copyright  Copyright (c) 2017 Turiknox
 * @license    https://github.com/Turiknox/magento2-sample-uicomponent/blob/master/LICENSE.md
 * @version    1.0.0
 */
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\Search\FilterGroup;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\StateException;
use Magento\Framework\Exception\ValidatorException;
use Magento\Framework\Exception\NoSuchEntityException;
use Turiknox\Sample\Api\DataRepositoryInterface;
use Turiknox\Sample\Api\Data\DataInterface;
use Turiknox\Sample\Api\Data\DataInterfaceFactory;
use Turiknox\Sample\Api\Data\DataSearchResultsInterfaceFactory;
use Turiknox\Sample\Model\ResourceModel\Data as ResourceData;
use Turiknox\Sample\Model\ResourceModel\Data\CollectionFactory as DataCollectionFactory;

class DataRepository implements DataRepositoryInterface
{
    /**
     * @var array
     */
    protected $_instances = [];
    /**
     * @var ResourceData
     */
    protected $_resource;
    /**
     * @var DataCollectionFactory
     */
    protected $_dataCollectionFactory;
    /**
     * @var DataSearchResultsInterfaceFactory
     */
    protected $_searchResultsFactory;
    /**
     * @var DataInterfaceFactory
     */
    protected $_dataInterfaceFactory;
    /**
     * @var DataObjectHelper
     */
    protected $_dataObjectHelper;

    public function __construct(
        ResourceData $resource,
        DataCollectionFactory $dataCollectionFactory,
        DataSearchResultsInterfaceFactory $dataSearchResultsInterfaceFactory,
        DataInterfaceFactory $dataInterfaceFactory,
        DataObjectHelper $dataObjectHelper
    )
    {
        $this->_resource = $resource;
        $this->_dataCollectionFactory = $dataCollectionFactory;
        $this->_searchResultsFactory = $dataSearchResultsInterfaceFactory;
        $this->_dataInterfaceFactory = $dataInterfaceFactory;
        $this->_dataObjectHelper = $dataObjectHelper;
    }

    /**
     * @param DataInterface $data
     * @return DataInterface
     * @throws CouldNotSaveException
     */
    public function save(DataInterface $data)
    {
        try {
            /** @var DataInterface|\Magento\Framework\Model\AbstractModel $data */
            $this->_resource->save($data);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the data: %1',
                $exception->getMessage()
            ));
        }
        return $data;
    }

    /**
     * Get data record
     *
     * @param $dataId
     * @return mixed
     * @throws NoSuchEntityException
     */
    public function getById($dataId)
    {
        if (!isset($this->_instances[$dataId])) {
            /** @var \Turiknox\Sample\Api\Data\DataInterface|\Magento\Framework\Model\AbstractModel $data */
            $data = $this->_dataInterfaceFactory->create();
            $this->_resource->load($data, $dataId);
            if (!$data->getId()) {
                throw new NoSuchEntityException(__('Requested data doesn\'t exist'));
            }
            $this->_instances[$dataId] = $data;
        }
        return $this->_instances[$dataId];
    }

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @return \Turiknox\Sample\Api\Data\DataSearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        /** @var \Turiknox\Sample\Api\Data\DataSearchResultsInterface $searchResults */
        $searchResults = $this->_searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);

        /** @var \Turiknox\Sample\Model\ResourceModel\Data\Collection $collection */
        $collection = $this->_dataCollectionFactory->create();

        //Add filters from root filter group to the collection
        /** @var FilterGroup $group */
        foreach ($searchCriteria->getFilterGroups() as $group) {
            $this->addFilterGroupToCollection($group, $collection);
        }
        $sortOrders = $searchCriteria->getSortOrders();
        /** @var SortOrder $sortOrder */
        if ($sortOrders) {
            foreach ($searchCriteria->getSortOrders() as $sortOrder) {
                $field = $sortOrder->getField();
                $collection->addOrder(
                    $field,
                    ($sortOrder->getDirection() == SortOrder::SORT_ASC) ? 'ASC' : 'DESC'
                );
            }
        } else {
            $field = 'data_id';
            $collection->addOrder($field, 'ASC');
        }
        $collection->setCurPage($searchCriteria->getCurrentPage());
        $collection->setPageSize($searchCriteria->getPageSize());

        $data = [];
        foreach ($collection as $datum) {
            $dataDataObject = $this->_dataInterfaceFactory->create();
            $this->_dataObjectHelper->populateWithArray($dataDataObject, $datum->getData(), DataInterface::class);
            $data[] = $dataDataObject;
        }
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults->setItems($data);
    }

    /**
     * @param DataInterface $data
     * @return bool
     * @throws CouldNotSaveException
     * @throws StateException
     */
    public function delete(DataInterface $data)
    {
        /** @var \Turiknox\Sample\Api\Data\DataInterface|\Magento\Framework\Model\AbstractModel $data */
        $id = $data->getId();
        try {
            unset($this->_instances[$id]);
            $this->_resource->delete($data);
        } catch (ValidatorException $e) {
            throw new CouldNotSaveException(__($e->getMessage()));
        } catch (\Exception $e) {
            throw new StateException(
                __('Unable to remove data %1', $id)
            );
        }
        unset($this->_instances[$id]);
        return true;
    }

    /**
     * @param $dataId
     * @return bool
     */
    public function deleteById($dataId)
    {
        $data = $this->getById($dataId);
        return $this->delete($data);
    }
}