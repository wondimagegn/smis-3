<?php
    /**
     * Export all member records in .xls format
     * with the help of the xlsHelper
     */    
     $this->Xls->setHeader('Master Sheet');
     $this->Xls->addXmlHeader();
     $this->Xls->setWorkSheetName('Section'.$section_detail['name']);
     $this->Xls->openRow();
                $this->Xls->writeString('College:');
     $this->Xls->closeRow(); 
      
	$this->Xls->openRow();
	$this->Xls->closeRow(); 
		
    $this->Xls->addXmlFooter();
    exit();
?>

