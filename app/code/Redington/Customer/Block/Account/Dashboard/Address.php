<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Redington\Customer\Block\Account\Dashboard;

use Magento\Customer\Api\Data\AddressInterface;
use Magento\Customer\Model\Address\Mapper;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class to manage customer dashboard addresses section
 *
 * @api
 * @since 100.0.2
 */
class Address extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Customer\Model\Address\Config
     */
    protected $_addressConfig;

    /**
     * @var \Magento\Customer\Helper\Session\CurrentCustomer
     */
    protected $currentCustomer;

    /**
     * @var \Magento\Customer\Helper\Session\CurrentCustomerAddress
     */
    protected $currentCustomerAddress;

    /**
     * @var Mapper
     */
    protected $addressMapper;

    /**  
    * @var \Magento\Customer\Model\CustomerFactory
    */
    protected $customerFactory;

    /**  
    * @var \Magento\Customer\Model\AddressFactory
    */
    protected $addressFactory;
    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer
     * @param \Magento\Customer\Helper\Session\CurrentCustomerAddress $currentCustomerAddress
     * @param \Magento\Customer\Model\Address\Config $addressConfig
     * @param Mapper $addressMapper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer,
        \Magento\Customer\Helper\Session\CurrentCustomerAddress $currentCustomerAddress,
        \Magento\Customer\Model\Address\Config $addressConfig,
        \Redington\Company\Helper\Data $companyHelper,
        Mapper $addressMapper,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Customer\Api\AddressRepositoryInterface $addressFactory,
		\Magento\Directory\Model\CountryFactory $countryFactory,
        array $data = []
    ) {
        $this->currentCustomer = $currentCustomer;
        $this->currentCustomerAddress = $currentCustomerAddress;
        $this->_addressConfig = $addressConfig;
        parent::__construct($context, $data);
        $this->addressMapper = $addressMapper;
        $this->companyHelper = $companyHelper;
        $this->customerFactory = $customerFactory;
        $this->addressFactory = $addressFactory;
		$this->_countryFactory = $countryFactory;
    }

    /**
     * Get the logged in customer
     *
     * @return \Magento\Customer\Api\Data\CustomerInterface|null
     */
    public function getCustomer()
    {
        try {
            return $this->currentCustomer->getCustomer();
        } catch (NoSuchEntityException $e) {
            return null;
        }
    }

    /**
     * HTML for Shipping Address
     *
     * @return \Magento\Framework\Phrase|string
     */
    public function getPrimaryShippingAddressHtml()
    {
       

        try {
             $adminId = $this->companyHelper->getCompanyAdminId();
            // print_r($this->currentCustomerAddress->getData());
            if($adminId)
            {
                $customer = $this->customerFactory->create()->load($adminId);
                $shippingAddressId = $customer->getDefaultShipping();
                //$address = $this->currentCustomerAddress->getDefaultShippingAddress();
                $address = $this->addressFactory->getById($shippingAddressId);

            }
            else
            {
                 $address = $this->currentCustomerAddress->getDefaultShippingAddress();
            }
           
        } catch (NoSuchEntityException $e) {
            return __(' You have not set a default shipping address.');
        }

        if ($address) {
            return $address;
        } else {

            return __('You have not set a default shipping address.');
        }
    }

    /**
     * HTML for Billing Address
     *
     * @return \Magento\Framework\Phrase|string
     */
    public function getPrimaryBillingAddressHtml()
    {
        try {
            $adminId = $this->companyHelper->getCompanyAdminId();
            // print_r($this->currentCustomerAddress->getData());
            if($adminId)
            {
                $customer = $this->customerFactory->create()->load($adminId);
                $billingAddressId  = $customer->getDefaultBilling();
                //$address = $this->currentCustomerAddress->getDefaultShippingAddress();
                $address = $this->addressFactory->getById($billingAddressId);

            }
            else
            {
                $address = $this->currentCustomerAddress->getDefaultBillingAddress();
            }
            
        } catch (NoSuchEntityException $e) {
            return __('You have not set a default billing address.');
        }

        if ($address) {
            return $address;
        } else {
            return __('You have not set a default billing address.');
        }
    }

    /**
     * @return string
     */
    public function getPrimaryShippingAddressEditUrl()
    {
        if (!$this->getCustomer()) {
            return '';
        } else {
            $address = $this->currentCustomerAddress->getDefaultShippingAddress();
            $addressId = $address ? $address->getId() : null;
            return $this->_urlBuilder->getUrl(
                'customer/address/edit',
                ['id' => $addressId]
            );
        }
    }

    /**
     * @return string
     */
    public function getPrimaryBillingAddressEditUrl()
    {
        if (!$this->getCustomer()) {
            return '';
        } else {
            $address = $this->currentCustomerAddress->getDefaultBillingAddress();
            $addressId = $address ? $address->getId() : null;
            return $this->_urlBuilder->getUrl(
                'customer/address/edit',
                ['id' => $addressId]
            );
        }
    }

    /**
     * @return string
     */
    public function getAddressBookUrl()
    {
        return $this->getUrl('customer/address/');
    }

    /**
     * Render an address as HTML and return the result
     *
     * @param AddressInterface $address
     * @return string
     */
    protected function _getAddressHtml($address)
    {
        /** @var \Magento\Customer\Block\Address\Renderer\RendererInterface $renderer */
        $renderer = $this->_addressConfig->getFormatByCode('html')->getRenderer();
        return $renderer->renderArray($this->addressMapper->toFlatArray($address));
    }
	
	public function getCountryname($countryCode){
		 $country = $this->_countryFactory->create()->load($countryCode);
        return $country->getName();
		
	}
}
