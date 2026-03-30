<?php
namespace Opencart\Admin\Controller\Extension\PurpletreePos\Pos;
class Posproduct extends \Opencart\System\Engine\Controller {
		private $error = array();
		
		public function index() {

    if (!$this->user->hasPermission('access', 'extension/purpletree_pos/pos/posproduct')) {
        $this->response->redirect(
            $this->url->link('error/permission', 'user_token=' . $this->session->data['user_token'], true)
        );
        return;
    }

    $this->load->language('extension/purpletree_pos/posproduct');
    $this->document->setTitle($this->language->get('heading_title'));
    $this->load->model('extension/purpletree_pos/posproduct');
    $this->getList();
}

/*		public function posPrintBarcode() {
			if (isset($this->request->post['qty'])) {
			    $data['qty'] = $this->request->post['qty'];
			} else {
				$data['qty'] = null;
			}
			if (isset($this->request->post['printbarcode'])) {
			    $data['printbarcode'] = $this->request->post['printbarcode'];
			} else {
				$data['printbarcode'] = null;
			}
            if (isset($this->request->post['printbarcodename'])) {
			    $data['printbarcodename'] = $this->request->post['printbarcodename'];
			} else {
				$data['printbarcodename'] = null;
			}
            if (isset($this->request->post['printbarcodeprice'])) {
			    $data['printbarcodeprice'] = $this->request->post['printbarcodeprice'];
			} else {
				$data['printbarcodeprice'] = null;
			}
             // if (isset($this->request->post['printbarcodespecialprice'])) {
			    // $data['printbarcodespecialprice'] = $this->request->post['printbarcodespecialprice'];
				
			// } else {
				// $data['printbarcodespecialprice'] = null;
			// }			
			$data['header'] = $this->load->controller('common/header');
		    $data['column_left'] = $this->load->controller('common/column_left');
		    $data['footer'] = $this->load->controller('common/footer');
         $data['action_back'] = $this->url->link('extension/purpletree_pos/pos/posproduct', 'user_token=' . $this->session->data['user_token'], true);
		$data['baseurl']=HTTP_CATALOG;
		$this->response->setOutput($this->load->view('extension/purpletree_pos/pos_print_barcode', $data));
		
		}*/
		
public function posMassPrintBarcode(): void {

    /* ===============================
       VALIDATE INPUT
       =============================== */
    if (
        empty($this->request->post['massprintbarcode']) ||
        empty($this->request->post['qty'])
    ) {
        exit('No data received');
    }

    /* ===============================
       FIX JSON INPUT
       =============================== */
    $rawJson = html_entity_decode(
        $this->request->post['massprintbarcode'],
        ENT_QUOTES,
        'UTF-8'
    );
    $rawJson = stripslashes($rawJson);

    $items = json_decode($rawJson, true);

    if (json_last_error() !== JSON_ERROR_NONE || !is_array($items)) {
        header('Content-Type: text/plain');
        echo "Invalid JSON\n\n";
        echo $rawJson;
        exit;
    }

    $qty = max(1, (int)$this->request->post['qty']);

    /* ===============================
       LOAD DEPENDENCIES
       =============================== */
    $this->load->model('catalog/product');
    require_once(DIR_SYSTEM . 'library/tcpdf/tcpdf.php');

    /* ===============================
       AUTO FIT TEXT
       =============================== */
    $fitTextCell = function (
        $pdf, $x, $y, $w, $h, $text,
        $font='helvetica', $style='B',
        $maxSize=6, $minSize=4.5, $align='C'
    ) {
        for ($size = $maxSize; $size >= $minSize; $size -= 0.2) {
            $pdf->SetFont($font, $style, $size);
            if ($pdf->GetStringWidth($text) <= $w) break;
        }
        $pdf->SetXY($x, $y);
        $pdf->Cell($w, $h, $text, 0, 0, $align);
    };

    /* ===============================
       DRAW SINGLE LABEL
       =============================== */
    $drawLabel = function (
    $pdf, $startX, $topOffset,
    $barcode, $productName, $sku,
    $rackCode,
    $sgcCenter, $sgcCorner
) use ($fitTextCell) {


        // Header
        $fitTextCell($pdf, $startX + 2, 1.2 + $topOffset, 38, 2.3, $sgcCenter, 'helvetica', 'B', 6, 5, 'L');
        $fitTextCell($pdf, $startX + 30, 1.2 + $topOffset, 10, 2.3, $sgcCorner, 'helvetica', 'B', 5.5, 4.5, 'R');

        // Product Name
        $fitTextCell($pdf, $startX + 2, 3.6 + $topOffset, 38, 2.6, $productName, 'helvetica', 'B', 6, 5, 'L');

        // Barcode
        $barcodeStyle = [
            'align'    => 'C',
            'border'   => false,
            'hpadding' => 0,
            'vpadding' => 0,
            'text'     => false
        ];
        
        $pdf->write1DBarcode(
            $barcode,
            'C128',
            $startX + 3,
            6.5 + $topOffset,
            40,
            8.0,
            0.25,
            $barcodeStyle,
            'N'
        );


        // Barcode text
        $pdf->SetFont('helvetica', 'B', 5.5);
        $pdf->SetXY($startX + 2, 15.2 + $topOffset);
        $pdf->Cell(38, 2.5, $barcode, 0, 0, 'C');

        // PRICE TAG
        $pdf->SetFont('helvetica', 'B', 5);
        $pdf->SetXY($startX + 2, 17.6 + $topOffset);
        $pdf->Cell(38, 2, $sku, 0, 0, 'C');
        
        // RACK (below price)
        if (!empty($rackCode)) {
            $pdf->SetFont('helvetica', 'B', 4.8);
            $pdf->SetXY($startX + 2, 19.8 + $topOffset);
            $pdf->Cell(38, 2, $rackCode, 0, 0, 'C');
        }

        
    };

    /* ===============================
       PDF SETUP
       =============================== */
    $pdf = new \TCPDF('L', 'mm', [100, 25], true, 'UTF-8', false);
    $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(false);
    $pdf->SetMargins(0, 0, 0);
    $pdf->SetAutoPageBreak(false);

    $xPositions = [1, 51]; // LEFT & RIGHT
    $topOffset  = 2.2;
    $labelIndex = 0;

    /* ===============================
       PRINT ALL PRODUCTS (ODD / EVEN SAFE)
       =============================== */
    for ($copy = 1; $copy <= $qty; $copy++) {

        foreach ($items as $item) {

            if (empty($item['product_id'])) {
                continue;
            }

            $product = $this->model_catalog_product->getProduct((int)$item['product_id']);
            /*if (!$product || empty($product['upc'])) {
                continue;
            }*/

            // New page for every 2 labels
            if ($labelIndex % 2 === 0) {
                $pdf->AddPage();
            }

            $col = $labelIndex % 2;

            if (!empty($item['type']) && $item['type'] === 'box') {
                $barcode = $product['upc'];
            } else {
                $barcode = $product['sku'];
            }
            $productName = $product['name'];

            $r_base = (int)preg_replace('/[^0-9]/', '', $product['r_tag'] ?? '0');
            $w_base = (int)preg_replace('/[^0-9]/', '', $product['w_tag'] ?? '0');

            $r_final = 'R' . ($r_base + (float)$product['price']);
            $w_final = 'W' . ($w_base + (float)$product['wholesale_price']);
            $priceTagText = $w_final . '-' . $r_final;
            $rackCode = $product['rack_code'] ?? '';

            if (!empty($item['type']) && $item['type'] === 'box') {
                $sgcCenter = 'SGC-BX-' . $product['product_id'];
            } else {
                $sgcCenter = 'SGC-BX-' . (!empty($product['box_id']) ? $product['box_id'] : 'U' . $product['product_id']);
            }

           $sgcCorner = !empty($product['max_quantity'])? ((int)$product['max_quantity'] . 'pc'): '';


            /* ===============================
               DRAW
               =============================== */
            $drawLabel(
                $pdf,
                $xPositions[$col],
                $topOffset,
                $barcode,
                $productName,
                $priceTagText,
                $rackCode,
                $sgcCenter,
                $sgcCorner
            );

            $labelIndex++;
        }
    }

    /* ===============================
       OUTPUT
       =============================== */
    ob_end_clean();
    $pdf->Output('mass_box_barcodes.pdf', 'I');
    exit;
}



		
		protected function getList() {
			if (isset($this->request->get['filter_name'])) {
				$filter_name = $this->request->get['filter_name'];
				} else {
				$filter_name = null;
			}
			
			if (isset($this->request->get['filter_model'])) {
				$filter_model = $this->request->get['filter_model'];
				} else {
				$filter_model = null;
			}
			
			if (isset($this->request->get['filter_price'])) {
				$filter_price = $this->request->get['filter_price'];
				} else {
				$filter_price = null;
			}
			
			if (isset($this->request->get['filter_quantity'])) {
				$filter_quantity = $this->request->get['filter_quantity'];
				} else {
				$filter_quantity = null;
			}
			
			if (isset($this->request->get['filter_tag'])) {
				$filter_tag = $this->request->get['filter_tag'];
				} else {
				$filter_tag = null;
			}
			if (isset($this->request->get['filter_rack_code'])) {
				$filter_rack_code = $this->request->get['filter_rack_code'];
				} else {
				$filter_rack_code = null;
			}
			if (isset($this->request->get['filter_box_id'])) {
				$filter_box_id = $this->request->get['filter_box_id'];
				} else {
				$filter_box_id = null;
			}
			if (isset($this->request->get['filter_barcode'])) {
				$filter_barcode = $this->request->get['filter_barcode'];
				} else {
				$filter_barcode = null;
			}
			
			if (isset($this->request->get['filter_status'])) {
				$filter_status = $this->request->get['filter_status'];
				} else {
				$filter_status = null;
			}
			
			if (isset($this->request->get['filter_approval'])) {
				$filter_approval = $this->request->get['filter_approval'];
				} else {
				$filter_approval = null;
			}
			
			if (isset($this->request->get['filter_image'])) {
				$filter_image = $this->request->get['filter_image'];
				} else {
				$filter_image = null;
			}
			
			if (isset($this->request->get['sort'])) {
				$sort = $this->request->get['sort'];
				} else {
				$sort = '';
			}
			
			if (isset($this->request->get['order'])) {
				$order = $this->request->get['order'];
				} else {
				$order = 'DESC';
			}
			
			if (isset($this->request->get['page'])) {
				$page = $this->request->get['page'];
				} else {
				$page = 1;
			}
			
			
			$url = '';
			
			if (isset($this->request->get['filter_name'])) {
				$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
			}
			
			if (isset($this->request->get['filter_model'])) {
				$url .= '&filter_model=' . urlencode(html_entity_decode($this->request->get['filter_model'], ENT_QUOTES, 'UTF-8'));
			}
			
			if (isset($this->request->get['filter_rack_code'])) {
				$url .= '&filter_rack_code=' . $this->request->get['filter_rack_code'];
			}
			if (isset($this->request->get['filter_box_id'])) {
				$url .= '&filter_box_id=' . $this->request->get['filter_box_id'];
			}
			if (isset($this->request->get['filter_price'])) {
				$url .= '&filter_price=' . $this->request->get['filter_price'];
			}
			
			if (isset($this->request->get['filter_quantity'])) {
				$url .= '&filter_quantity=' . $this->request->get['filter_quantity'];
			}
			
			if (isset($this->request->get['filter_tag'])) {
				$url .= '&filter_tag=' . $this->request->get['filter_tag'];
			}
			
			if (isset($this->request->get['filter_status'])) {
				$url .= '&filter_status=' . $this->request->get['filter_status'];
			}
			
			if (isset($this->request->get['filter_image'])) {
				$url .= '&filter_image=' . $this->request->get['filter_image'];
			}
			
			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}
			
			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}
			
			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}
			
			
			$data['breadcrumbs'] = array();
			
			$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
			);
			
			$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/purpletree_pos/pos/posproduct', 'user_token=' . $this->session->data['user_token'] . $url, true)
			);
			$data['add'] = $this->url->link('catalog/product|form', 'user_token=' . $this->session->data['user_token'] . $url . '&pos_product=1', true);
			$data['products'] = array();
			
			$filter_data = array(
			'filter_name'	  => $filter_name,
			'filter_model'	  => $filter_model,
			'filter_rack_code'	  => $filter_rack_code,
			'filter_box_id' => $filter_box_id,
			'filter_barcode'     => $filter_barcode,
			'filter_price'	  => $filter_price,
			'filter_quantity' => $filter_quantity,
			'filter_status'   => $filter_status,
			'filter_tag' => $filter_tag,
			'sort'            => $sort,
			'order'           => $order,
			'start'           => ($page - 1) * $this->config->get('config_pagination_admin'),
			'limit'           => $this->config->get('config_pagination_admin')
			);
			
			$this->load->model('tool/image');
			
			$this->load->model('catalog/product');
			$data['delete'] = $this->url->link('extension/purpletree_pos/pos/posproduct|delete', 'user_token=' . $this->session->data['user_token'] . $url, true);
			$product_total = $this->model_extension_purpletree_pos_posproduct->getTotalPosProducts($filter_data);
			$results = array();
			$results = $this->model_extension_purpletree_pos_posproduct->getPosProducts($filter_data);
			//\echo"<pre>"; print_r($results); die;
			if($results){
			foreach ($results as $result) {
				if (is_file(DIR_IMAGE . $result['image'])) {
					$image = $this->model_tool_image->resize($result['image'], 40, 40);
					} else {
					$image = $this->model_tool_image->resize('no_image.png', 40, 40);
				}
				if(version_compare(VERSION, '4.0.2.3', '<=')){
				$special = false;
				
				$product_specials = $this->model_catalog_product->getSpecials($result['product_id']);
				
				foreach ($product_specials  as $product_special) {
					if (($product_special['date_start'] == '0000-00-00' || strtotime($product_special['date_start']) < time()) && ($product_special['date_end'] == '0000-00-00' || strtotime($product_special['date_end']) > time())) {
						$special = $product_special['price'];
						
						break;
					}
				}
				}else{
                  $special='';
}
				$edit = $this->url->link('catalog/product|form', 'user_token=' . $this->session->data['user_token'] . '&product_id=' . $result['product_id'] . $url.'&pos_product=1', true);
				$data['products'][] = array(
				'product_id' => $result['product_id'],
				'image'      => $image,
				'name'       => $result['name'],
				'model'      => $result['model'],
				'rack_code'      => $result['rack_code'],
				'box_code' => $result['box_code'] ?? '',
				'box_id' => $result['box_id'],
				'price'      => $this->currency->format($result['price'], $this->config->get('config_currency')),
				'price_raw'          => (float)$result['price'],
                'wholesale_price'    => (float)$result['wholesale_price'],
                'r_tag'              => $result['r_tag'],
                'w_tag'              => $result['w_tag'],
                'max_quantity' => $result['max_quantity'],
				'sku'      => $result['sku'],
				'short_code' => $result['short_code'],
				'upc'      => $result['upc'],
				'ean'      => $result['ean'],
				'jan'      => $result['jan'],
				'isbn'      => $result['isbn'],
				'mpn'      => $result['mpn'],
				'special'    => $special,
				'quantity'   => $result['pos_quentity'],
				'status'     => $result['product_status'] ? $this->language->get('text_enabled') : $this->language->get('text_disabled'),				
				'edit'       => $edit
				);
			}
			}
			
			$data['heading_title'] = $this->language->get('heading_title');
			$data['button_massbarcodeprint'] = $this->language->get('button_massbarcodeprint');
			$data['text_popupheading'] = $this->language->get('text_popupheading');
			$data['text_masspopupheading'] = $this->language->get('text_masspopupheading');
			$data['text_prints'] = $this->language->get('text_prints');
			$data['text_qty'] = $this->language->get('text_qty');
			$data['text_printsall'] = $this->language->get('text_printsall');
			$data['text_close'] = $this->language->get('text_close');
			$data['text_original_title'] = $this->language->get('text_original_title');
			
			$data['text_list'] = $this->language->get('text_list');
			$data['text_enabled'] = $this->language->get('text_enabled');
			$data['text_disabled'] = $this->language->get('text_disabled');
			$data['text_no_results'] = $this->language->get('text_no_results');
			$data['text_confirm'] = $this->language->get('text_confirm');
			$data['text_yes'] = $this->language->get('text_yes');
			$data['text_no'] = $this->language->get('text_no');
			$data['text_all'] = $this->language->get('text_all');
			
			$data['column_image'] = $this->language->get('column_image');
			$data['column_name'] = $this->language->get('column_name');
			$data['column_seller_name'] = $this->language->get('column_seller_name');
			$data['column_model'] = $this->language->get('column_model');
			$data['column_price'] = $this->language->get('column_price');
			$data['column_barcode'] = $this->language->get('column_barcode');
			
			$data['entry_sku'] = $this->language->get('entry_sku');
			$data['entry_upc'] = $this->language->get('entry_upc');
			$data['entry_ean'] = $this->language->get('entry_ean');
			$data['entry_mpn'] = $this->language->get('entry_mpn');
			$data['entry_jan'] = $this->language->get('entry_jan');
			$data['entry_isbn'] = $this->language->get('entry_isbn');
			
			$data['column_quantity'] = $this->language->get('column_quantity');
			$data['column_approved'] = $this->language->get('column_approved');
			$data['column_status'] = $this->language->get('column_status');
			$data['column_action'] = $this->language->get('column_action');
			
			$data['entry_name'] = $this->language->get('entry_name');
			$data['entry_model'] = $this->language->get('entry_model');
			$data['entry_price'] = $this->language->get('entry_price');
			$data['entry_quantity'] = $this->language->get('entry_quantity');
			$data['entry_status'] = $this->language->get('entry_status');
			$data['entry_image'] = $this->language->get('entry_image');
			
			$data['button_approve'] = $this->language->get('button_approve');
			$data['button_add'] = $this->language->get('button_add');
			$data['button_edit'] = $this->language->get('button_edit');
			$data['button_delete'] = $this->language->get('button_delete');
			$data['button_filter'] = $this->language->get('button_filter');
			$data['button_unassign'] = $this->language->get('button_unassign');
			
			$data['user_token'] = $this->session->data['user_token'];
			
			if (isset($this->error['warning'])) {
				$data['error_warning'] = $this->error['warning'];
				} else {
				$data['error_warning'] = '';
			}
			
			if (isset($this->session->data['success'])) {
				$data['success'] = $this->session->data['success'];
				
				unset($this->session->data['success']);
				} else {
				$data['success'] = '';
			}
			if (isset($this->session->data['error_warning'])) {
				$data['error_warning'] = $this->session->data['error_warning'];
				
				unset($this->session->data['error_warning']);
				} else {
				$data['error_warning'] = '';
			}
			
			if (isset($this->request->post['selected'])) {
				$data['selected'] = (array)$this->request->post['selected'];
				} else {
				$data['selected'] = array();
			}
			
			$url = '';
			
			if (isset($this->request->get['filter_name'])) {
				$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
			}
			
			if (isset($this->request->get['filter_model'])) {
				$url .= '&filter_model=' . urlencode(html_entity_decode($this->request->get['filter_model'], ENT_QUOTES, 'UTF-8'));
			}
			
			if (isset($this->request->get['filter_price'])) {
				$url .= '&filter_price=' . $this->request->get['filter_price'];
			}
			
			if (isset($this->request->get['filter_quantity'])) {
				$url .= '&filter_quantity=' . $this->request->get['filter_quantity'];
			}
			
			if (isset($this->request->get['filter_status'])) {
				$url .= '&filter_status=' . $this->request->get['filter_status'];
			}
			
			if (isset($this->request->get['filter_image'])) {
				$url .= '&filter_image=' . $this->request->get['filter_image'];
			}
			
			if ($order == 'ASC') {
				$url .= '&order=DESC';
				} else {
				$url .= '&order=ASC';
			}
			
			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}
			
			
			
			$data['sort_name'] = $this->url->link('extension/purpletree_pos/pos/posproduct', 'user_token=' . $this->session->data['user_token'] . '&sort=pd.name' . $url, true);
			$data['sort_model'] = $this->url->link('extension/purpletree_pos/pos/posproduct', 'user_token=' . $this->session->data['user_token'] . '&sort=p.model' . $url, true);
			$data['sort_price'] = $this->url->link('extension/purpletree_pos/pos/posproduct', 'user_token=' . $this->session->data['user_token'] . '&sort=p.price' . $url, true);
			$data['sort_quantity'] = $this->url->link('extension/purpletree_pos/pos/posproduct', 'user_token=' . $this->session->data['user_token'] . '&sort=p.quantity' . $url, true);
			$data['sort_status'] = $this->url->link('extension/purpletree_pos/pos/posproduct', 'user_token=' . $this->session->data['user_token'] . '&sort=p.status' . $url, true);
			$data['sort_order'] = $this->url->link('extension/purpletree_pos/pos/posproduct', 'user_token=' . $this->session->data['user_token'] . '&sort=p.sort_order' . $url, true);
			$data['sort_box_id'] = $this->url->link('extension/purpletree_pos/pos/posproduct', 'user_token=' . $this->session->data['user_token'] . '&sort=p.box_id' . $url, true);
			
			$url = '';
			
			if (isset($this->request->get['filter_name'])) {
				$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
			}
			
			if (isset($this->request->get['filter_model'])) {
				$url .= '&filter_model=' . urlencode(html_entity_decode($this->request->get['filter_model'], ENT_QUOTES, 'UTF-8'));
			}
			
			if (isset($this->request->get['filter_price'])) {
				$url .= '&filter_price=' . $this->request->get['filter_price'];
			}
			
			if (isset($this->request->get['filter_quantity'])) {
				$url .= '&filter_quantity=' . $this->request->get['filter_quantity'];
			}
			
			if (isset($this->request->get['filter_status'])) {
				$url .= '&filter_status=' . $this->request->get['filter_status'];
			}
			
			if (isset($this->request->get['filter_image'])) {
				$url .= '&filter_image=' . $this->request->get['filter_image'];
			}
			
			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}
			
	    	if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}
			
			
			
			// $pagination = new Pagination();
			// $pagination->total = $product_total;
			// $pagination->page = $page;
			// $pagination->limit = $this->config->get('config_limit_admin');
			// $pagination->url = $this->url->link('extension/purpletree_pos/posproduct', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);
			
			// $data['pagination'] = $pagination->render();
			
			// $data['results'] = sprintf($this->language->get('text_pagination'), ($product_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($product_total - $this->config->get('config_limit_admin'))) ? $product_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $product_total, ceil($product_total / $this->config->get('config_limit_admin')));
			
			$data['pagination'] = $this->load->controller('common/pagination', [
			'total' => $product_total,
			'page'  => $page,
			'limit' => $this->config->get('config_pagination_admin'),
			'url'   => $this->url->link('extension/purpletree_pos/pos/posproduct', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true)
		]);
		$data['results'] = sprintf($this->language->get('text_pagination'), ($product_total) ? (($page - 1) * $this->config->get('config_pagination_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_pagination_admin')) > ($product_total - $this->config->get('config_pagination_admin'))) ? $product_total : ((($page - 1) * $this->config->get('config_pagination_admin')) + $this->config->get('config_pagination_admin')), $product_total, ceil($product_total / $this->config->get('config_pagination_admin')));
			
			$data['filter_name'] = $filter_name;
			$data['filter_model'] = $filter_model;
			$data['filter_rack_code'] = $filter_rack_code;
			$data['filter_box_id'] = $filter_box_id;
			$data['filter_price'] = $filter_price;
			$data['filter_quantity'] = $filter_quantity;
			$data['filter_status'] = $filter_status;
			$data['filter_tag'] = $filter_tag;
			$data['filter_approval'] = $filter_approval;
			$data['filter_image'] = $filter_image;
			$data['sort'] = $sort;
			$data['order'] = $order;
			$data['action_printbarcode'] = $this->url->link('extension/purpletree_pos/pos/posproduct|posPrintBarcode', 'user_token=' . $this->session->data['user_token'] . $url, true);
			$data['action_massprintbarcode'] = $this->url->link('extension/purpletree_pos/pos/posproduct|posMassPrintBarcode', 'user_token=' . $this->session->data['user_token'] . $url, true);
			
			$data['baseurl']=HTTP_CATALOG;
			
			$data['header'] = $this->load->controller('common/header');
			$data['column_left'] = $this->load->controller('common/column_left');
			$data['footer'] = $this->load->controller('common/footer');
			
			$this->response->setOutput($this->load->view('extension/purpletree_pos/posproduct_list', $data));
		}
		public function delete() {
		$this->load->language('catalog/product');

		$this->load->language('extension/purpletree_pos/posproduct');
		$this->load->model('extension/purpletree_pos/posproduct');
        // echo"<pre>"; print_r($this->request->post['selected']); die;
		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $product_id) {
				$this->model_extension_purpletree_pos_posproduct->deleteProduct($product_id);
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['filter_name'])) {
				$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_model'])) {
				$url .= '&filter_model=' . urlencode(html_entity_decode($this->request->get['filter_model'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_price'])) {
				$url .= '&filter_price=' . $this->request->get['filter_price'];
			}

			if (isset($this->request->get['filter_quantity'])) {
				$url .= '&filter_quantity=' . $this->request->get['filter_quantity'];
			}

			if (isset($this->request->get['filter_status'])) {
				$url .= '&filter_status=' . $this->request->get['filter_status'];
			}

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('extension/purpletree_pos/posproduct', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getList();
	}
	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'extension/purpletree_pos/posproduct')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
	
	public function posPrintBarcode(): void {

    ob_start();

    require_once(DIR_SYSTEM . 'library/tcpdf/tcpdf.php');

    $fitTextCell = function (
        $pdf,
        $x,
        $y,
        $w,
        $h,
        $text,
        $font = 'helvetica',
        $style = '',
        $maxSize = 6,
        $minSize = 4.5,
        $align = 'C'
    ) {
        for ($size = $maxSize; $size >= $minSize; $size -= 0.2) {
            $pdf->SetFont($font, $style, $size);
            if ($pdf->GetStringWidth($text) <= $w) {
                break;
            }
        }
        $pdf->SetXY($x, $y);
        $pdf->Cell($w, $h, $text, 0, 0, $align);
    };
    
               $this->load->model('extension/purpletree_pos/posproduct');
            
            $box_id = (int)($this->request->post['print_box_id'] ?? 0);
            
            $box_product_id = 0;
            $rack_code = '';
            
            if ($box_id > 0) {
            
                $box = $this->model_extension_purpletree_pos_posproduct->getproduct($box_id);
            
                if (!empty($box)) {
                    $box_product_id = (int)$box['product_id'];   // BOX NUMBER
                    $rack_code      = $box['rack_code'] ?? '';   // BOX RACK
                }
            }

            if ($box_product_id === 0) {
                $box_product_id = (int)($this->request->post['print_product_id'] ?? 0);
                $rack_code      = $this->request->post['print_rack_code'] ?? '';
            }



    $barcode   = $this->request->post['printbarcode'] ?? '';
    $name      = $this->request->post['printbarcodename'] ?? '';
    $sku_number = $this->request->post['printsku'] ?? '0';
    $price           = (float)($this->request->post['print_price'] ?? 0);
    $wholesale_price = (float)($this->request->post['print_wholesale'] ?? 0);
    $r_tag_raw       = $this->request->post['print_r_tag'] ?? 'R0';
    $w_tag_raw       = $this->request->post['print_w_tag'] ?? 'W0';
    
    $r_base = (int)preg_replace('/[^0-9]/', '', $r_tag_raw);
    $w_base = (int)preg_replace('/[^0-9]/', '', $w_tag_raw);
    
    $r_final = 'R' . ($r_base + $price);
    $w_final = 'W' . ($w_base + $wholesale_price);
    
    $priceTagText = $w_final . '-' . $r_final;

    $qty       = max(1, (int)($this->request->post['qty'] ?? 1));
    $maxQty    = max(1, (int)($this->request->post['printmaxqty'] ?? 1));

    $pdf = new \TCPDF('L', 'mm', [92, 12], true, 'UTF-8', false);
    $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(false);
    $pdf->SetMargins(1, 0.8, 1);
    $pdf->SetAutoPageBreak(false);

    $barcodeStyle = [
        'align'    => 'C',
        'border'   => false,
        'hpadding' => 0,
        'vpadding' => 0,
        'text'     => false,
        'font'     => 'helvetica',
        'fontsize' => 7
    ];


    for ($i = 0; $i < $qty; $i++) {

        $pdf->AddPage();
        $pdf->setCellPaddings(0, 0, 0, 0);

        $pdf->write1DBarcode(
            $barcode,
            'C128',
            4,     // X
            2.6,     // Y
            42,    // WIDTH
            7,     // HEIGHT
            0.25,  // THICKNESS
            $barcodeStyle,
            'N'
        );
        if (!empty($rack_code)) {
            $pdf->SetFont('helvetica', 'B', 4.2);
            $pdf->SetXY(10, 0.5);   // slightly above barcode
            $pdf->Cell(42, 2, $rack_code, 0, 0, 'L');
        }
        
        $pdf->SetFont('helvetica', 'B', 6);
        $pdf->setFontSpacing(1);   
        $pdf->SetXY(6, 9.6);
        $pdf->Cell(42, 2, $barcode, 0, 0, 'L');
        $pdf->setFontSpacing(0.2);      // reset spacing

        $current    = ($i % $maxQty) + 1;   // reset after max
        $sgcCenter = 'SGC-B' . $box_product_id;
        $sgcCorner  = $current . '/' . $maxQty;

        $textX = 18;
        $textW = 40;

        /* Row 1: SGC (center) + 1/4 (right) */
        $fitTextCell($pdf, $textX, 2.0, $textW, 2.6, $sgcCenter, 'helvetica', 'B', 6, 5, 'C');
        $fitTextCell($pdf, 9, 2.0, $textW, 2.6, $sgcCorner, 'helvetica', 'B', 6, 5, 'R');
        
        /* Row 2: Product Name */
        $fitTextCell($pdf, $textX, 4.8, $textW, 2.6, $name, 'helvetica', 'B', 6, 5, 'C');
        
        /* Row 3: SKU */
        $fitTextCell($pdf, $textX, 7.6, $textW, 2.6, $priceTagText, 'helvetica', 'B', 6, 5, 'C');

    }

    ob_end_clean();
    $pdf->Output('barcode.pdf', 'I');
    exit;
}




		public function autocomplete() { 
		    $this->load->model('extension/purpletree_pos/posproduct'); 
		    $json = array(); 
		    if (isset($this->request->get['filter_name']) || isset($this->request->get['filter_model']) || isset($this->request->get['filter_rack_code']) || isset($this->request->get['filter_box_id']) || isset($this->request->get['filter_barcode']) ) {
		        $this->load->model('catalog/product'); 
		        $this->load->model('catalog/option'); 
		        if (isset($this->request->get['filter_name'])) {
		            $filter_name = $this->request->get['filter_name']; 
		            
		        } else {
		            $filter_name = ''; 
		            
		        } if (isset($this->request->get['filter_model'])) {
		            $filter_model = $this->request->get['filter_model']; 
		            
		        } else {
		            $filter_model = ''; 
		            
		        } 
		        if (isset($this->request->get['filter_rack_code'])) {
		            $filter_rack_code = $this->request->get['filter_rack_code']; 
		            
		        } else {
		            $filter_rack_code = ''; 
		            
		        } 
		        if (isset($this->request->get['filter_box_id'])) {
		            $filter_box_id = $this->request->get['filter_box_id']; 
		            
		        } else {
		            $filter_box_id = ''; 
		            
		        } 
		        if (isset($this->request->get['filter_barcode'])) {
		            $filter_barcode = $this->request->get['filter_barcode']; 
		            
		        } else {
		            $filter_barcode = ''; 
		            
		        } 
		        if (isset($this->request->get['filter_price'])) {
		            $filter_price = $this->request->get['filter_price']; 
		            
		        } else {
		            $filter_price = ''; 
		            
		        } 
		        if (isset($this->request->get['filter_quantity'])) {
		            $filter_quantity = $this->request->get['filter_quantity']; 
		            
		        } else {
		            $filter_quantity = ''; 
		            
		        } if (isset($this->request->get['filter_status'])) {
		            $filter_status = $this->request->get['filter_status']; 
		            
		        } else { $filter_status = ''; 
		            
		        } if (isset($this->request->get['limit'])) {
		            $limit = $this->request->get['limit']; 
		            
		        } else {
		            $limit = 5;
		          }
		            $filter_data = array(
		                'filter_name' => $filter_name,
		                'filter_model' => $filter_model,
		                'filter_rack_code' => $filter_rack_code,
		                'filter_box_id' => $filter_box_id,
		                'filter_barcode' => $filter_barcode,
		                'filter_price' => $filter_price,
		                'filter_quantity' => $filter_quantity,
		                'filter_status' => $filter_status,
		                'start' => 0,
		                'limit' => $limit ); 
		                
		                $results = $this->model_extension_purpletree_pos_posproduct->getPosProducts($filter_data);
		                
		                foreach ($results as $result) {
		                    $option_data = array();
		                    $json[] = array(
		                        'product_id' => $result['product_id'], 
		                        'name' => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8')),
		                        'model' => $result['model'],
		                        'rack_code' => $result['rack_code'],
		                        'box_id' => $result['box_id'],
		                        'sku'        => $result['sku'],
                                'upc'        => $result['upc'],
		                        'price' => $result['price'] ); 
		                    
		                } 
		        
		    }

            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
    }

}
?>