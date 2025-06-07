# dcansoft-trendpres-v3.3
DCanSoft TrendPres v3.3 - WooCommerce Trendyol Ürün Entegrasyonu
https://assets/dcansoft-logo.png

📌 Özellikler
Trendyol Ürün Çekme: URL ile ürün bilgilerini otomatik alır

WooCommerce Entegrasyonu: Çekilen ürünleri otomatik oluşturur

Çift Yöntemli Sistem: API + HTML parsing desteği

Kullanıcı Dostu Arayüz: Basit admin paneli

🚀 Kurulum
ZIP dosyasını indirin:

bash
wget https://example.com/DCanSoft-TrendPres-v3.3.zip
WordPress'e yükleyin:

Admin Panel > Eklentiler > Yeni Ekle > ZIP Yükle

Gerekli ayarları yapın:

php
// wp-config.php'ye ekleyin
define('DCANSOFT_API_KEY', 'trendyol_api_anahtarınız');
🛠 Kullanım
WordPress admin panelinde TrendPres PRO menüsüne gidin

Trendyol ürün URL'sini girin

"Ürünü Çek" butonuna basın

https://assets/admin-panel-screenshot.png

⚙️ Ayarlar
Parametre	Açıklama	Varsayılan
api_key	Trendyol API anahtarı	Boş
auto_sync	Otomatik senkronizasyon	false
price_margin	Fiyat marjı (%)	10
📝 Gereksinimler
WordPress 5.6+

WooCommerce 5.0+

PHP 7.4+

cURL etkin olmalı

🔍 Bilinen Sorunlar
Trendyol API limitleri (günlük 100 istek)

CAPTCHA engeli (HTML parsing yönteminde)

🤝 Katkıda Bulunma
Repoyu fork edin:

bash
git clone https://github.com/dcansoft/trendpres-v3.git
Yeni branch oluşturun:

bash
git checkout -b yeni-ozellik
Değişiklikleri gönderin

📜 Lisans
GNU General Public License v3.0

📞 Destek
Email: destek@dcansoft.com

Website: https://dcansoft.com

https://assets/integration-diagram.png

Not: Bu eklenti Trendyol'un resmi API'sı olmadan çalışır, ancak performans için API anahtarı önerilir.
