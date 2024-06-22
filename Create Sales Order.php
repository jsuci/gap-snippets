// soap api calls
function create_erp_user($current_order, $apikey, $url) {
    if (!$current_order) {
        return;
    }

    // Create User API call
    $soap_request = '<?xml version="1.0" encoding="utf-8"?>
     <soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
         <soap:Header>
             <KlozHeader xmlns="http://klozinc.exocloud.ca/">
                 <apikey>'.$apikey.'</apikey>
             </KlozHeader>
         </soap:Header>
         <soap:Body>
             <createUser xmlns="http://klozinc.exocloud.ca/">
                 <name>'.$current_order['name'].'</name>
                 <address>'.$current_order['address'].'</address>
                 <city>'.$current_order['city'].'</city>
                 <state>'.$current_order['state'].'</state>
                 <phone>'.$current_order['phone'].'</phone>
                 <mobile>'.$current_order['mobile'].'</mobile>
                 <email>'.$current_order['email'].'</email>
                 <country>'.$current_order['country'].'</country>
                 <fax>'.$current_order['fax'].'</fax>
                 <address2>'.$current_order['address2'].'</address2>
                 <postalcode>'.$current_order['postalcode'].'</postalcode>
                 <currencyid>'.$current_order['currencyid'].'</currencyid>
				 <customerGroupId>'.$current_order['customerGroupId'].'</customerGroupId>
				 <taxId>'.$current_order['taxId'].'</taxId>
             </createUser>
         </soap:Body>
     </soap:Envelope>';

     $curl = curl_init();

     curl_setopt_array($curl, array(
         CURLOPT_URL => $url,
         CURLOPT_RETURNTRANSFER => true,
         CURLOPT_ENCODING => '',
         CURLOPT_MAXREDIRS => 10,
         CURLOPT_TIMEOUT => 0,
         CURLOPT_FOLLOWLOCATION => true,
         CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
         CURLOPT_CUSTOMREQUEST => 'POST',
         CURLOPT_POSTFIELDS => $soap_request,
         CURLOPT_HTTPHEADER => array(
             'Content-Type: text/xml; charset=utf-8',
             'SOAPAction: "http://klozinc.exocloud.ca/createUser"'
         ),
     ));

     $response = curl_exec($curl);

     curl_close($curl);

     // Load the XML response
     $xml = simplexml_load_string($response);

     // Register the soap namespace
     $xml->registerXPathNamespace('soap', 'http://schemas.xmlsoap.org/soap/envelope/');
     $xml->registerXPathNamespace('ns', 'http://klozinc.exocloud.ca/');

     $id = $xml->xpath('//ns:createUserResult');
     $createUserResultValue = (string)$id[0];
	 $data = array(
		'erpid' => $createUserResultValue,
		'name' => $current_order['name'],
		'address' => $current_order['address'],
		'city' => $current_order['city'],
		'state' => $current_order['state'],
		'phone' => $current_order['phone'],
		'mobile' => $current_order['mobile'],
		'email' => $current_order['email'],
		'country' => $current_order['country'],
		'fax' => $current_order['fax'],
		'address2' => $current_order['address2'],
		'postalcode' => $current_order['postalcode'],
		'currencyid' => $current_order['currencyid'],
		'customerGroupId' => $current_order['customerGroupId'],
		'taxId' => $current_order['taxId']
	 );
	
	 insert_customer_erp_data($data);
}

function create_erp_sales_order($current_order, $apikey, $url) {
    if (!$current_order) {
        return;
    }

    $so_items = array();

    foreach ($current_order['items'] as $index => $item) {
        $so_items[$index] = "
            <SOItems>
              <itemid>" . htmlspecialchars($item['itemid'], ENT_QUOTES, 'UTF-8') . "</itemid>
              <itemcode>" . htmlspecialchars($item['itemcode'], ENT_QUOTES, 'UTF-8') . "</itemcode>
              <itemDescription>" . htmlspecialchars($item['itemDescription'], ENT_QUOTES, 'UTF-8') . "</itemDescription>
              <itemColorid>" . htmlspecialchars($item['itemColorid'], ENT_QUOTES, 'UTF-8') . "</itemColorid>
              <itemSizeid>" . htmlspecialchars($item['itemSizeid'], ENT_QUOTES, 'UTF-8') . "</itemSizeid>
              <itemQuantity>" . htmlspecialchars($item['itemQuantity'], ENT_QUOTES, 'UTF-8') . "</itemQuantity>
              <Price>" . htmlspecialchars($item['Price'], ENT_QUOTES, 'UTF-8') . "</Price>
              <DiscountPercentage>" . htmlspecialchars($item['DiscountPercentage'], ENT_QUOTES, 'UTF-8') . "</DiscountPercentage>
              <DiscountAmount>" . htmlspecialchars($item['DiscountAmount'], ENT_QUOTES, 'UTF-8') . "</DiscountAmount>
              <NetValue>" . htmlspecialchars($item['NetValue'], ENT_QUOTES, 'UTF-8') . "</NetValue>
            </SOItems>
        ";
    }

    $soap_request = '<?xml version="1.0" encoding="utf-8"?>
    <soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
        <soap:Header>
            <KlozHeader xmlns="http://klozinc.exocloud.ca/">
                <apikey>' . htmlspecialchars($apikey, ENT_QUOTES, 'UTF-8') . '</apikey>
            </KlozHeader>
        </soap:Header>
        <soap:Body>
            <createSO xmlns="http://klozinc.exocloud.ca/">
                <customerid>' . htmlspecialchars($current_order['customerid'], ENT_QUOTES, 'UTF-8') . '</customerid>
                <orderbasicamount>' . htmlspecialchars($current_order['orderbasicamount'], ENT_QUOTES, 'UTF-8') . '</orderbasicamount>
                <taxamount>' . htmlspecialchars($current_order['taxamount'], ENT_QUOTES, 'UTF-8') . '</taxamount>
                <discount>' . htmlspecialchars($current_order['discount'], ENT_QUOTES, 'UTF-8') . '</discount>
                <totalwithouttax>' . htmlspecialchars($current_order['totalwithouttax'], ENT_QUOTES, 'UTF-8') . '</totalwithouttax>
                <grandtotal>' . htmlspecialchars($current_order['grandtotal'], ENT_QUOTES, 'UTF-8') . '</grandtotal>
                <orderDate>' . htmlspecialchars($current_order['orderDate'], ENT_QUOTES, 'UTF-8') . '</orderDate>
                <terms>' . htmlspecialchars($current_order['terms'], ENT_QUOTES, 'UTF-8') . '</terms>
                <itemarray>
                    ' . implode("\n", $so_items) . '
                </itemarray>
                <invoice>' . htmlspecialchars($current_order['invoice'], ENT_QUOTES, 'UTF-8') . '</invoice>
                <otherdiscount>' . htmlspecialchars($current_order['otherdiscount'], ENT_QUOTES, 'UTF-8') . '</otherdiscount>
                <customerpo>' . htmlspecialchars($current_order['customerpo'], ENT_QUOTES, 'UTF-8') . '</customerpo>
                <localShippingCharge>' . htmlspecialchars($current_order['localShippingCharge'], ENT_QUOTES, 'UTF-8') . '</localShippingCharge>
                <overseasShippingCharge>' . htmlspecialchars($current_order['overseasShippingCharge'], ENT_QUOTES, 'UTF-8') . '</overseasShippingCharge>
            </createSO>
        </soap:Body>
    </soap:Envelope>';

    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => $soap_request,
        CURLOPT_HTTPHEADER => array(
            'Host: sandbox.klozinc.exocloud.ca',
            'Content-Type: text/xml; charset=utf-8',
            'SOAPAction: http://klozinc.exocloud.ca/createSO'
        ),
    ));

    $response = curl_exec($curl);

    if (curl_errno($curl)) {
        $error_msg = curl_error($curl);
        curl_close($curl);
        return array(
            'error' => $error_msg,
        );
    }

    curl_close($curl);

    // Load the XML response
    $xml = simplexml_load_string($response);

    // Register the soap namespace
    $xml->registerXPathNamespace('soap', 'http://schemas.xmlsoap.org/soap/envelope/');
    $xml->registerXPathNamespace('ns', 'http://klozinc.exocloud.ca/');

    // Extract the required fields
    $billno = (string)$xml->xpath('//ns:createSOResult/ns:billno')[0];
    $status = (string)$xml->xpath('//ns:createSOResult/ns:status')[0];

    return array(
        'billno' => $billno,
        'status' => $status,
    );
}

function get_item_by_itemcode($itemcode, $apikey, $url) {
    if (!$itemcode) {
        return;
    }

    // Create User API call
	$curl = curl_init();

	curl_setopt_array($curl, array(
	  CURLOPT_URL => $url,
	  CURLOPT_RETURNTRANSFER => true,
	  CURLOPT_ENCODING => '',
	  CURLOPT_MAXREDIRS => 10,
	  CURLOPT_TIMEOUT => 0,
	  CURLOPT_FOLLOWLOCATION => true,
	  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	  CURLOPT_CUSTOMREQUEST => 'POST',
	  CURLOPT_POSTFIELDS =>'<?xml version="1.0" encoding="utf-8"?>
	<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
	  <soap:Header>
		<KlozHeader xmlns="http://klozinc.exocloud.ca/">
		  <apikey>'.$apikey.'</apikey>
		</KlozHeader>
	  </soap:Header>
	  <soap:Body>
		<GetItemByItemCode xmlns="http://klozinc.exocloud.ca/">
		  <itemCode>'.$itemcode.'</itemCode>
		</GetItemByItemCode>
	  </soap:Body>
	</soap:Envelope>',
	  CURLOPT_HTTPHEADER => array(
		'Host: sandbox.klozinc.exocloud.ca',
		'Content-Type: text/xml; charset=utf-8',
		'SOAPAction: http://klozinc.exocloud.ca/GetItemByItemCode'
	  ),
	));

	$response = curl_exec($curl);

	curl_close($curl);

    // Load the XML response
    $xml = simplexml_load_string($response);

    // Register the soap namespace
    $xml->registerXPathNamespace('soap', 'http://schemas.xmlsoap.org/soap/envelope/');
    $xml->registerXPathNamespace('ns', 'http://klozinc.exocloud.ca/');

    // Extract the required fields
    $id = (string)$xml->xpath('//ns:GetItemByItemCodeResult/ns:Item/ns:id')[0];
    $itemCode = (string)$xml->xpath('//ns:GetItemByItemCodeResult/ns:Item/ns:Itemcode')[0];
    $itemDesp = (string)$xml->xpath('//ns:GetItemByItemCodeResult/ns:Item/ns:ItemDesp')[0];
	$itemName = (string)$xml->xpath('//ns:GetItemByItemCodeResult/ns:Item/ns:ItemName')[0];

    return array(
        'itemid' => $id,
        'itemcode' => $itemCode,
        'itemDescription' => $itemDesp,
		'itemName' => $itemName
    );
}



// customer erp table
function create_customer_erp_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'customer_erp';
    
    // Check if the table already exists
    if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
        $charset_collate = $wpdb->get_charset_collate();
        
        $sql = "CREATE TABLE $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
			erpid mediumint(9) NOT NULL,
            name varchar(255) NOT NULL,
            address varchar(255) NOT NULL,
            city varchar(100) NOT NULL,
            state varchar(100) NOT NULL,
            phone varchar(50) NOT NULL,
            mobile varchar(50) DEFAULT NULL,
            email varchar(100) NOT NULL,
            country varchar(100) NOT NULL,
            fax varchar(50) DEFAULT NULL,
            address2 varchar(255) DEFAULT NULL,
            postalcode varchar(20) NOT NULL,
            currencyid int NOT NULL,
            customerGroupId int NOT NULL,
            taxId int NOT NULL,
            PRIMARY KEY (id)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
}

function insert_customer_erp_data($data) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'customer_erp';
    
    $data = array_map('strtoupper', $data);
    
    $wpdb->insert(
        $table_name,
        array(
			'erpid' => $data['erpid'],
            'name' => $data['name'],
            'address' => $data['address'],
            'city' => $data['city'],
            'state' => $data['state'],
            'phone' => $data['phone'],
            'mobile' => $data['mobile'],
            'email' => $data['email'],
            'country' => $data['country'],
            'fax' => $data['fax'],
            'address2' => $data['address2'],
            'postalcode' => $data['postalcode'],
            'currencyid' => $data['currencyid'],
            'customerGroupId' => $data['customerGroupId'],
            'taxId' => $data['taxId']
        )
    );
}

function select_customer_erp($name, $email) {
    global $wpdb;

    $table_name = $wpdb->prefix . 'customer_erp';

    $results = $wpdb->get_results( "SELECT * FROM $table_name WHERE name LIKE '$name' AND email LIKE '$email';" );

	if(!$results) {
		return null;
	}
	return $results[0];
}


// sales order erp table
function create_sales_order_erp_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'sales_order_erp';
    
    // Check if the table already exists
    if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
        $charset_collate = $wpdb->get_charset_collate();
        
        $sql = "CREATE TABLE $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
			erpid mediumint(9) NOT NULL,
            orderid varchar(255) NOT NULL,
            billno varchar(255) DEFAULT NULL,
            PRIMARY KEY (id)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
}

function insert_sales_order_erp_data($erpid, $orderid) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'sales_order_erp';
    
    $wpdb->insert(
        $table_name,
        array(
			'erpid' => $erpid,
            'orderid' => $orderid,
        )
    );
}

function select_sales_order_erp($erpid, $orderid) {
    global $wpdb;

    $table_name = $wpdb->prefix . 'sales_order_erp';

    $results = $wpdb->get_results( "SELECT * FROM $table_name WHERE erpid = $erpid AND orderid = $orderid;" );

	if(!$results) {
		return null;
	}
	
	return $results[0];
}

function update_sales_order_erp($erpid, $orderid, $billno) {
    global $wpdb;

    $table_name = $wpdb->prefix . 'sales_order_erp';

    $wpdb->update(
        $table_name,
        array(
            'billno' => $billno
        ),
        array(
			'erpid' => $erpid,
			'orderid' => $orderid
		)
    );
}



// helper functions
function get_currency_id($currency) {

    $currency_mapping = array(
        'CAD' => 2,
        'USD' => 1
    );

    return isset($currency_mapping[$currency]) ? $currency_mapping[$currency] : null;
}

function get_country_name($country) {
   
    $country = strtolower($country);

   
    if ($country === 'ca' || $country === 'canada') {
        return 'CANADA';
    } elseif ($country === 'united states' || $country === 'usa') {
        return 'USA';
    }

    return null;
}

function get_tax_id($country, $state) {
    $country = strtoupper($country);
    $state = strtoupper($state);

    $canadian_provinces = array(
        'AB' => 'Alberta',
        'BC' => 'British Columbia',
        'MB' => 'Manitoba',
        'NT' => 'Northwest Territories',
        'NU' => 'Nunavut',
        'QC' => 'Quebec',
        'SK' => 'Saskatchewan',
        'YT' => 'Yukon',
        'ON' => 'Ontario',
        'NB' => 'New Brunswick',
        'NL' => 'Newfoundland and Labrador',
        'NS' => 'Nova Scotia',
        'PE' => 'Prince Edward Island'
    );

    // Check the country and state to determine the tax ID
    if ($country === 'USA') {
        return 1;
    } elseif ($country === 'CANADA') {
        if (in_array($state, array('AB', 'BC', 'MB', 'NT', 'NU', 'QC', 'SK', 'YT'))) {
            return 12;
        } elseif ($state === 'ON') {
            return 8;
        } elseif (in_array($state, array('NB', 'NL', 'NS', 'PE'))) {
            return 17;
        }
    }

    // Return null if no conditions are met
    return null;
}

function get_wc_order_details($order_id) {
	$order = wc_get_order($order_id);
	$order_data = $order->get_data();
	$order_id = $order_data['id'];
	$order_currency = $order_data['currency'];
	$order_billing_first_name = $order_data['billing']['first_name'];
	$order_billing_last_name = $order_data['billing']['last_name'];
	$order_billing_full_name = $order_data['billing']['first_name'] . ' ' . $order_data['billing']['last_name'];
	$order_billing_address_1 = $order_data['billing']['address_1'];
	$order_billing_address_2 = $order_data['billing']['address_2'];
	$order_billing_city = $order_data['billing']['city'];
	$order_billing_state = $order_data['billing']['state'];
	$order_billing_postcode = $order_data['billing']['postcode'];
	$order_billing_country = $order_data['billing']['country'];
	$order_billing_email = $order_data['billing']['email'];
	$order_billing_phone = $order_data['billing']['phone'];
	
	$order_country = get_country_name($order_billing_country);
	$order_tax_id = get_tax_id($order_country, $order_billing_state);
	$order_currency_id = get_currency_id($order_currency);
	$order_total = $order_data['total'];
	$order_date_created = $order_data['date_created']->date('F d, Y');
	$order_items = $order->get_items();
	$items = array();
	
	$order_shipping_total = $order_data['shipping_total'];

	if ($order_country === 'CANADA') {
		$localShippingCharge = $order_shipping_total;
		$overseasShippingCharge = 0.00;
	} else {
		$localShippingCharge = 0.00;
		$overseasShippingCharge = $order_shipping_total;
	}
	
	
	foreach ($order_items as $index => $item) {
		$item_data = $item->get_data();
		$product = $item->get_product();
		$product_data = $product->get_data();
		$is_bundled = $product->is_type('bundle');
		$product_variation_id = $item['variation_id'];
		
		if(!$is_bundled) {
			$item_quantity = $item_data['quantity'];
			$item_sub_total = $item_data['subtotal'];
			$item_total = $item_data['total'];
			$item_price =  number_format((float)($item_sub_total / $item_quantity), 2, '.', '');
			$item_discount_percentage = round((($item_sub_total - $item_total) / $item_sub_total) * 100);
			$item_discount_amount = round(($item_sub_total * ($item_discount_percentage / 100)), 2);
			$item_net_value = $item_total;
			$product_sku = $product_data['sku'];
			
			if ($product_variation_id) {
				$variation = wc_get_product($item['variation_id']);
				$variation_attributes = $variation->get_variation_attributes();
				$variation_sku = (!$variation->get_sku()) ? $product_sku : $variation->get_sku();
				
				$erp_item = get_item_by_itemcode($variation_sku, 'Test@apikloz', 'http://sandbox.klozinc.exocloud.ca/api/exowebservice.asmx');

				$items[$index] = array(
					'itemid' => $erp_item['itemid'],
					'itemcode' => $variation_sku,
					'itemDescription' => $erp_item['itemName'],
					'itemColorid' => '',
					'itemSizeid' => '',
					'itemQuantity' => $item_quantity,
					'Price' => $item_price,
					'DiscountPercentage' => $item_discount_percentage,
					'DiscountAmount' => $item_discount_amount,
					'NetValue' => $item_net_value,
				);
			} else {
				$erp_item = get_item_by_itemcode($product_sku, 'Test@apikloz', 'http://sandbox.klozinc.exocloud.ca/api/exowebservice.asmx');
				$items[$index] = array(
					'itemid' => $erp_item['itemid'],
					'itemcode' => $product_sku,
					'itemDescription' => $erp_item['itemName'],
					'itemColorid' => '',
					'itemSizeid' => '',
					'itemQuantity' => $item_quantity,
					'Price' => $item_price,
					'DiscountPercentage' => $item_discount_percentage,
					'DiscountAmount' => $item_discount_amount,
					'NetValue' => $item_net_value,
				);
			}
		}
	}

	$data = array(
		'name' => $order_billing_full_name,
		'address' => $order_billing_address_1,
		'city' => $order_billing_city,
		'state' => $order_billing_state,
		'phone' => $order_billing_phone,
		'mobile' => '',
		'email' => $order_billing_email,
		'country' => $order_country,
		'fax' => '',
		'address2' => $order_billing_address_2,
		'postalcode' => $order_billing_postcode,
		'currencyid' => $order_currency_id,
		'customerGroupId' => 24,
		'taxId' => $order_tax_id,
		'orderbasicamount' => $order_total,
		'taxamount' => 0.00,
		'discount' => 0.00,
		'totalwithouttax' => 0.00,
		'grandtotal' => 0.00,
		'orderDate' => $order_date_created,
		'terms' => '',
		'items' => $items,
		'invoice' => 'GOALIGNPILATES',
		'otherdiscount' => 0.00,
		'customerpo' => $order_id,
		'localShippingCharge' => $localShippingCharge,
		'overseasShippingCharge' => $overseasShippingCharge
	);
	
	$uppercased_data = array_map(function($value) {
		if (is_array($value)) {
			return $value;
		} else {
			return strtoupper($value);
		}
	}, $data);

	return $uppercased_data;
}




// main execution
function main($order_id) {
	$current_order = get_wc_order_details($order_id);
	$customer_erp = select_customer_erp($current_order['name'], $current_order['email']);

	if (!$customer_erp) {
		create_erp_user($current_order, 'Test@apikloz', 'http://sandbox.klozinc.exocloud.ca/api/exowebservice.asmx');
		$customer_erp = select_customer_erp($current_order['name'], $current_order['email']);
		$current_order['customerid'] = $customer_erp->erpid;
	} else {
		$current_order['customerid'] = $customer_erp->erpid;
	}
		
	$current_sales_order = select_sales_order_erp($current_order['customerid'], $order_id);
	
	if (!$current_sales_order) {
		insert_sales_order_erp_data($current_order['customerid'], $order_id);
		$createso_result = create_erp_sales_order($current_order, 'Test@apikloz', 'http://sandbox.klozinc.exocloud.ca/api/exowebservice.asmx');
		update_sales_order_erp($current_order['customerid'], $order_id, $createso_result['billno']);
	}
}



add_action('wp_loaded', 'create_customer_erp_table');
add_action('wp_loaded', 'create_sales_order_erp_table');
add_action('woocommerce_before_thankyou', 'main');