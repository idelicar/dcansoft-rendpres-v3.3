<?php
/*
Plugin Name: DCanSoft TrendPres v4.0
Description: Trendyol'dan profesyonel ürün çekme ve yönetim sistemi
Version: 4.0
Author: DCanSoft
*/

defined('ABSPATH') || exit;

class DCanSoft_TrendPres_Ultimate {
    private $api_key;
    
    public function __construct() {
        $this->api_key = get_option('dcansoft_api_key');
        
        add_action('admin_menu', [$this, 'create_admin_interface']);
        add_action('admin_enqueue_scripts', [$this, 'load_admin_assets']);
        add_action('wp_ajax_dcansoft_import', [$this, 'ajax_handler']);
    }

    public function create_admin_interface() {
        add_menu_page(
            'TrendPres Ultimate',
            'TrendPres v4',
            'manage_options',
            'dcansoft-ultimate',
            [$this, 'render_dashboard'],
            'dashicons-tag',
            6
        );
        
        add_submenu_page(
            'dcansoft-ultimate',
            'API Ayarları',
            'API Ayarları',
            'manage_options',
            'dcansoft-api-settings',
            [$this, 'render_api_settings']
        );
    }

    public function load_admin_assets($hook) {
        if(strpos($hook, 'dcansoft-ultimate') !== false) {
            wp_enqueue_style('dcansoft-admin-css', plugins_url('assets/css/admin.css', __FILE__));
            wp_enqueue_script('dcansoft-admin-js', plugins_url('assets/js/admin.js', __FILE__), ['jquery'], null, true);
            
            wp_localize_script('dcansoft-admin-js', 'dcansoft', [
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('dcansoft-nonce')
            ]);
        }
    }

    public function render_dashboard() {
        ?>
        <div class="dcansoft-wrapper">
            <header class="dcansoft-header">
                <img src="<?php echo plugins_url('assets/img/logo.png', __FILE__); ?>" width="200">
                <div class="dcansoft-stats">
                    <div class="stat-card">
                        <h3>Toplam Ürün</h3>
                        <span><?php echo $this->get_product_count(); ?></span>
                    </div>
                    <div class="stat-card">
                        <h3>Bugün Eklenen</h3>
                        <span><?php echo $this->get_todays_products(); ?></span>
                    </div>
                </div>
            </header>
            
            <div class="dcansoft-main">
                <div class="import-section">
                    <h2><span class="dashicons dashicons-download"></span> Ürün İçe Aktar</h2>
                    <div class="import-form">
                        <input type="url" id="product-url" placeholder="Trendyol Ürün URL">
                        <button id="import-btn" class="dcansoft-btn">
                            <span class="dashicons dashicons-cloud-download"></span> Ürünü Çek
                        </button>
                    </div>
                    <div class="progress-bar">
                        <div class="progress-fill"></div>
                    </div>
                </div>
                
                <div class="product-preview">
                    <h2><span class="dashicons dashicons-visibility"></span> Önizleme</h2>
                    <div class="preview-container"></div>
                </div>
                
                <div class="log-section">
                    <h2><span class="dashicons dashicons-media-text"></span> İşlem Geçmişi</h2>
                    <div class="log-container"></div>
                </div>
            </div>
        </div>
        <?php
    }

    public function ajax_handler() {
        check_ajax_referer('dcansoft-nonce', 'security');
        
        $action = sanitize_text_field($_POST['action_type']);
        
        switch($action) {
            case 'import_product':
                $this->import_product();
                break;
                
            case 'get_stats':
                wp_send_json_success([
                    'total' => $this->get_product_count(),
                    'today' => $this->get_todays_products()
                ]);
                break;
        }
    }
    
    private function import_product() {
        $url = esc_url_raw($_POST['product_url']);
        
        try {
            $product_data = $this->fetch_product_data($url);
            $product_id = $this->create_product($product_data);
            
            wp_send_json_success([
                'message' => 'Ürün başarıyla eklendi!',
                'product_id' => $product_id,
                'edit_link' => admin_url("post.php?post=$product_id&action=edit"),
                'preview' => $this->generate_preview_html($product_data)
            ]);
            
        } catch(Exception $e) {
            wp_send_json_error([
                'message' => 'Hata: ' . $e->getMessage()
            ]);
        }
    }
    
    private function fetch_product_data($url) {
        // Gelişmiş API entegrasyonu
        $response = wp_remote_get("https://api.trendyol.com/v1/products?url=".urlencode($url), [
            'headers' => [
                'Authorization' => 'Bearer '.$this->api_key,
                'Accept' => 'application/json'
            ],
            'timeout' => 30
        ]);
        
        if(is_wp_error($response)) {
            throw new Exception("API bağlantı hatası");
        }
        
        $data = json_decode($response['body'], true);
        
        return [
            'name' => $data['product']['name'],
            'price' => $data['product']['price']['sellingPrice'],
            'images' => $data['product']['images'],
            'attributes' => $data['product']['attributes']
        ];
    }
    
    private function create_product($data) {
        $product = new WC_Product_Simple();
        $product->set_name($data['name']);
        $product->set_regular_price($data['price']);
        $product->set_status('publish');
        
        if(!empty($data['images'])) {
            $image_id = $this->upload_image($data['images'][0]);
            $product->set_image_id($image_id);
        }
        
        return $product->save();
    }
    
    private function upload_image($url) {
        require_once(ABSPATH . 'wp-admin/includes/image.php');
        $tmp_file = download_url($url);
        
        $file_array = [
            'name' => basename($url),
            'tmp_name' => $tmp_file
        ];
        
        return media_handle_sideload($file_array, 0);
    }
    
    private function generate_preview_html($data) {
        ob_start(); ?>
        <div class="product-preview-card">
            <h3><?php echo esc_html($data['name']); ?></h3>
            <div class="product-price"><?php echo wc_price($data['price']); ?></div>
            <div class="product-attributes">
                <?php foreach($data['attributes'] as $attr): ?>
                <div class="attribute">
                    <strong><?php echo $attr['name']; ?>:</strong>
                    <span><?php echo $attr['value']; ?></span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
    
    private function get_product_count() {
        global $wpdb;
        return $wpdb->get_var("
            SELECT COUNT(*) FROM {$wpdb->posts} 
            WHERE post_type = 'product' 
            AND post_status = 'publish'
        ");
    }
    
    private function get_todays_products() {
        global $wpdb;
        return $wpdb->get_var("
            SELECT COUNT(*) FROM {$wpdb->posts} 
            WHERE post_type = 'product' 
            AND post_date >= CURDATE()
        ");
    }
}

new DCanSoft_TrendPres_Ultimate();