<?php
	include_once 'models/Product.php';
	include_once 'models/Customer.php';
	class ProductController {
		var $product;
		var $customer;
		function __construct()
		{
			$this->product = new Product();
			$this->customer = new Customer();
		}
		function findByCode() {
			$code = $_GET['code'];
			echo json_encode($this->product->findByCode($code));
		}
		function findDetailByCode() {
			$code = $_GET['code'];
			echo json_encode($this->product->findDetailByCode($code));
		}
		function page_home() {
			require_once 'views/index.php';
		}
		function page_404() {
			require_once 'views/page-404.php';
		}
		function page_shop() {
			$authors = $this->product->getAuthor();
			$types = $this->product->getType();
			$publishers = $this->product->getPublisher();
			$query = array();
			if(isset($_GET['author'])) {
				array_push($query, array(
					"name" => "author",
					"code" => $_GET['author']
				));
			}
			if(isset($_GET['publisher'])) {
				array_push($query, array(
					"name" => "publisher",
					"code" => $_GET['publisher']
				));
			}
			if(isset($_GET['type'])) {
				array_push($query, array(
					"name" => "type",
					"code" => $_GET['type']
				));
			}
			if(isset($_GET['price'])) {
				$prices = explode("-", $_GET['price']);
				array_push($query, array(
					"name" => "price",
					"from" => $prices[0],
					"to" => $prices[1]
				));
			}
			$page = isset($_GET['page']) ? $_GET['page'] : 1;
			$sort = isset($_GET['sort']) ? $_GET['sort'] : 'id';
			$products = $this->product->search($query, $page, $sort);
			$total = $this->product->count($query);
			$productNew = $this->product->findNew();
			require_once 'views/listing.php';
		}
		function page_cart() {
			require_once 'views/shopping_cart.php';
		}
		function page_empty_cart() {
			require_once 'views/empty-cart.php';
		}
		function page_detail() {
			$code = $_GET['code'];
			$product = $this->product->findDetailByCode($code);
			$productRecommend = $this->product->findRecommend($product['type_id']);
			require_once 'views/product.php';
		}
		function page_checkout() {
			$shipInfo = $this->customer->getShipInfo($_SESSION['customer']['id']);
			require_once 'views/checkout-one-page.php';
		}
		function page_account() {
			$orders = $this->customer->getOrders($_SESSION['customer']['id']);
			require_once 'views/account-order.php';
		}
		function page_login() {
			require_once 'views/login_form.php';
		}
	}
?>