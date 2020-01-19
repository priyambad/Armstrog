<?php
/**
 * Copyright © Redington. All rights reserved.
 *
 */
namespace Redington\Category\Controller\Adminhtml\Category;

/**
 * Class Index for execute method
 */
class Index extends \Magento\Backend\App\Action
{

     /**
     * Undocumented function
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
		
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
		
    }

   
    public function execute()
    {
		
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->prepend((__('Category Permission')));

        return $resultPage;
    }
}
