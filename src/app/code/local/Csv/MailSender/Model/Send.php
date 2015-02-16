<?php
class Csv_MailSender_Model_Send extends Mage_Core_Helper_Abstract
{
	public function sendEmails() 
	{
		(int) $mailCount = Mage::getStoreConfig('csv_mailsender/options/mail_count');
		$flagModel = Mage::getModel('csv_mailsender/flag_send');
		$lastParams = $flagModel->loadSelf()->getFlagData();
		(int) $lastId = $lastParams['last_id'];
		$filePath = $this->getFilePath();
		$csvData = $this->readCsvData($filePath);

		(int) $i = 0;
		foreach (array_slice($csvData, 1 + $lastId) as $item) {
			if($i < $mailCount) {
				$customerName = $item[2+1];
				$customerEmail = $item[9+1];
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
	 