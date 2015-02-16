<?php
class Csv_MailSender_Model_Send
{
	public function sendEmails() 
	{
		(int) $mailCount = Mage::getStoreConfig('csv_mailsender/options/mail_count');
		(int) $userNameColumnId = Mage::getStoreConfig('csv_mailsender/options/user_name_csv_id');
		(int) $userEmailColumnId = Mage::getStoreConfig('csv_mailsender/options/user_email_csv_id');
		(int) $emailTemplateCode = Mage::getStoreConfig('csv_mailsender/options/email_template');
		/** @var Mage_Core_Model_Email_Template $emailTemplate */
		$emailTemplate = Mage::getModel('core/email_template')->loadByCode($emailTemplateCode);
		$senderName = Mage::getStoreConfig('csv_mailsender/options/sender_name');		
		$senderEmail = Mage::getStoreConfig('csv_mailsender/options/sender_email');	
		$storeId = Mage::app()->getStore()->getStoreId();	
		$flagModel = Mage::getModel('csv_mailsender/flag_send');
		$lastParams = $flagModel->loadSelf()->getFlagData();
		(int) $lastId = $lastParams['last_id'];
		$filePath = $this->getFilePath();
		$csvData = $this->readCsvData($filePath);
		(int) $i = 0;
		foreach (array_slice($csvData, 1 + $lastId) as $item) {
			if($i < $mailCount) {
				$recepientName = $item[$userNameColumnId-1];
				$recepientEmail = $item[$userEmailColumnId-1];
				$sender = array('name' => $senderName, 'email' => $senderEmail);
        		$vars = array('user_name' => $recepientName);
        		$emailTemplate->sendTransactional($emailTemplateCode, $sender, $recepientEmail, $recepientName, $vars, $storeId);
				$i++;
				$currentState = array('last_id' => $lastId + $i);
				$flagModel->setFlagData($currentState)->save();
			} else {
				Mage::log($currentState['last_id']);
				break;
				return true;
			}
		}
	}

	public function readCsvData($filePath)
	{
		$csv = new Varien_File_Csv();
		return $csv->getData($filePath);
    }

	public function getFilePath() 
	{
		return Mage::getBaseDir('var') . DS . Mage::getStoreConfig('csv_mailsender/options/path');
	}
}
	 