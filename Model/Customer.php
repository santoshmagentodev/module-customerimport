<?php

namespace Pramod\CustomerImport\Model;

use Magento\Customer\Model\CustomerFactory;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class Customer.
 */
class Customer
{
    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var CustomerFactory
     */
    protected $customerFactory;

    /**
     * @var \Magento\Customer\Api\Data\CustomerInterfaceFactory
     */
    protected $customerInterfaceFactory;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    protected $customerRepository;

     /**
     * @var \Magento\Framework\Encryption\EncryptorInterface
     */
    protected $encryptorInterface;


    /**
     * @param StoreManagerInterface $storeManager
     * @param CustomerFactory $customerFactory
     * @param \Magento\Customer\Api\Data\CustomerInterfaceFactory $customerInterfaceFactory
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param \Magento\Framework\Encryption\EncryptorInterface $encryptorInterface
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        CustomerFactory $customerFactory,
        \Magento\Customer\Api\Data\CustomerInterfaceFactory $customerInterfaceFactory,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Framework\Encryption\EncryptorInterface  $encryptorInterface
    ) {
        $this->storeManager = $storeManager;
        $this->customerFactory = $customerFactory;
        $this->customerInterfaceFactory = $customerInterfaceFactory;
        $this->customerRepository = $customerRepository;
        $this->encryptorInterface = $encryptorInterface;
    }

     /**
     * @param $data
     * @return true
     * @throws AlreadyExistsException
     * @throws NoSuchEntityException
     */
    public function updateJsonCustomer($data)
    {
        $data['emailaddress']= array_key_exists('emailaddress',$data)?$data['emailaddress']:'';
        $data['fname'] = array_key_exists('fname',$data)?$data['fname']:'';
        $data['lname'] = array_key_exists('lname',$data)?$data['lname']:'';
        $websiteId = $this->storeManager->getStore()->getWebsiteId();
        $customerData = $this->customerFactory->create()->setWebsiteId($websiteId)->loadByEmail($data['emailaddress']);
        try {
            if($customerData->getId()) {
                $customer = $this->customerRepository->get($data['emailaddress']);
                $customer->setWebsiteId($websiteId)
                    ->setFirstname($data['fname'])
                    ->setLastname($data['lname'])
                    ->setEmail($data['emailaddress']);
                $this->customerRepository->save($customer);  //update customer
            }else{
                $this->createCustomer($data);
            }
            return true;
        } catch (AlreadyExistsException $e) {
            throw new AlreadyExistsException(__($e->getMessage()), $e);
        } catch (\Exception $e) {
            throw new \RuntimeException(__($e->getMessage()));
        }
    }


    /**
     * @param $data
     * @return true
     * @throws AlreadyExistsException
     * @throws NoSuchEntityException
     */
    public function updateCsvCustomer($data)
    {
        $websiteId = $this->storeManager->getStore()->getWebsiteId();
        $customerData = $this->customerFactory->create()->setWebsiteId($websiteId)->loadByEmail($data[2]);
        try {
            if($customerData->getId()) {
                $customer = $this->customerRepository->get($data[2]);
                $customer->setWebsiteId($websiteId)
                    ->setFirstname($data[0])
                    ->setLastname($data[1])
                    ->setEmail($data[2]);
                $this->customerRepository->save($customer);  //update customer
            }else{
                $csvData['emailaddress']= $data[2];
                $csvData['fname'] = $data[0];
                $csvData['lname'] = $data[1];
                $this->createCustomer($csvData);
            }
            return true;
        } catch (AlreadyExistsException $e) {
            throw new AlreadyExistsException(__($e->getMessage()), $e);
        } catch (\Exception $e) {
            throw new \RuntimeException(__($e->getMessage()));
        }
    }


    /**
     * @param $data
     * @return void
     * @throws LocalizedException
     * @throws NoSuchEntityException
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\State\InputMismatchException
     */
    public function createCustomer($data)
    {
        $websiteId = $this->storeManager->getStore()->getWebsiteId();
        try {
            $customer = $this->customerInterfaceFactory->create();
            $customer->setWebsiteId($websiteId);
            $customer->setEmail($data['emailaddress']);
            $customer->setFirstname($data['fname']);
            $customer->setLastname($data['lname']);
            $hashedPassword = $this->encryptorInterface->getHash('MyNewPass', true);
            $this->customerRepository->save($customer, $hashedPassword);
        } catch (Exception $e) {
            throw new \RuntimeException(__($e->getMessage()));
        }
    }
}
