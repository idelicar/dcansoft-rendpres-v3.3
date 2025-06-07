<?php
/*
Plugin Name: DCanSoft TrendPres v3.3
Description: Trendyol ürünlerini gerçek zamanlı WooCommerce'e aktarır
Version: 3.3
Author: DCanSoft
*/

defined('ABSPATH') || exit;

class DCanSoft_TrendPres_Pro {
    private $api_key = 'DCANSOFT_API_KEY'; // Kendi API anahtarınızı ekleyin

    public function __construct() {
        add_action('admin_menu', [$this, 'add_admin_menu']);
        add_action('wp_ajax_dcansoft_fetch_product', [$this, 'fetch_product']);
    }

    public function add_admin_menu() {
        add_menu_page(
            'Trendyol Ürün Aktarım',
            'TrendPres PRO',
            'manage_options',
            'dcansoft-trendpres-pro',
            [$this, 'admin_interface'],
            'dashicons-cart',
            56
        );
    }

    public function admin_interface() {
        ?>
        <div class="wrap">
            <h1>DCanSoft TrendPres PRO v3.3</h1>
            <div class="card">
                <h2>Ürün Çekme Arayüzü</h2>
                <input type="url" id="trendyol-url" placeholder="Trendyol Ürün URL" class="regular-text">
                <button id="fetch-btn" class="button button-primary">Ürünü Çek</button>
                <div id="product-result"></div>
            </div>
        </div>

        <script>
        jQuery(document).ready(function($) {
            $('#fetch-btn').click(function() {
                $('#product-result').html('<p class="loading">Ürün çekiliyor...</p>');
                
                $.post(ajaxurl, {
                    action: 'dcansoft_fetch_product',
                    url: $('#trendyol-url').val()
                }, function(response) {
                    $('#product-result').html(response.data.message);
                }).fail(function() {
                    $('#product-result').html('<p class="error">Hata oluştu!</p>');
                });
            });
        });
        </script>
        <?php
    }

    public function fetch_product() {
        $url = esc_url_raw($_POST['url']);
        
        // Trendyol API veya scraping işlemi
        $product_data = $this->get_trendyol_product($url);
        
        if($product_data) {
            $product_id = $this->create_product($product_data);
            wp_send_json_success([
                'message' => sprintf('Ürün başarıyla eklendi! <a href="%s">Ürünü Görüntüle</a>', 
                    admin_url('post.php?post='.$product_id.'&action=edit'))
            ]);
        } else {
            wp_send_json_error(['message' => 'Ürün çekilemedi!']);
        }
    }

    private function get_trendyol_product($url) {
        // 1. Trendyol API Entegrasyonu
        $api_url = 'https://api.trendyol.com/sapigw/product-detail?url='.urlencode($url);
        
        $response = wp_remote_get($api_url, [
            'headers' => [
                'Authorization' => 'Bearer '.$this->api_key,
                'Content-Type' => 'application/json'
            ],
            'timeout' => 30
        ]);

        // 2. Fallback: Manuel Parsing
        if(is_wp_error($response)) {
            $html = file_get_contents($url);
            preg_match('/<h1.*?>(.*?)<\/h1>/', $html, $title);
            preg_match('/"price":"(.*?)"/', $html, $price);
            
            return [
                'name' => $title[1] ?? 'Ürün Adı',
                'price' => $price[1] ?? 0,
                'description' => 'Trendyol ürün açıklaması'
            ];
        }

        return json_decode($response['body'], true);
    }

    private function create_product($data) {
        $product = new WC_Product_Simple();
        $product->set_name($data['name']);
        $product->set_regular_price($data['price']);
        $product->set_description($data['description']);
        return $product->save();
    }
}

new DCanSoft_TrendPres_Pro();