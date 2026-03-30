<?php
namespace Opencart\Catalog\Controller\Extension\PurpletreePos\Startup;
class Poscart extends \Opencart\System\Engine\Controller {
	public function index(): void {
		// Point Of Sale Cart
		$this->registry->set('poscart', new \Opencart\System\Library\Extension\PurpletreePos\Cart\Ptsposcart($this->registry));
	}
}