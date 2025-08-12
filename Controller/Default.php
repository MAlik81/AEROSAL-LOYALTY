<?php
class Migastarter_Controller_Default extends Core_Controller_Default {
    public function init() {
     parent::init();
  }
    public function indexAction() {
        $this->_sendJson([
            'error' => 1,
            'message' => p__('Migastarter','Invalid Access.')
        ]);
	}







	







}